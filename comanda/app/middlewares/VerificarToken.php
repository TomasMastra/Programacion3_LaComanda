<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarToken
{
    public function __invoke(Request $request, RequestHandler $handler)
    {

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutenticadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
}
