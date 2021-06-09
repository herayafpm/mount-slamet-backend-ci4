<?php

use Firebase\JWT\JWT;

function create_by_jwt($jwt)
{
    $seconds = 20;
    list($header, $payload, $signature) = explode(".", $jwt);
    $payload = json_decode(base64_decode($payload));
    $now = date("Y-m-d H:i:s");
    $time = strtotime($now);
    $payload->iat = $time;
    $payload->iss = config('app')->appName;
    $payload->nbf = $time;
    $payload->exp = $time + $seconds;
    $refresh_token = JWT::encode($payload, env('appJWTKey'));
    return $refresh_token;
}

function create_token($data)
{
    // $seconds = 20;
    // $now = date("Y-m-d H:i:s");
    // $time = strtotime($now);
    // $data['iat'] = $time;
    // $data['iss'] = config('app')->appName;
    // $data['nbf'] = $time;
    // $data['exp'] = $time + $seconds;
    $jwt = JWT::encode($data, env('appJWTKey'));
    return $jwt;
}
