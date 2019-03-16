<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/Html.php';

$idEvento=$_POST['id_evento'];
$urlRetorno=$_POST['url_ref'];

echo Html::cabeceraHtml() . Html::actionBar();

?>

<div style="width: 100%;" id="mainContent">
<form method="post" action="../Controlador/crearTipoEntrada.php">
	<p>Nombre</p>
	<input type="text" name="te_nombre">
	<p>Descripcion</p>
	<input type="text" name="te_descripcion">
	<p>Mostrar Descripcion</p>
	<input type="text" name="te_mostrar_descripcion">
	<p>Cantidad disponible</p>
	<input type="text" name="te_cantidad_disponible">
	<p>Precio</p>
	<input type="text" name="te_precio">
	<p>Canales de venta</p>
	<input type="text" name="te_canales">
	<p>Impuestos</p>
	<input type="text" name="te_impuestos">
	<p>Inicio venta</p>
	<input type="date" name="te_inicio_venta">
	<p>Fin venta</p>
	<input type="date" name="te_fin_venta">
	<p>Visibilidad</p>
	<input type="text" name="te_visibilidad">
	<p>Minimo compra</p>
	<input type="text" name="te_minimo_compra">
	<p>Maximo compra</p>
	<input type="text" name="te_maximo_compra">
	<p>Grupo</p>
	<input type="text" name="te_grupo">
	<p>Estado</p>
	<input type="text" name="te_estado">
	<input type="hidden" name="te_id_evento" value="<?php echo $idEvento;?>">
	<input type="hidden" name="te_url_ref" value="<?php echo $urlRetorno;?>">
	<input type="submit" value="Crear tipo de entrada">
</form>
</div>
