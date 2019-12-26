<?php
namespace src\model;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class NotifyModel{

    function notifyUser(Request $req, Response $res, $args) : Response{
        $parsedBody = $req->getParsedBody();
        $userToken = $req->getHeader('userToken');

        if(!isset($userToken[0])){
            return $res->withStatus(400)->withJson(array('message'=>'Admin not authenticated'));
        }

        $admin = new AdminModel();

        if(!($admin->verifyAuthorizedAdmin($userToken[0]))){
            return $res->withStatus(400)->withJson(array('message'=>'Admin not avaliable'));
        }

        if(isset($parsedBody['title'])&&isset($parsedBody['message'])){
            $title = $parsedBody['title'];
            $message = $parsedBody['message'];
            $firebase = new FirebaseHandler($title, $message);

            $notify = $firebase->notifyAllUsers();
            if(!$notify){
                return $res->withStatus(400)->withJson(array('message'=>'Erro sending message'));
            }

            return $res->withStatus(200)->withJson($notify);

        }else{
            return $res->withStatus(400)->withJson(array('message'=>'Missing parameters'));
        }

    }

}