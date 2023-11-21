<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Usuario.php';

class MiddlewareLogin //hacer mw login y otro s no le corresponde ese producto
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
    
        if (isset($parametros['nombre_login']) && isset($parametros['clave_login'])) {
            $nombre_login = $parametros['nombre_login'];
            $clave_login = $parametros['clave_login'];
            $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre_login, $clave_login);
    
            if ($usuario) {    
   
                    return $handler->handle($request);
            } else {
                $payload = json_encode(array('mensaje' => 'Error al obtener el usuario, verifique los datos ingresados'));
            }
        } else {
            $payload = json_encode(array('mensaje' => 'Error, no se encontro el nombre de usuario y/o la clave'));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
}
