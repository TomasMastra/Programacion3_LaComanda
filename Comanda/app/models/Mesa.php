<?php

class Mesa
{
    public $id;
    public $sillas;
    public $fecha_reserva;
    public $hora_inicio;
    public $hora_salida;
    public $nombre;
    public $estado;
    public $codigo;

    public function crearMesa()
    {
        if(self::validarMesa($this->sillas, $this->fecha_reserva, $this->nombre))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (sillas, fecha_reserva, hora_inicio, estado, nombre, codigo) VALUES (:sillas, :fecha_reserva, :hora_inicio, :estado, :nombre, :codigo)");
            $consulta->bindValue(':sillas', $this->sillas, PDO::PARAM_INT);
            $consulta->bindValue(':fecha_reserva', $this->fecha_reserva, PDO::PARAM_STR);
            $consulta->bindValue(':hora_inicio', $this->hora_inicio, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', self::generarCodigoMesaUnico(), PDO::PARAM_STR);

            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();

        }
        return false; 
    }
    
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, sillas, fecha_reserva, hora_inicio, hora_salida, nombre, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }

    public static function obtenerMesa($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, fecha_reserva, hora_inicio, hora_Salida, nombre, estado, sillas FROM mesas WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function contarMesas($nombre)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $verificarConsulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) FROM mesas WHERE nombre = :nombrre AND estado != 'Cerrada'");
        $verificarConsulta->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $verificarConsulta->execute();
        $existeMesa = $verificarConsulta->fetchColumn();

        return $existeMesa;
    }

  /*  public static function modificarEstadoMesa($estado, $nombre)
    {
        $mesa = self::obtenerMesa($nombre);
    
        try {
            if ($mesa) {
                if (self::validarEstado($estado)) {

                    $objAccesoDato = AccesoDatos::obtenerInstancia();
                    echo "Dentro del if <br>";

                    $consulta = $objAccesoDato->prepararConsulta("
                        UPDATE mesas 
                        SET estado = :estado, 
                            fecha_salida = 
                                CASE 
                                    WHEN :estado = 'Cerrado' THEN CURRENT_TIMESTAMP 
                                    ELSE NULL 
                                END
                        WHERE nombre = :nombre
                    ");
    
                    echo "A este no llega </br>";

                    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    
                    // Ejecutar la consulta y devolver el resultado
                    $resultado = $consulta->execute();
    
                    return $resultado;
                }
            }
        } catch (PDOException $e) {
            // Manejar la excepción, registrarla o relanzarla según sea necesario
            // ...
        }
        echo "Llego hasta aca y salio del if </br>";
        return false;
    }*/

    public static function cambiarEstadoMesa($estado, $nombre)
    {
        $pedido = self::obtenerMesaPorId($id);

        try {
            if ($pedido && self::validarEstado($estado)) {

                
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE nombre = :nombre");
                $consulta->bindValue(':nombre', $nombre, PDO::PARAM_INT);    
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);    
                $resultado = $consulta->execute();
                return $resultado;
                

            }
        } catch (PDOException $e) {

        }
    
        return false;
    }
    
    
    public static function obtenerMesaPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, fecha_reserva, hora_inicio, hora_Salida, nombre, estado, sillas FROM mesas WHERE id_mesa = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function obtenerMesaPorCodigo($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, fecha_reserva, hora_inicio, hora_Salida, nombre, estado, sillas, codigo FROM mesas WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject();
    }
    

    public static function validarEstado($estado)
    {
        if($estado == "Con cliente comiendo" || $estado == "Con cliente pagando" || $estado == "Cerrada")
        {
            return true;
        }
        return false;
    }


    public static function validarMesa($sillas, $fecha_reserva, $nombre)
    {
        if($sillas > 0 && $sillas < 11 && $fecha_reserva > '2023-11-01' && !self::obtenerMesa($nombre))
        {
            return true;
        }

        return false;
    }

    public static function generarCodigoMesaUnico()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        do {
            $codigo = substr(uniqid(), -5);

            $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM mesas WHERE codigo = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();

            // Si el resultado es mayor que cero, significa que el código ya existe
            $existeCodigo = $consulta->fetchColumn() > 0;
        } while ($existeCodigo);

        return $codigo;
    }



}