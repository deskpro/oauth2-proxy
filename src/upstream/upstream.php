<?php

namespace Oauth2;

function urlsafeB64Encode($input) : string
{
    return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
}


function createSignature($timestamp): string {

    $msg = implode("\n", [
        $_SERVER['HTTP_X_FORWARDED_USER'],
        $_SERVER['HTTP_X_FORWARDED_EMAIL'],
        $timestamp,
    ]);

    $algorithm = "sha256";
    $signature = hash_hmac($algorithm, $msg, $_SERVER['SERVICE_SECRET'], true);
    return urlsafeB64Encode($signature) . "," . $timestamp ;
}

function redirect()
{
    $provider  = $_SERVER['OAUTH2PROXY_PROVIDER'];
    $hostname  = $_SERVER['OAUTH2PROXY_X_FORWARDED_FOR_SITE'] . $_SERVER['DP_SITE_HOSTNAME_SUFFIX'];


    try {
        $token = $_SERVER['HTTP_X_FORWARDED_ACCESS_TOKEN'];
        $profile = \Oauth2\Profiles::profile($token, $provider);
    } catch (\Exception $e) {
        // TODO: log this exception
        $profile = [];
    }

    // create the signature
    $timestamp = time();
    $signature = createSignature($timestamp);

    $queryParams = [
        'email' =>      $_SERVER['HTTP_X_FORWARDED_EMAIL'],
        'user' =>       $_SERVER['HTTP_X_FORWARDED_USER'],
        'timestamp' =>  $timestamp,
        'signature' =>  $signature,
    ];
    $profileParams = ["name", "firstName", "lastName", "phone", "organization", "avatar", "country", "locale" ];
    foreach ($profileParams as $name) {
        if (array_key_exists($name, $profile)) {
            $queryParams[$name] = $profile[$name];
        }
    }

    $url = implode("", [
        "scheme" => "https://",
        "host" =>   $hostname,
        "path" =>   "/agent/login/authenticate-callback/$provider/oauth2/end",
        "query" =>  '?'.http_build_query($queryParams)
    ]);

    header("Location: $url");
}

function verifySignature()
{
    list($message, $timestamp) = explode(",", $_SERVER['HTTP_X_SIGNATURE']);
    $signature = createSignature($timestamp);
    if ($signature !== $_SERVER['HTTP_X_SIGNATURE']) {
        header('HTTP/1.0 403 Forbidden');
        return;
    }

    // validity of the signature in seconds
    $lifetime = 5;
    if ($lifetime < time() - $timestamp) {
        header('HTTP/1.0 403 Forbidden');
        return;
    }

    header('HTTP/1.0 200 Ok');
}

if ($_SERVER['COMMAND'] === 'redirect') {
    redirect();
} else {
    verifySignature();
}
