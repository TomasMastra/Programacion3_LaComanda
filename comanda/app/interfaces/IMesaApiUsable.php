<?php
interface IMesaApiUsable
{
	public function TraerUno($request, $response, $args);
	public function TraerTodos($request, $response, $args);
	public function CargarUno($request, $response, $args);
	public function CambiarEstado($request, $response, $args);
	public function AgregarResenia($request, $response, $args);
	public function CargarDesdeCSV($request, $response, $args);
	public function GuardarEnCSV($request, $response, $args);

}
