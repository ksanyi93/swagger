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

$app->post('/secret', [$secretController, 'createSecret']);

//$app->get('/secret/{hash}', [$secretController, 'getSecret']);

session_start();

$app->get('/secret/{hash}', function (Request $request, Response $response, $args) use ($secretController) {
    $hash = $_SESSION['hash'];

    if (empty($hash)) {
        echo 'Hiba történt a számításban!';
    }

    return $secretController->getSecret($request, $response, ['hash' => $hash]);
});

/* $app->get('/swagger.yaml', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/../swagger.yaml'));
    return $response->withHeader('Content-Type', 'application/x-yaml');
}); */

$app->run();