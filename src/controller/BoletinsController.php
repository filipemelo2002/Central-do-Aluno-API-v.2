<?php

namespace src\controller;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use src\model\BoletinModel;

use function src\get_string_between;
use function src\getContents;

class BoletinsController{

    public function view(Request $req, Response $res, $args): Response{
        $boletin = new BoletinModel();
        return $boletin->requestBoletinsData($req, $res, $args);
    }
 
    public function index(Request $req, Response $res, $args): Response{
        $boletin = new BoletinModel();
        return $boletin->requestBoletinList($req, $res, $args);
    }


}