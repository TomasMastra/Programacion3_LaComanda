<?php

class Usuario
{
    public $id_usuario;
    public $clave;
    public $nombre_usuario;
    public $fecha_inicio;
    public $estado;
    public $trabajo_id;

    public function crearUsuario()//ver
    {
        if(self::validarUsuario($this->trabajo_id, $this->fecha_inicio, $this->nombre_usuario))
        {

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre_usuario, fecha_inicio, estado, trabajo_id, clave) VALUES (:nombre_usuario, :fecha_inicio, :estado, :trabajo_id, :clave)");
            $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':nombre_usuario', $this->nombre_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_inicio', $this->fecha_inicio, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':trabajo_id', $this->trabajo_id, PDO::PARAM_INT);
            $consulta->bindValue(':clave', $claveHash);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        return false;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, nombre_usuario, clave, fecha_inicio, estado, trabajo_id FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre_usuario, clave, fecha_inicio, estado, trabajo_id FROM usuarios WHERE nombre_usuario = :nombre_usuario");
        $consulta->bindValue(':nombre_usuario', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    // Modifica un usuario
    public static function modificarUsuario($id, $nombre, $clave, $fecha_inicio)
    {
        $resultado = self::contarUsuarios($id);
        
        try {
            if ($resultado > 0) {
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET nombre_usuario = :nombre, clave = :clave, fecha_inicio = :fecha_inicio WHERE id_usuario = :id");
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
                $consulta->bindValue(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
    
                $consulta->execute();
            }
        } catch (PDOException $e) {

        }
    
        return $resultado;
    }
    

    // Busca si existe el usuario de id pasado por parametro, busca solo de los usuarios activos
    public static function borrarUsuario($nombre)
    {

        if (self::contarUsuariosPorNombre($nombre)) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fechaBaja, estado = :estado WHERE nombre_usuario = :nombre AND estado != 'Despedido'");
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->bindValue(':estado', "Despedido", PDO::PARAM_STR);

            $resultado = $consulta->execute();

            return $resultado;
        }
        return false;
    }

    public static function contarUsuarios($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $verificarConsulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) FROM usuarios WHERE id_usuario = :id AND estado != 'Despedido'");
        $verificarConsulta->bindValue(':id', $id, PDO::PARAM_INT);
        $verificarConsulta->execute();
        $existeUsuario = $verificarConsulta->fetchColumn();

        return $existeUsuario;
    }

    public static function contarUsuariosPorNombre($nombre)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $verificarConsulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = :nombre AND estado != 'Despedido'");
        $verificarConsulta->bindValue(':nombre', $nombre, PDO::PARAM_STR); // Debería ser PDO::PARAM_STR en lugar de PDO::PARAM_INT
        $verificarConsulta->execute();
        $existeUsuario = $verificarConsulta->fetchColumn();
    
        return $existeUsuario;
    }
    

    public static function obtenerUsuarioPorNombreClave($nombre, $clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, nombre_usuario, clave, fecha_inicio, estado, trabajo_id FROM usuarios WHERE nombre_usuario = :nombre_usuario AND clave = :clave");
        $consulta->bindValue(':nombre_usuario', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);  
    
        $consulta->execute();
    
        return $consulta->fetchObject();
    }
    

    public static function obtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre_usuario, clave, fecha_inicio, estado, trabajo_id FROM usuarios WHERE id_usuario = :id_usuario");
        $consulta->bindValue(':nombre_usuario', $nombre, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function validarUsuario($trabajo_id, $fecha_inicio, $nombre)
    {

        if($trabajo_id > 0 && $trabajo_id < 7 && $fecha_inicio > '2023-11-01' && !self::obtenerUsuario($nombre))
        {
            return true;
        }
        return false;
    }
   
    
}