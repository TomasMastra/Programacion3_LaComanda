<?php
require_once './models/Producto.php';
require_once './interfaces/IProductoApiUsable.php';

class ProductoController extends Producto implements IProductoApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre']) && isset($parametros['precio']) && isset($parametros['trabajo_id']))
        {
            $nombre = $parametros['nombre'];
            $precio = $parametros['precio'];
            $trabajo_id = $parametros['trabajo_id'];

            $producto = new Producto();
            $producto->nombre = $nombre;
            $producto->precio = $precio;
            $producto->trabajo_id = $trabajo_id;
            $producto->tiempo_estimado = 15;
            $resultado = $producto->crearProducto();


            if($resultado == false)
            {
                $payload = json_encode(array("mensaje" => "Producto: {$producto->nombre } no se pudo crear"));
            }else{
                $payload = json_encode(array("mensaje" => "Producto: {$producto->nombre } creado con éxito"));
            }

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        // En caso de no realizarse la carga del producto
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(400); 
    }

    public function TraerUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre_producto = $args['nombre'];            

       // $usuario = Usuario::obtenerUsuarioPorNombreClave($nombre, $clave);
        $producto = Producto::obtenerProducto($nombre_producto);

       
            $payload = json_encode($producto);
            
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        

        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(400); 
    }

    public function TraerTodos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
 
        //$nombre_login = isset($parametros['nombre_login']) ? $parametros['nombre_login'] : null;
        //$clave_login = isset($parametros['clave_login']) ? $parametros['clave_login'] : null;

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        AutentificadorJWT::VerificarToken($token);
        $data = AutentificadorJWT::ObtenerData($token);
    
        $lista = Producto::obtenerTodos($data->rol);
    
        if ($lista !== false) {
            $payload = json_encode(array("listaProducto" => $lista));
        } else {
            $payload = json_encode(array("mensaje" => "Error al obtener la lista de productos"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id_producto'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $trabajo_id = $parametros['trabajo_id'];

        $resultado = Producto::modificarProducto($id, $nombre, $precio, $trabajo_id);

        if ($resultado) {
            $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Producto no se pudo modificar, asegúrese de que el producto exista y/o haya ingresado un ID correcto"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }




}
