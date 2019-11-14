<?php


namespace src\controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use function src\getDatabaseConfigs;


final class SessionController extends Controllers{

    public function index(Request $req, Response $res, $args): Response{
        $this->chAuth = curl_init();
        $this->getProxyIps();
        $index = array_rand($this->proxies,1);
        return $res->withJson(array('status'=>200,'usingProxy'=>$this->proxies[$index]));
    }


    public function authUser(Request $req, Response $res, $args): Response{
        $this->chAuth = curl_init();
        $this->getProxyIps();
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
        $userToken = hash('sha256', $email.$senha);
        if(!$database->consultarUsuarioAutenticado($userToken)){
            $database->cadastrarUsuario($userToken, $email, $senha);
        }
        return $userToken;
    }
}