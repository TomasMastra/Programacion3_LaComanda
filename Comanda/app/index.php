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

require_once 'middlewares\MiddlewareSocio.php';
require_once 'middlewares\MiddlewareMozo.php';
require_once 'middlewares\MiddlewareLogin.php';
require_once 'middlewares\MiddlewareEmpleado.php';
require_once 'middlewares\MiddlewareAgregarProducto.php';







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
    $group->post('/modificar', \UsuarioController::class . ':ModificarUno'); //OK // User inc OK 
    $group->post('/borrar', \UsuarioController::class . ':BorrarUno');// OK
  })->add(new MiddlewareLogin())->add(new MiddlewareSocio());

  // Solo mozos
  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(new MiddlewareMozo()); //OK lo pueden ver solo mozos/socios
    $group->get('/{nombre}', \MesaController::class . ':TraerUno')->add(new MiddlewareMozo());// OK lo pueden ver mozos/socios
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new MiddlewareMozo());   // OK puede agregar mozo/socio
    $group->post('/estado', \MesaController::class . ':CambiarEstado')->add(new MiddlewareMozo());   // no funciona
  })->add(new MiddlewareLogin());

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');// muestra los de su sector
    $group->get('/{nombre}', \ProductoController::class . ':TraerUno'); // si no es de su secor, no deja
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new MiddlewareAgregarProducto());  // cada uno crea en su sector
    $group->post('/modificar', \ProductoController::class . ':ModificarUno');//ver si dejar
    $group->post('/borrar', \ProductoController::class . ':BorrarUno');//ver si dejar
  })->add(new MiddlewareLogin());

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');//muestra los de su sector, mozo puede ver todos
    $group->get('/{id_pedido}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new MiddlewareMozo()); 
    $group->post('/estado', \PedidoController::class . ':CambiarEstado')->add(new MiddlewareEmpleado());
  })->add(new MiddlewareLogin());



$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new MiddlewareLogin()); //llama al middleware

$app->run();
