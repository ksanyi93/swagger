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
        //session_write_close();

        $response->getBody()->write(json_encode($secret));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getSecret(Request $request, Response $response, $args)
    {
        $hash = $args['hash'];
        $secret = $this->secretService->getSecret($hash);

        if ($secret) {
            $response->getBody()->write(json_encode($secret));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(404);
        }
    }
}
