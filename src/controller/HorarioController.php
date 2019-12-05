<?php

namespace src\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use src\model\HorarioModel;

use function src\get_string_between;
use function src\getContents;

class HorarioController{

    function index(Request $req, Response $res, $args): Response{
       $horario = new HorarioModel();

       return $horario->getHorarioRequest($req, $res, $args);
    }


   
}