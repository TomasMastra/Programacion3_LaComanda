<?php
interface IProductoApiUsable
{
	public function TraerUno($request, $response, $args);
	public function TraerTodos($request, $response, $args);
	public function CargarUno($request, $response, $args);
	public function ModificarUno($request, $response, $args);

}
