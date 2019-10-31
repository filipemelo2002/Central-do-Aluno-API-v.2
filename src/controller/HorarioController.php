<?php

namespace src\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HorarioController extends Controllers{

    function index(Request $req, Response $res, $args): Response{

        return $res->withStatus(200)->withJson(array('message'=>"KKKKKKKKKKKKKK"));
    }
}