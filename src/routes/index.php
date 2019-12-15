<?php


namespace src\routes;

use src\controller\AdminController;
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
$app->post('/admin/login', AdminController::class.':login');
$app->get('/admin/view', AdminController::class.':getData');

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();

/*RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]*/