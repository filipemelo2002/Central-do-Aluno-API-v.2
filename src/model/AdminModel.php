<?php 

namespace src\model;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

use function src\getDatabaseConfigs;

class AdminModel{

    public function handleAdminSignIn(Request $req, Response $res, $args): Response{
        $parsedBody = $req->getParsedBody();

        if(!isset($parsedBody['email'])||!isset($parsedBody['senha'])){
            return $res->withStatus(400)->withJson(array('message'=>'Few parameters missing'));
        }

        $configs = getDatabaseConfigs();
        $database = new DatabaseHandler($configs['dbName'],$configs['host'], $configs['user'], $configs['pass']);
        $response = $database->authAdmin($parsedBody['email'],$parsedBody['senha']);

        if(!$response){
            return $res->withStatus(400)->withJson(array('message'=>'Admin not authenticated'));
        }

        return $res->withStatus(200)->withJson($response);
    }


    public function getUsersData(Request $req, Response $res, $args): Response{
        $userToken = $req->getHeader('userToken');

        if(!isset($userToken[0])){
            return $res->withStatus(400)->withJson(array('message'=>'Admin not authenticated'));
        }

        $configs = getDatabaseConfigs();
        $database = new DatabaseHandler($configs['dbName'],$configs['host'], $configs['user'], $configs['pass']);

        $auhenticated = $database->consultarAdminoAutenticado($userToken[0]);

        if(!$auhenticated){
            return $res->withStatus(400)->withJson(array('message'=>'Admin not avaliable'));
        }

        $response = $database->getAllUsers();

        return $res->withStatus(200)->withJson($response);
    }

}