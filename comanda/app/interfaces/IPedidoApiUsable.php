<?php
interface IPedidoApiUsable
{
	public function TraerUno($request, $response, $args);
	public function TraerTodos($request, $response, $args);
	public function CargarUno($request, $response, $args);
	public function CambiarEstado($request, $response, $args);
	public function MostrarPedido($request, $response, $args);


}
