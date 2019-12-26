<?php


namespace src\controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use src\model\AdminModel;
use src\model\NotifyModel;

class AdminController{

    public function login(Request $req, Response $res, $args): Response{
        $response = new AdminModel();

        return $response->handleAdminSignIn($req, $res, $args);
    }


    public function getData(Request $req, Response $res, $args): Response{
        $response = new AdminModel();

        return $response->getUsersData($req, $res, $args);
    }

    public function notification(Request $req, Response $res, $args):Response{
        $response = new NotifyModel();

        return $response->notifyUser($req, $res, $args);
    }
}