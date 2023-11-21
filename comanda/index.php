<?php
require_once 'clases/UsuarioController.php';
require_once 'clases/MesaController.php';
require_once 'clases/ProductoController.php';
require_once 'clases/PedidoController.php';
require_once 'clases/TrabajoController.php';





$usuarioController = new UsuarioController();
$mesaController = new MesaController();
$productoController = new ProductoController();
$pedidoController = new PedidoController();
$trabajoController = new TrabajoController();



if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'listar':
                $cds = $cdController->listarCds();
                echo json_encode($cds);
                break;
            }
        }

}elseif($_SERVER['REQUEST_METHOD'] === 'POST'){

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'agregar':

                if (isset($_POST['fecha_inicio']) && isset($_POST['nombre_usuario']) && isset($_POST['estado']) && isset($_POST['trabajo'])) {
                    $resultado = $usuarioController->agregarUsuario($_POST['fecha_inicio'], $_POST['estado'], $_POST['nombre_usuario'], $_POST['trabajo']);
                    echo json_encode(['resultado' => $resultado]);
                }elseif(isset($_POST['fecha_reserva']) && isset($_POST['hora_inicio']) && isset($_POST['hora_salida']) && isset($_POST['sillas']) && isset($_POST['nombre']) /* && isset($_POST['estado'])*/) {
                $resultado = $mesaController->agregarMesa($_POST['fecha_reserva'], $_POST['hora_inicio'], $_POST['hora_salida'], $_POST['sillas'], $_POST['nombre']/*, $_POST['estado']*/);
                    echo json_encode(['resultado' => $resultado]);
                }elseif (isset($_POST['nombre']) && isset($_POST['precio']) && isset($_POST['trabajo_id'])) {
                    $resultado = $productoController->agregarProducto($_POST['nombre'], $_POST['precio'], $_POST['trabajo_id']);
                    echo json_encode(['resultado' => $resultado]);
                }elseif (isset($_POST['costo_total']) && isset($_POST['hora_pedido']) && isset($_POST['detalle']) && isset($_POST['mesa_id']) && isset($_POST['estado']) && isset($_POST['producto_id'])) {
                    $resultado = $pedidoController->agregarPedido($_POST['costo_total'], $_POST['hora_pedido'], $_POST['detalle'], $_POST['mesa_id'], $_POST['estado'], $_POST['producto_id']);
                    echo json_encode(['resultado' => $resultado]);
                }else{
                        echo json_encode(['error' => 'Faltan parámetros']);
                }

                break;
            }
        }

}elseif($_SERVER['REQUEST_METHOD'] === 'PUT'){
    parse_str(file_get_contents("php://input"), $putData);
    //$putData = json_decode(file_get_contents("php://input"), true);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'modificar':
                if (isset($putData['id']) && isset($putData['titulo']) && isset($putData['cantante']) && isset($putData['anio'])) {
                    $resultado = $cdController->modificarCd($putData['id'], $putData['titulo'], $putData['cantante'], $putData['anio']);
                    echo json_encode(['resultado' => $resultado]);
                } else {
                    echo json_encode(['error' => 'Faltan parametros']);
                }
                break;
            }
        }

}elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'borrar':
                    if (isset($_GET['id_usuario'])) {
                        $resultado = $usuarioController->eliminarUsuario($_GET['id_usuario']);
                        echo json_encode(['resultado' => $resultado]);
                    } elseif((isset($_GET['id_pedido']))) {

                    }elseif((isset($_GET['id_mesa'])))
                    {
                        $resultado = $mesaController->modificarEstadoMesa($_GET['id_mesa']);
                        echo json_encode(['resultado' => $resultado]);
                    }else{
                        echo json_encode(['error' => 'Falta el parametro id']);

                    }
                    break;
                default:
                    echo json_encode(['error' => 'Accion no valida']);
                    break;
            }
        } else {
            echo json_encode(['error' => 'Falta el parametro action']);
        }
    } else {
        echo json_encode(['error' => 'Metodo HTTP no permitido']);
    }
