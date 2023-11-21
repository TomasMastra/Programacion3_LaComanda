<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Usuario.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Pedido.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Producto.php';


class MiddlewareEmpleado //hacer mw login y otro s no le corresponde ese producto
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
        $payload = 'ERROR';
    
        if (isset($parametros['id_pedido'])) {

            $nombre_login = $parametros['nombre_login'];
            $clave_login = $parametros['clave_login'];
            $id_pedido = $parametros['id_pedido'];

            $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre_login, $clave_login);
            $pedido = Pedido::obtenerPedidoPorId($id_pedido);
    
            if($pedido)
            {
                $producto = Producto::obtenerProductoPorId($pedido->producto_id);

                if($producto)
                {
                    if ($usuario->trabajo_id == $producto->trabajo_id) {    
   
                        return $handler->handle($request);
                    } else {
                        $payload = json_encode(array('mensaje' => 'Error al acceder, no te corrresponde ese pedido'));
                    }
                }

            }
            
        } else {
            $payload = json_encode(array('mensaje' => 'Error, no se encontro el pedido'));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
}
