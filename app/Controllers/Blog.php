<?php

namespace App\Controllers;

use App\Models\BlogsModel;
use App\Models\UsersModel;
use Exception;

class Blog extends BaseController
{
    public function data()
    {
        $blogs_model = new BlogsModel();
        $blog =  $blogs_model->select("blog_judul,blog_isi")->first();
        $response = service('response');
        $response->setStatusCode(200);
        $response->setBody(json_encode(['status' => true, 'message' => "Berhasil", 'data' => $blog]));
        $response->setHeader('Content-type', 'application/json');
        return $response;
    }
    public function index()
    {
        try {
            $username = $this->request->getGet("username");
            $password = $this->request->getGet("password");
            if (!empty($username) && !empty($password)) {
                $user_model = new UsersModel();
                $user = $user_model->authenticate($username, $password);
                if ($user) {
                    $this->session->set('isLogin', true);
                    $blogs_model = new BlogsModel();
                    $data['blog'] = $blogs_model->first();
                    return view("blog", $data);
                } else {
                    throw new Exception();
                }
            } else {
                throw new Exception();
            }
        } catch (\Exception $th) {
            return redirect()->to(base_url());
        }
    }
    public function upload_file()
    {
        if (!isset($this->session->isLogin)) return redirect()->to(base_url());
        if ($this->request->getFile('file') != null) {
            $img = $this->request->getFile('file');
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move(FCPATH . 'blogs', $newName);
            }
        }
        if (!empty($newName)) {
            return base_url('blogs/' . $newName);
        } else {
            return "";
        }
    }
    public function update_blog()
    {
        if (!isset($this->session->isLogin)) return redirect()->to(base_url());
        $blogs_model = new BlogsModel();
        $blog =  $blogs_model->first();
        if ($blog) {
            $res = $blogs_model->update($blog['blog_id'], [
                'blog_isi' => $this->request->getPost("blog_isi")
            ]);
        } else {
            $res = $blogs_model->save([
                'blog_judul' => $this->request->getPost("blog_judul") ?? "judul",
                'blog_isi' => $this->request->getPost("blog_isi")
            ]);
        }
        echo json_encode(['status' => $res]);
    }
}
