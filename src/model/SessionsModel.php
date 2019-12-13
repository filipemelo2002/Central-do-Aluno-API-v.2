<?php

namespace src\model;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

use function src\get_string_between;
use function src\getContents;

use function src\getDatabaseConfigs;

class SessionsModel extends SiepeHandlerModel{

    function __construct(){
        $this->chAuth = curl_init();
    }

    function singInUser(Request $req, Response $res, $args): Response{
        $parsedBody = $req->getParsedBody();
        if(isset($parsedBody['email'])&&isset($parsedBody['senha'])){
            $email = $parsedBody['email'];
            $senha = $parsedBody['senha'];
            
            $auth = $this->authUserAtSiepe($email, $senha);
            if($auth){
                $userToken = $this->saveUserAndReturnToken($email, $senha);
                return $res->withJson(array(
                    'userToken'=>$userToken,
                    'email'=>$email,
                    'senha'=>$senha
                ));
            }
            return $res->withStatus(400)->withJson(array('message'=>"Error signing up "));
            
        }

        return $res->withStatus(400)->withJson(array('message'=>"parameters missing"));     
    }


    private function saveUserAndReturnToken($email, $senha){
        $configs = getDatabaseConfigs();
        $database  = new \src\model\DatabaseHandler($configs['dbName'], $configs['host'], $configs['user'], $configs['pass']);
        $userToken = hash('md5', $email);
        if(!$database->consultarUsuarioAutenticado($userToken)){
            $database->cadastrarUsuario($userToken, $email, $senha);
        }
        return $userToken;
    }
}