<?php


use Psr\http\Message\ServerRequestInterface as Request;
use Psr\http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoggerMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
       /* $before = date('Y-m-d H:i:s');

        $response = $handler->handle($request);
        $existingContent = json_decode($response->getBody());*/

        $response = new Response();
      /*  $existingContent->fechaAntes = $before;
        $existingContent->fechaDespues = date('Y-m-d H:i:s');*/

        //$payload = json_encode($existingContent);
        $payload = json_encode(array('mensaje' => 'No podes pasar'));


        $response->getBody()->Write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }
}