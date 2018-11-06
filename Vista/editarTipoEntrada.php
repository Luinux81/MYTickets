<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/actionBar.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

$idTp=$_GET['tpid'];
$idEvento=$_GET['eid'];
$urlRetorno="../Vista/editarEvento.php";

$tp=TipoEntrada::getTipoEntrada($idEvento, $idTp);

echo actionBar::Html();
?>
<div style="width: 100%;" id="mainContent">
<form method="post" action="../Controlador/editarTipoEntrada.php">
	<p>Nombre</p>
	<input type="text" name="te_nombre" value="<?php echo $tp->nombre; ?>">
	<p>Descripcion</p>
	<input type="text" name="te_descripcion" value="<?php echo $tp->descripcion;?>">
	<p>Mostrar Descripcion</p>
	<input type="text" name="te_mostrar_descripcion" value="<?php echo $tp->mostrar_descripcion;?>">
	<p>Cantidad disponible</p>
	<input type="text" name="te_cantidad_disponible" value="<?php echo $tp->cantidad_disponible;?>">
	<p>Precio</p>
	<input type="text" name="te_precio" value="<?php echo $tp->precio;?>">
	<p>Canales de venta</p>
	<input type="text" name="te_canales" value="<?php echo $tp->canales_venta;?>">
	<p>Impuestos</p>
	<input type="text" name="te_impuestos" value="<?php echo $tp->impuestos;?>">
	<p>Inicio venta</p>
	<input type="date" name="te_inicio_venta" value="<?php echo $tp->inicio_venta;?>">
	<p>Fin venta</p>
	<input type="date" name="te_fin_venta" value="<?php echo $tp->fin_venta;?>">
	<p>Visibilidad</p>
	<input type="text" name="te_visibilidad" value="<?php echo $tp->visibilidad;?>">
	<p>Minimo compra</p>
	<input type="text" name="te_minimo_compra" value="<?php echo $tp->minimo_compra;?>">
	<p>Maximo compra</p>
	<input type="text" name="te_maximo_compra" value="<?php echo $tp->maximo_compra; ?>">
	<p>Grupo</p>
	<input type="text" name="te_grupo" value="<?php echo $tp->grupo;?>">
	<p>Estado</p>
	<input type="text" name="te_estado" value="<?php echo $tp->estado;?>">
	
	<input type="hidden" name="te_id" value="<?php echo $tp->id;?>">
	<input type="hidden" name="te_id_evento" value="<?php echo $tp->eventoId;?>">
	<input type="hidden" name="te_url_ref" value="<?php echo $urlRetorno?>">
	<input type="submit" value="Editar tipo de entrada">
</form>
</div>
