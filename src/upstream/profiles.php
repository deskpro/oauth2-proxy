<?php

namespace Oauth2;

class Profiles
{
    const PROVIDER_GOOGLE = "google";

    const PROVIDER_LINKEDIN = "linkedin";

    const PROVIDER_AZURE = "azure";

    public static function profile(string $token, string $provider) : array
    {
        $instance = new Profiles();

        switch ($provider) {
            case Profiles::PROVIDER_GOOGLE:
                return $instance->googleProfile($token);
            case Profiles::PROVIDER_LINKEDIN:
                return $instance->linkedinProfile($token);
            case Profiles::PROVIDER_AZURE:
                return $instance->azureProfile($token);
            default:
                throw new \RuntimeException(sprintf("unknown provider: %s", $provider));
        }
    }

    public function googleProfile(string $token) : array
    {
        $url = implode("", [
            "scheme" => "https://",
            "host" =>   "people.googleapis.com",
            "path" =>   "/v1/people/me",
            "query" =>  '?'.http_build_query([
                "personFields" => 'names,photos'
                ])
        ]);
        $data = $this->get($url, [ "Authorization: Bearer $token" ]);
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $profile = [];
        if (array_key_exists("names", $data) && is_array($data['names'])) {
            foreach ($data['names'] as $name) {
                if ($name["metadata"]["primary"]) {
                    $profile["name"]        = $name["displayName"];
                    $profile["firstName"]   = $name["givenName"];
                    $profile["lastName"]    = $name["familyName"];
                }
            }
        }

        return $profile;
    }

    public function linkedinProfile(string $token) : array
    {
        $data = $this->get('https://api.linkedin.com/v1/people/~', [
            "Authorization: Bearer $token",
            "X-RestLi-Protocol-Version: 2.0.0",
            "Content-Type: application/json",
            "x-li-format: json"
        ]);
        if (empty($data)) {
            return [];
        }

        return [
            "firstName" => $data["firstName"],
            "lastName"  => $data["lastName"]
        ];
    }

    public function azureProfile(string $token) : array
    {
//        $data = $this->get('https://graph.windows.net/me?api-version=1.6', [
//            "Authorization: Bearer $token",
//            "Content-Type: application/json"
//        ]);
        $data = $this->get('https://graph.microsoft.com/v1.0/me', [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $profile = [];
        if (array_key_exists("surname", $data)) {
            $profile["lastName"] = $data["surname"];
        }
        if (array_key_exists("givenName", $data)) {
            $profile["firstName"] = $data["givenName"];
        }
        if (array_key_exists("displayName", $data)) {
            $profile["name"] = $data["displayName"];
        }

        return $profile;
    }

    private function get(string $url, array $headers) : ?array
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url); // Set the url
        \curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);

        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        \curl_setopt($ch,CURLOPT_TIMEOUT, 1);
        \curl_setopt($ch,CURLOPT_MAXREDIRS, 1);

        $result = \curl_exec($ch); // Execute
        $status = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \curl_close($ch); // Closing

        if ($status === 200) {
            return json_decode($result, true);
        }
        throw new \Exception($result . "\n" . implode($headers));
        return null;
    }
}
