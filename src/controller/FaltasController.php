<?php


namespace src\controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FaltasController extends Controllers{
    
    public function index(Request $req, Response $res, $args):Response{
        $this->chAuth = curl_init();
        $userToken = $req->getHeader('userToken'); 
        $filter = $req->getQueryParams();
        if(isset($userToken[0])&&isset($filter['boletimId'])&&isset($filter['ano'])){
            $user = $this->verifyUserInDatabase($userToken[0]);
            if($user){
                $response = $this->getPercentFaltas($user['email'], $user[ 'senha']);

                return $res->withStatus(200)->withJson($response);
            }

            return $res->withStatus(400)->withJson(array('message'=>'User not avaliable'));
        }

        return $res->withStatus(400)->withJson(array('message'=>'missing parameters'));
    }

    private function getPercentFaltas($email, $senha){


    }
    
}