<?php

require_once 'Usuario.php';
class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $trabajo_id;

    public function crearProducto()
    {
        if(self::validarProducto($this->nombre, $this->precio, $this->trabajo_id))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio, trabajo_id) VALUES (:nombre, :precio, :trabajo_id)");
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':trabajo_id', $this->trabajo_id, PDO::PARAM_INT);
        
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();

        }

        return false;
    
    }

    public static function obtenerTodos($nombre, $clave)
    {
        $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre, $clave);
    
        echo $usuario->trabajo_id;
        if($usuario)
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            //$usuario->trabajo_id = 2; //probamos con otro id
            $condicion = ($usuario->trabajo_id == 5) ? "" : "WHERE trabajo_id = :rol";
            
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, nombre, precio, trabajo_id FROM productos $condicion");
    
            // Si el usuario no es el jefe, vincula el rol en la consulta
            if ($usuario->trabajo_id != 5) {
                $consulta->bindValue(':rol', $usuario->trabajo_id, PDO::PARAM_INT);
            }
    
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS);
        }
    
        return false;
    }

    public static function obtenerProducto($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, nombre, precio, trabajo_id FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    // Modifica un producto
    public static function modificarProducto($id, $nombre, $precio, $trabajo_id)
    {
        try {
            if (self::contarProductos($id) > 0 && self::validarProducto($nombre, $precio, $trabajo_id)) {
                echo $trabajo_id;
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, precio = :precio, trabajo_id = :trabajo_id WHERE id_producto = :id");
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
                $consulta->bindValue(':trabajo_id', $trabajo_id, PDO::PARAM_INT);
    
                $consulta->execute();

                return true;
            }
        } catch (PDOException $e) {

        }
    
        return false;
    }
    
    // Busca si existe el id
    public static function contarProductos($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $verificarConsulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) FROM productos WHERE id_producto = :id");
        $verificarConsulta->bindValue(':id', $id, PDO::PARAM_INT);
        $verificarConsulta->execute();
        $existeProducto = $verificarConsulta->fetchColumn();

        return $existeProducto;
    }

    // Valida que este todo OK para dar el alta, verifica el sector
    public static function validarProducto($nombre, $precio, $trabajo_id)
    {
        if($nombre != "" && $precio > 100 && $trabajo_id < 5 && $trabajo_id > 0)//hacer funcion para validar trabajo
        {
            echo $trabajo_id;
            return true;
        }

        return false;
    }
    public static function obtenerProductoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id_producto = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    // Busca a quien le corrresponde ese producto mediante el id
    public static function buscarEncargado($id)//borrar
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT trabajo_id FROM productos WHERE id_producto = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        
        $consulta->execute();
        
        return $consulta->fetchObject();
    }

    
}
