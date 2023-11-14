<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Usuario.php';

class MiddlewareAgregarProducto 
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();
        $nombre_login = $parametros['nombre_login'];
        $clave_login = $parametros['clave_login'];
        $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre_login, $clave_login);
        $response = new Response();
        $payload = '';
    
        if($usuario->trabajo_id != 5) {

            if(isset($parametros['trabajo_id']))
            {
                $rol = $parametros['trabajo_id'];
                if($rol == $usuario->trabajo_id)
                {
                    return $handler->handle($request);

                }else{
                    $payload = json_encode(array('mensaje' => 'Error al agregar, verifique si el producto esta en su SECTOR'));

                }
            }else{
                $payload = json_encode(array('mensaje' => 'Error, verifique los parametros (trabjo_id)'));

            }

        }else{
            return $handler->handle($request);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
}
