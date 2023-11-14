<?php
require_once './models/Mesa.php';
require_once './interfaces/IMesaApiUsable.php';

class MesaController extends Mesa implements IMesaApiUsable
{
    //
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre']) && isset($parametros['fecha_reserva']) && isset($parametros['hora_inicio']) && isset($parametros['sillas']))
        {
          $nombre = $parametros['nombre'];
          $fecha_reserva = $parametros['fecha_reserva'];
          $estado = "Con cliente esperando pedido";
          $hora_inicio = $parametros['hora_inicio'];
          $sillas = $parametros['sillas'];

          // Creamos el usuario
          $mesa = new Mesa();
          $mesa->nombre = $nombre;
          $mesa->fecha_reserva = $fecha_reserva;
          $mesa->estado = $estado;
          $mesa->sillas = $sillas;
          $mesa->hora_inicio = $hora_inicio;
          $resultado = $mesa->crearMesa();

              if($resultado)
              {
                $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
              }else{
                $payload = json_encode(array("mensaje" => "Mesa no pudo ser creada"));
              }

          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
            
        }else{

          $payload = json_encode(array("mensaje" => "Error con los parametros, intente nuevamente"));
          $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400); 
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $nombre_mesa = $args['nombre'];
        $mesa = Mesa::obtenerMesa($nombre_mesa);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CambiarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        if (isset($parametros['estado']) && isset($parametros['nombre_mesa'])) {
            $estado = $parametros['estado'];
            $nombre = $parametros['nombre_mesa'];
    
            // Validar que el estado sea válido
           /* if (!Mesa::validarEstado($estado)) {
                $payload = json_encode(array("mensaje" => "Estado inválido"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }*/
    
            // Intentar cambiar el estado de la mesa
            $resultado = Mesa::modificarEstadoMesa($estado, $nombre);
    
            if ($resultado) {
                $payload = json_encode(array("mensaje" => "Estado modificado con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "ERROR al cambiar el estado"));
            }
    
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    
        // Si falta alguno de los parámetros requeridos
        $payload = json_encode(array("mensaje" => "Parámetros incompletos"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    public function MostrarListados($request, $response, $args)
    {

    }
    
}
