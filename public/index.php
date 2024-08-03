<?php

require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use SecretServer\SecretController;

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$secretController = new SecretController();

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Welcome to my secret project!");
    return $response;
});

$app->get('/secret/form', function (Request $request, Response $response, array $args) {
    $html = file_get_contents(__DIR__ . '/form.html');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/secret', [$secretController, 'createSecret']);

session_start();

$app->get('/secret/{hash}', function (Request $request, Response $response, $args) use ($secretController) {
    
    $hash = $_SESSION['hash'] ?? '';
    $remainingViews = $_SESSION['remainingViews'] ?? '';

    if (empty($hash)) {
        $response->getBody()->write('Your hash not exists!');
        return $response;
    } elseif (empty($remainingViews) || $remainingViews == 0) {
        $response->getBody()->write('Your remaining views are running out of attempts!');
        return $response;
    }

    return $secretController->getSecret($request, $response, ['hash' => $hash, 'remainingViews' => $remainingViews]);
});

$app->run();