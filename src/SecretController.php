<?php
namespace SecretServer;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SecretController {
    private $secretService;

    public function __construct() {
        $this->secretService = new SecretService();
    }

    public function createSecret(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody(), true);
        $secret = $this->secretService->createSecret($data);

        $_SESSION['hash'] = $secret['hash'];
        $_SESSION['remainingViews'] = $secret['remainingViews'];
        session_write_close();

        $response->getBody()->write(json_encode($secret));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getSecret(Request $request, Response $response, $args)
    {
        $secret = $this->secretService->getSecret($args['hash'], $args['remainingViews']);

        if ($secret) {
            $response->getBody()->write(json_encode($secret));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write('Your expiry date has gone!');
            return $response;
        }
    }
}
