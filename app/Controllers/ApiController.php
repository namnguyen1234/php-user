<?php

namespace App\Controllers;

use App\Configs\Authenticate;
use App\Models\Pagination;
use App\Models\User;

header('Content-Type: application/json');

class ApiController
{
    public function logIn() 
    {
      try {
          $data = json_decode(file_get_contents('php://input'), true);

          $method = $_SERVER['REQUEST_METHOD'];

          if($method == 'POST') {
              $user_model = new User;
              $user = $user_model->hasUser($data);

              if (!$user) {
                $this->response(401, [
                    'message' => 'Login fail'
                ]);
              } else {
                $token = Authenticate::generateToken([
                    'username' => $data['username'],
                    'password' => $data['password'],
                ]);

                $this->response(200, [
                    'message' => 'Login successfully',
                    'token' => $token,
                    'user' => $user[0]
                ]);
              }
          }
      } catch (\Exception  $e) {
        $this->response(500, [
            'message' => 'Something wrong'
        ]);
      }
    }

    public function me() {
        $token = $this->getBearerToken();

        $data = Authenticate::decode($token);

        $user = (new User())->hasUser((array) $data);

        if ($user) {
            $this->response(200, [
                'message' => 'Login successfully',
                'user' => $user[0]
            ]);
        } else {
            $this->response(401, [
                'message' => 'Unauthorized'
            ]);
        }
    }

    public function getAll()
    {
      try {
            $method = $_SERVER['REQUEST_METHOD'];
           
        if ($method == 'GET') {
            $perPage = 5;
            $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
            $startAt =($page - 1)*$perPage;
            $user_model = new User();
            $user = $user_model->getAll($perPage, $startAt);
            $totalUser = $user_model->totalUser();
            $totalPage = ceil($totalUser/$perPage) ;
            

            $response = [
                'message' => 'Lấy user thành công',
                'user' => $user,
                'dataPage' => [
                    'page' => $page,
                    'startAt' => $startAt,
                    'totalPage' => $totalPage
                ]

            ];
            http_response_code(200);

            echo json_encode($response);
        }
      } catch (\Exception  $e) {
        $response = [
            'message' => 'Lấy user thất bại'
        ];
        http_response_code(500);
          echo $e->getMessage($response);
      }
    }
    public function getById()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            if ($method == 'GET') {
                $id = $_GET['id'];
                $user_model = new User();
                $user = $user_model->getById($id);
            
                $response = [
                    'message' => 'Lấy user thành công',
                    'user' => $user
                ];
                http_response_code(200);

                echo json_encode($response);
            }
        } catch (\Exception $e) {
            $response = [
                'message' => 'Lấy user thất bại'
            ];
            http_response_code(500);
              echo $e->getMessage($response);
          }
    }

   
    public function destroys()
    {
       try {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == 'DELETE') {
            $id = $_GET['id'];
            $user_model = new User();
            $user_model->delete($id);        
                $response = [
                    'message' => 'Xóa user thành công',
                ];
                http_response_code(200);

               echo json_encode($response);
        }
       }catch (\Exception $e) {
        $response = [
            'message' => 'Xóa user thất bại'
        ];
        http_response_code(500);
          echo $e->getMessage($response);
      }
    }

    public function insert()
    {
       try {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == 'POST') {
            $user_model = new User();
            $user = $user_model->insert($data);
                $response = [
                    'message' => 'Tạo mới user thành công',
                    'user' => $user
                ];
                http_response_code(200);
            
            echo json_encode($response);
        }
       } catch (\Exception $e) {
        $response = [
            'message' => 'Tạo mới user thất bại'
        ];
        http_response_code(500);
          echo $e->getMessage($response);
      }
    }

    public function update()
    {
       try {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == 'PUT') {
            $id = $_GET['id'];
            $user_model = new User();
            $user = $user_model->update($id, $data);
                $response = [
                    'message' => 'Cập nhật user thành công',
                    'user' => $user
                ];
                http_response_code(200);

                echo json_encode($response);
        }
       } catch (\Exception $e) {
        $response = [
            'message' => 'Cập nhật user thất bại'
        ];
          http_response_code(500);
          echo $e->getMessage($response);
      }
    }

    public function response($code, $data)
    {
        http_response_code($code);

        echo json_encode($data);
    }

    function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    
    /**
     * get access token from header
     * */
    function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
