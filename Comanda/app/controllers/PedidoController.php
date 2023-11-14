<?php

require_once './models/Pedido.php';
require_once './models/Mesa.php';

require_once './interfaces/IPedidoApiUsable.php';

class PedidoController extends Pedido implements IPedidoApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['costo']) && isset($parametros['hora_pedido']) && isset($parametros['mesa_id']) && isset($parametros['producto_id'])  && isset($parametros['cantidad']))
        {

            $costo = $parametros['costo'];
            $hora_pedido = $parametros['hora_pedido'];
            $mesa_id = $parametros['mesa_id'];
            $producto_id = $parametros['producto_id'];
            $cantidad = $parametros['cantidad'];

            // Creamos el pedido
            $pedido = new Pedido();
            $pedido->costo = $costo;
            $pedido->hora_pedido = $hora_pedido;
            $pedido->mesa_id = $mesa_id;
            $pedido->producto_id = $producto_id;
            $pedido->cantidad = $cantidad;

            if ($pedido->crearPedido() == false) {
                $payload = json_encode(array("mensaje" => "Pedido no se pudo crear"));
            } else {
               Pedido::tomarFoto($pedido->mesa_id);
                $payload = json_encode(array("mensaje" => "Pedido creado con éxito"));
            }

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(400); 
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por ID
        $id_pedido = $args['id_pedido'];

        $parametros = $request->getParsedBody();

        if(isset($parametros['codigo_pedido']) && isset($parametros['codigo_mesa']))
        {
            $codigo_pedido = $parametros['codigo_pedido'];
            $codigo_mesa = $parametros['codigo_mesa'];

            $mesa = Mesa::obtenerMesaPorCodigo($codigo_mesa);
            $pedido = Pedido::obtenerPedidoPorCodigo($codigo_pedido);
            if($pedido->mesa_id == $mesa->id_mesa)
            {
                $payload = json_encode($pedido);
            }else{
                $payload = json_encode(array("mensaje" => "Pedido no fue encontrado"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre_login = isset($parametros['nombre_login']) ? $parametros['nombre_login'] : null;
        $clave_login = isset($parametros['clave_login']) ? $parametros['clave_login'] : null;
    
        $lista = Pedido::obtenerTodos($nombre_login, $clave_login);
    
        if ($lista !== false) {
            $payload = json_encode(array("listaPedido" => $lista));
        } else {
            $payload = json_encode(array("mensaje" => "Error al obtener la lista de pedidos"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    

    /*public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id_pedido'];
        $costo = $parametros['costo'];
        $hora_pedido = $parametros['hora_pedido'];
        $mesa_id = $parametros['mesa_id'];
        $producto_id = $parametros['producto_id'];
        $cantidad = $parametros['cantidad'];

        $resultado = Pedido::modificarPedido($id, $costo, $hora_pedido, $mesa_id, $producto_id, $cantidad);

        if ($resultado) {
            $payload = json_encode(array("mensaje" => "Pedido modificado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Pedido no se pudo modificar, asegúrese de que el pedido exista y/o haya ingresado un ID correcto"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }*/
/*
    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedidoId = $parametros['id_pedido'];

        $resultado = Pedido::borrarPedido($pedidoId);

        if ($resultado) {
            // La operación fue exitosa
            $payload = json_encode(array("mensaje" => "Pedido de ID {$pedidoId} borrado con éxito"));
        } else {
            // Hubo un problema
            $payload = json_encode(array("mensaje" => "Pedido de ID {$pedidoId} no se pudo borrar"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }*/

    public function CambiarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id_pedido'];
        $estado = $parametros['estado'];

        $resultado = Pedido::cambiarEstadoPedido($id, $estado);

        if ($resultado) {
            $payload = json_encode(array("mensaje" => "Pedido modificado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Pedido no se pudo modificar, asegúrese de que el pedido exista y/o haya ingresado un ID correcto"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public function MostrarListados($request, $response, $args)
    {

    }
}