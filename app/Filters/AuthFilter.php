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
        $decoded = "";
        $uriSegments = $request->getUri()->getSegments();
        try {
            $jwt = explode("Bearer ", $request->getHeader('Authorization')->getValue())[1];
            $decoded = JWT::decode($jwt, env("appJWTKey"), array('HS256'));
            $userModel = new UsersModel();
            $user = $userModel->getUserByEmail($decoded->user_email);
            if (empty($uriSegments[2] ?? "") && ($uriSegments[2] ?? "") !== 'ubah_password') {
                unset($user['user_password']);
            }
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
        } catch (\Firebase\JWT\ExpiredException $th) {
            if (isset($uriSegments[2]) && $uriSegments[2] !== 'refresh_token') {
                $jwt = explode("Bearer ", $request->getHeader('Authorization')->getValue())[1];
                helper('create_token');
                $refresh_token = create_by_jwt($jwt);
                $response->setStatusCode(200);
                $response->setBody(json_encode(["status" => 1, "message" => "update_token", "data" => ['token' => $refresh_token]]));
                $response->setHeader('Content-type', 'application/json');
                return $response;
            } else {
                $response->setStatusCode(401);
                $response->setBody(json_encode(["status" => 0, "message" => "unathorized", "data" => []]));
                $response->setHeader('Content-type', 'application/json');
                return $response;
            }
            return $response;
        } catch (\Exception $th) {
            $response->setStatusCode(401);
            $response->setBody(json_encode(["status" => 0, "message" => "unathorized", "data" => []]));
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
