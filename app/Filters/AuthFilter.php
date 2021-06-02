<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use App\Models\UsersModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        if (!$request->getHeader('Authorization')) {
            $response->setStatusCode(401);
            $response->setBody(json_encode(["status" => 0, "message" => "Unauthorized", "data" => []]));
            $response->setHeader('Content-type', 'application/json');
            return $response;
        }
        try {
            $jwt = explode("Bearer ", $request->getHeader('Authorization')->getValue())[1];
            $decoded = JWT::decode($jwt, env("appJWTKey"), array('HS256'));
            $userModel = new UsersModel();
            $user = $userModel->getUserByEmail($decoded->user_email);
            unset($user['user_password']);
            unset($user['user_created_at']);
            unset($user['user_updated_at']);
            unset($user['user_g_auth_key']);
            unset($user['user_fb_auth_key']);
            $request->user = $user;
            if ($arguments != null) {
                if (!in_array($user['role'], $arguments)) {
                    throw new \Exception();
                }
            }
        } catch (\Exception $th) {
            $response->setStatusCode(401);
            $response->setBody(json_encode(["status" => 0, "message" => $th->getMessage(), "data" => []]));
            $response->setHeader('Content-type', 'application/json');
            return $response;
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
