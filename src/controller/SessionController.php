<?php


namespace src\controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use src\model\SessionsModel;

final class SessionController{

    public function index(Request $req, Response $res, $args): Response{
        return $res->withJson(array('status'=>200,'message'=>"last update on 5/12/19"));
    }


    public function authUser(Request $req, Response $res, $args): Response{
        $session = new SessionsModel();
        return $session->singInUser($req, $res, $args);
    }

}