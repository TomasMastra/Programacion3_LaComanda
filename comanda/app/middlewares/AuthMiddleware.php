<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\utils\AutentificadorJWT.php';


require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Usuario.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Producto.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Pedido.php';



class AuthMiddleware
{
    public static function verificarRolSocio(Request $request, RequestHandler $handler)
    {     
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            if($data->rol == 5)
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }else{
                throw new Exception();
            }
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');

    }

    public static function verificarToken(Request $request, RequestHandler $handler)
    {

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    // Veifica el el usuario sea mozo y socio
    public static function verificarRolMozo(Request $request, RequestHandler $handler)
    {     
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            if($data->rol == 4 || $data->rol == 5)
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }else{
                throw new Exception();
            }
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');

    }

    public static function verificarRolEmpleadoAgregar(Request $request, RequestHandler $handler)
    {
        /*
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
        */

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            $parametros = $request->getParsedBody();

            $sector = $parametros['trabajo_id'];

            if($data->rol == $sector || $data->rol == 5)
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }else{
                throw new Exception();
            }
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarRolEmpleadoCambiarEstado(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try{
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            $sector = $parametros['trabajo_id'];

            if($data->rol == $sector || $data->rol == 5)
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }else{
                throw new Exception();
            }
        }catch (Exception $e){
            $response = new response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarRolEmpleadoBuscarProducto(Request $request, RequestHandler $handler)
    {
        //$parametros = $request->getParsedBody();
    
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
    
        try {
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
    
            // Access route parameters using getAttribute
            $nombre_producto = $request->getAttribute('nombre');
            $producto = Producto::obtenerProducto($nombre_producto);
    
            if ($data->rol == $producto->trabajo_id || $data->rol == 5) {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarRolPedido(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response(); // Initialize $response here
    
        try {
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
    
            $parametros = $request->getParsedBody();
    
            if (isset($parametros['id_pedido'])) {
                $id_pedido = $parametros['id_pedido'];
                $pedido = Pedido::obtenerPedidoPorId($id_pedido);
                $producto = Producto::obtenerProductoPorId($pedido->producto_id);
    
                if ($pedido && ($producto->trabajo_id == $data->rol) || $data->rol == 5) {
                    $request->datosToken = $data;
                    $response = $handler->handle($request);
                } else {
                    throw new Exception();
                }
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    

    
}
