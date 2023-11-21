<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['nombre_usuario']) && isset($parametros['fecha_inicio']) && isset($parametros['trabajo_id']) && isset($parametros['clave']))
        {

          $nombre_usuario = $parametros['nombre_usuario'];
          $fecha_inicio = $parametros['fecha_inicio'];
          $estado = "Activo";
          $trabajo_id = $parametros['trabajo_id'];
          $clave = $parametros['clave'];

          // Creamos el usuario
          $usr = new Usuario();
          $usr->nombre_usuario = $nombre_usuario;
          $usr->fecha_inicio = $fecha_inicio;
          $usr->trabajo_id = $trabajo_id;
          $usr->estado = $estado;
          $usr->clave = $clave;
          $resultado = $usr->crearUsuario();

          if($resultado){
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
          }else{
            $payload = json_encode(array("mensaje" => "Error al crear el usuario, pruebe con otro nombre"));
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

    public function TraerUno($request, $response, $args)//ver 
    {
        // Buscamos usuario por nombre
        $usr = $args['nombre_usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_usuario = $parametros['id_usuario'];
        $nombre = $parametros['nombre_usuario'];
        $clave = $parametros['clave'];        
        $fecha_inicio = $parametros['fecha_inicio'];

        $resultado = Usuario::modificarUsuario($id_usuario, $nombre, $clave, $fecha_inicio);

        if($resultado)
        {
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        }else{
          $payload = json_encode(array("mensaje" => "Usuario no se pudo modificar, asegurese que el usuario este activo y/o halla ingresado un ID correcto"));

        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre_usuario']))
        {
        $nombre = $parametros['nombre_usuario'];

        //$usuario = new Usuario();
        $resultado = Usuario::borrarUsuario($nombre);

        if ($resultado) {
          // La operación fue exitosa
          $payload = json_encode(array("mensaje" => "Usuario {$nombre} borrado con exito"));
        } else {
          // Hubo un problema
          $payload = json_encode(array("mensaje" => "Usuario {$nombre} no se pudo borrar"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }else{
        $payload = json_encode(array("mensaje" => "ERROR con los parametros"));

      }

      return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(400); 

    }
}
