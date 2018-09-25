<?php

function urlsafeB64Encode($input) : string
{
    return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
}


function createSignature(): string {

    $msg = implode("\n", [
        $_SERVER['HTTP_X_FORWARDED_USER'],
        $_SERVER['HTTP_X_FORWARDED_EMAIL']
    ]);

    $algorithm = "sha256";
    $signature = hash_hmac($algorithm, $msg, $_SERVER['SERVICE_SECRET'], true);
    return urlsafeB64Encode($signature);
}

function redirect()
{
    $email = $_SERVER['HTTP_X_FORWARDED_EMAIL'];
    $user  = $_SERVER['HTTP_X_FORWARDED_USER'];
    $provider  = $_SERVER['OAUTH2PROXY_PROVIDER'];
    $hostname  = $_SERVER['OAUTH2PROXY_X_FORWARDED_FOR_SITE'] . $_SERVER['DP_SITE_HOSTNAME_SUFFIX'];

    $signature = createSignature();;

    $url = implode("", [
        "scheme" => "https://",
        "host" => $hostname,
        "path" => "/agent/login/authenticate-callback/$provider/oauth2/end",
        "query" => '?'.http_build_query([
            'email' => $email,
            'user' => $user,
            'signature' => $signature,
        ])
    ]);

    header("Location: $url");
}

function verifySignature()
{
    $signature = createSignature();
    $existingSignature = $_SERVER['HTTP_X_SIGNATURE'];
    if ($signature === $existingSignature) {
        header('HTTP/1.0 200 Ok');
    } else {
        header('HTTP/1.0 403 Forbidden');
    }
}

if ($_SERVER['COMMAND'] === 'redirect') {
    redirect();
} else {
    verifySignature();
}
