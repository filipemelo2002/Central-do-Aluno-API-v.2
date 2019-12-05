<?php


namespace src\controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use src\model\FaltasModel;

class FaltasController{
     
    public function index(Request $req, Response $res, $args):Response{
       $model = new FaltasModel();

       return $model->getFaltasData($req, $res, $args);
    }


}