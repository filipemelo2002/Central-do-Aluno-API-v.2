<?php


namespace src\routes;

use src\controller\FaltasController;
use src\controller\BoletinsController;
use src\controller\HorarioController;
use src\controller\SessionController;

use function src\getConfigs;

$app = new \Slim\App(getConfigs());


$app->get('/',SessionController::class .':index');
$app->post('/sessions', SessionController::class.':authUser');
$app->get('/boletins',BoletinsController::class .':index');
$app->get('/boletins/view', BoletinsController::class.':view');
$app->get('/faltas',FaltasController::class .':index');
$app->get('/horarios', HorarioController::class.':index');
$app->run();

/*RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]*/