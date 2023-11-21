<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';


require_once 'middlewares\LoggerMiddleWare.php';

require_once 'middlewares\MiddlewareMozo.php';
require_once 'middlewares\MiddlewareLogin.php';
require_once 'middlewares\MiddlewareEmpleado.php';
require_once 'middlewares\MiddlewareAgregarProducto.php';

require_once 'middlewares\AuthMiddleware.php';
require_once 'utils\AutentificadorJWT.php';




// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Solo Socio
  $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos'); // mostrar todos OK
    $group->get('/{nombre_usuario}', \UsuarioController::class . ':TraerUno');// OK
    $group->post('[/]', \UsuarioController::class . ':CargarUno');    // OK // nombre repetido OK // validar sector OK
    $group->put('/modificar', \UsuarioController::class . ':ModificarUno'); //OK // User inc OK 
    $group->delete('/borrar', \UsuarioController::class . ':BorrarUno');// OK
  })->add(\authMiddleware::class . ':verificarToken')->add(\authMiddleware::class . ':verificarRolSocio');
  // Solo mozos
  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\authMiddleware::class . ':verificarRolMozo');
    $group->get('/{nombre}', \MesaController::class . ':TraerUno')->add(\authMiddleware::class . ':verificarRolMozo');
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(\authMiddleware::class . ':verificarRolMozo');
    $group->post('/estado', \MesaController::class . ':CambiarEstado')->add(\authMiddleware::class . ':verificarRolMozo');
    $group->post('/resenia', \MesaController::class . ':AgregarResenia')->add(\authMiddleware::class . ':verificarRolMozo');  
    $group->post('/cargar', \MesaController::class . ':CargarDesdeCSV')->add(\authMiddleware::class . ':verificarRolMozo');  
    $group->post('/guardar', \MesaController::class . ':GuardarEnCSV')->add(\authMiddleware::class . ':verificarRolMozo');  
  })->add(\authMiddleware::class . ':verificarToken');

  $app->group('/productos', function (RouteCollectorProxy $group) { 
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{nombre}', \ProductoController::class . ':TraerUno')->add(\authMiddleware::class . ':verificarRolEmpleadoBuscarProducto');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\authMiddleware::class . ':verificarRolEmpleadoAgregar');
    $group->post('/modificar', \ProductoController::class . ':ModificarUno');//ver si dejar
    $group->post('/borrar', \ProductoController::class . ':BorrarUno');
  })->add(\authMiddleware::class . ':verificarToken');/*->add(\authMiddleware::class . ':verificarRolMozo');*/

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');//muestra los de su sector, mozo puede ver todos
    $group->get('/codigo', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\authMiddleware::class . ':verificarRolMozo');
    $group->post('/estado', \PedidoController::class . ':CambiarEstado')->add(\authMiddleware::class . ':verificarRolPedido');
    $group->get('/mostrar/{mesa_id}', \PedidoController::class . ':MostrarPedido')->add(\authMiddleware::class . ':verificarRolMozo');  

  })->add(\authMiddleware::class . ':verificarToken');



  $app->group('/auth', function (RouteCollectorProxy $group) {
  $group->post('/login', function (Request $request, Response $response) {    
    $parametros = $request->getParsedBody();

    if(isset($parametros['nombre']) && isset($parametros['clave']))
    {
      $nombre = $parametros['nombre'];
      $clave = $parametros['clave'];

      $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre, $clave);

      if($usuario){ 
        $rol = $usuario->trabajo_id;
        $id_usuario = $usuario->id_usuario;
        $datos = array('nombre' => $nombre, 'rol' => $rol, 'id' => $id_usuario);

        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));
      } else {
        $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
      }
    }else{
      $payload = json_encode(array('error' => 'Usuario o contraseña no fueron encontrados'));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

});



$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new MiddlewareLogin()); //llama al middleware

$app->run();
