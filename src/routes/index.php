<?php


namespace src\routes;

use src\controller\FaltasController;
use src\controller\BoletinsController;
use src\controller\SessionController;

use function src\getConfigs;

$app = new \Slim\App(getConfigs());


$app->get('/',SessionController::class .':index');
$app->post('/sessions', SessionController::class.':authUser');
$app->get('/boletins',BoletinsController::class .':index');
$app->get('/boletins/view', BoletinsController::class.':view');
$app->get('/faltas',FaltasController::class .':index');
$app->run();