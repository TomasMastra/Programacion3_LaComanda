<?php

require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Usuario.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Producto.php';
require_once 'C:\Users\Tomas Mastra\slim-php-mysql-heroku\app\models\Mesa.php';



class Pedido
{
    public $id;
    public $costo;
    public $hora_pedido;
    public $mesa_id;
    public $producto_id;
    public $estado;
    public $cantidad;
    public $tiempo;
    public $codigo;

    public function crearPedido()
    {
        if(self::validarPedido($this->mesa_id, $this->producto_id))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (costo, hora_pedido, mesa_id, producto_id, cantidad, tiempo, codigo) VALUES (:costo, :hora_pedido, :mesa_id, :producto_id, :cantidad, :tiempo, :codigo)");
            $consulta->bindValue(':costo', $this->costo, PDO::PARAM_INT);
            $consulta->bindValue(':hora_pedido', $this->hora_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':mesa_id', $this->mesa_id, PDO::PARAM_INT);
            $consulta->bindValue(':producto_id', $this->producto_id, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':tiempo', 10, PDO::PARAM_INT);
            $consulta->bindValue(':codigo', self::generarCodigoPedidoUnico(), PDO::PARAM_STR);


            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        }
        return false;
    }

    public static function obtenerTodos($nombre, $clave)
    {
        $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre, $clave);
    
        if($usuario)
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $condicion = ($usuario->trabajo_id == 5) ? "" : "WHERE trabajo_id = :rol";
            
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, costo, hora_pedido, mesa_id, producto_id, cantidad, estado FROM pedidos $condicion");
    
            // Si el usuario no es el jefe, vincula el rol en la consulta
            if ($usuario->trabajo_id != 5) {
                $consulta->bindValue(':rol', $usuario->trabajo_id, PDO::PARAM_INT);
            }
    
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS);
        }
    
        return false;
    }
    

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, costo, hora_pedido, mesa_id, producto_id, cantidad, estado FROM pedidos WHERE id_pedido = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject();
    }


    public static function modificarUsuario($id, $estado)
    {
        $peddo = self::obtenerPedidoPorId($id);
        
        try {
            if ($pedido) {
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos  SET estado = :estado WHERE id_usuario = :id");
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
                $consulta->bindValue(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
    
                $consulta->execute();
            }
        } catch (PDOException $e) {

        }
    
        return $resultado;
    }
    
    public static function cambiarEstadoPedido($id, $estado)
    {
        $pedido = self::obtenerPedidoPorId($id);

        try {
            if ($pedido && self::validarEstado($estado)) {

                
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id");
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);    
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);    
                $resultado = $consulta->execute();
                return $resultado;
                

            }
        } catch (PDOException $e) {

        }
    
        return false;
    }
// borrar
    public static function contarPedidos($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $verificarConsulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) FROM pedidos WHERE id_pedido = :id");
        $verificarConsulta->bindValue(':id', $id, PDO::PARAM_INT);
        $verificarConsulta->execute();
        $existePedido = $verificarConsulta->fetchColumn();

        return $existePedido;
    }


    public static function validarPedido($mesa_id, $producto_id)
    {
        if(Mesa::obtenerMesaPorId($mesa_id) && Producto::obtenerProductoPorId($producto_id)) //error aca
        {
            return true;
        }
        return false;  
    }

    public static function obtenerPedidoPorId($id)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, costo, hora_pedido, mesa_id, producto_id, estado, cantidad FROM pedidos WHERE id_pedido = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function obtenerPedidoPorCodigo($codigo)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, costo, hora_pedido, mesa_id, producto_id, estado, cantidad, codigo FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function tomarFoto($mesa_id)
    {
        $uploadDirectory = '..\FotosMesas';

        if (isset($_FILES['foto'])) {
            $tamanoImagen = $_FILES['foto']['size'];
            $tipoImagen = $_FILES['foto']['type'];
        
            if ((strpos(strtolower($tipoImagen), "png") || strpos(strtolower($tipoImagen), "jpg")) && ($tamanoImagen < 1000000)) {
                $nombreArchivo = $mesa_id . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $archivoTemporal = $_FILES['foto']['tmp_name'];
                $rutaDestino = $uploadDirectory . $nombreArchivo;
        
                if (move_uploaded_file($archivoTemporal, $rutaDestino)) {
                    echo 'Imagen cargada exitosamente';
                } else {
                    echo 'Error al cargar la imagen';
                }
            } else {
                echo 'No se aceptan archivos con el tipo: ' . $tipoImagen . "</br>";
            }
        } else {
            echo 'No se seleccionó ninguna imagen';
        }
        
    }

    public static function validarEstado($estado)
    {
        return ($estado == "Listo para servir" || $estado == "En preparacion" || $estado == "Servido");        
    }

    public static function generarCodigoPedidoUnico()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        do {
            $codigo = substr(uniqid(), -5);

            $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM pedidos WHERE codigo = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();

            // Si el resultado es mayor que cero, significa que el código ya existe
            $existeCodigo = $consulta->fetchColumn() > 0;
        } while ($existeCodigo);

        return $codigo;
    }

}
