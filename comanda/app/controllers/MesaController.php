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
          $numero_mesa = $parametros['numero_mesa'];


          // Creamos el usuario
          $mesa = new Mesa();
          $mesa->nombre = $nombre;
          $mesa->fecha_reserva = $fecha_reserva;
          $mesa->estado = $estado;
          $mesa->sillas = $sillas;
          $mesa->hora_inicio = $hora_inicio;
          $mesa->numero_mesa = $numero_mesa;
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
            $resultado = Mesa::cambiarEstadoMesa($estado, $nombre);
    
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

   /* public function MostrarListados($request, $response, $args)
    {

    }*/

    public function AgregarResenia($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        if (isset($parametros['codigo_pedido']) && isset($parametros['codigo_mesa'])) 
        {
            $codigo_pedido = $parametros['codigo_pedido'];
            $codigo_mesa = $parametros['codigo_mesa'];
    
            $mesa = Mesa::obtenerMesaPorCodigo($codigo_mesa);
            $pedido = Pedido::obtenerPedidoPorCodigo($codigo_pedido);
    

            if ($mesa && $pedido) 
            {
                if ($pedido->mesa_id == $mesa->id_mesa) 
                {

                    if (isset($parametros['comentario']) && isset($parametros['puntaje'])) {
                        $comentario = $parametros['comentario'];
                        $puntaje = $parametros['puntaje'];
    
                        $resultado = Mesa::cargarResenia($comentario, $puntaje, $codigo_mesa);
    
                        if ($resultado) {
                            $payload = json_encode(["mensaje" => "Reseña cargada exitosamente"]);
                        } else {
                            $payload = json_encode(["mensaje" => "No se pudo cargar la encuesta"]);
                        }
                    } else {
                        $payload = json_encode(["mensaje" => "Faltan el comentario y/o puntaje"]);
                    }
                } else {
                    $payload = json_encode(["mensaje" => "No se pudo localizar la mesa, verifique ambos códigos"]);
                }
            } else {
                $payload = json_encode(["mensaje" => "Codigos no coinciden"]);
            }
        }else{
            $payload = json_encode(["mensaje" => "Faltan el código de pedido y/o el código de mesa"]);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarDesdeCSV($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mesasCreadas = 0;

        try {
            if (isset($parametros['path'])) {
                $path = $parametros['path'];

                if(file_exists($path))
                {
                    $filas = array_map('str_getcsv', file($path));
                    $encabezados = array_shift($filas);
        
                    foreach ($filas as $fila) {
                        $datosMesa = array_combine($encabezados, $fila);
                        $mesa = new Mesa();
                        $mesa->sillas = $datosMesa['sillas'];
                        $mesa->fecha_reserva = $datosMesa['fecha_reserva'];
                        $mesa->hora_inicio = $datosMesa['hora_inicio'];
                        $mesa->estado = $datosMesa['estado'];
                        $mesa->nombre = $datosMesa['nombre'];
                        $mesa->codigo = self::generarCodigoMesaUnico();
                        $mesa->numero_mesa = $datosMesa['numero_mesa'];
        
                        $idMesa = $mesa->crearMesa();
                        if ($idMesa !== false) {
                            $mesasCreadas++;
                        }
                    }
                }else{
                    $response->getBody()->write(json_encode(['error' => 'Hubo un error al con el path']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
    
                $response->getBody()->write(json_encode(['mesasCreadas' => $mesasCreadas . " mesas creadas con exito"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Hubo un error al cargar desde CSV']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    
        return $response->withStatus(400); // codigo 400 si el path no es valido
    }
    

    public function GuardarEnCSV($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
    
            // Obtener el path del archivo CSV desde los parámetros POST
            if (isset($parametros['path'])) {
                $path = $parametros['path'];
    
                // Obtener todas las mesas
                $listaMesas = Mesa::obtenerTodos();
    
                // Crear un archivo CSV
                $csvFile = fopen($path, 'w');
    
                // Escribir encabezados en el archivo CSV
                fputcsv($csvFile, array('id', 'sillas', 'fecha_reserva', 'hora_inicio', 'hora_salida', 'nombre', 'estado', 'codigo', 'numero_mesa'));
    
                // Recorrer todas las mesas y escribir los datos en el archivo CSV
                foreach ($listaMesas as $mesa) {
                    fputcsv($csvFile, array(
                        $mesa->id_mesa,
                        $mesa->sillas,
                        $mesa->fecha_reserva,
                        $mesa->hora_inicio,
                        $mesa->hora_salida,
                        $mesa->nombre,
                        $mesa->estado,
                        $mesa->codigo,
                        $mesa->numero_mesa,
  
                    ));
                }
    
                // Cerrar el archivo CSV
                fclose($csvFile);
    
                // Devolver el archivo CSV como respuesta
                $response->getBody()->write(json_encode(['mensaje' => 'Archivo CSV creado exitosamente']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Falta el parámetro "path" en la solicitud']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Hubo un error al guardar en CSV']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
     
}
