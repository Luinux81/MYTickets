<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

$te=new TipoEntrada();

$te->id=$_POST['te_id'];
$te->eventoId=$_POST['te_id_evento'];
$te->nombre=$_POST['te_nombre'];
$te->descripcion=$_POST['te_descripcion'];
$te->mostrar_descripcion=$_POST['te_mostrar_descripcion'];
$te->cantidad_disponible=$_POST['te_cantidad_disponible'];
$te->precio=$_POST['te_precio'];
$te->canales_venta=$_POST['te_canales'];
$te->impuestos=$_POST['te_impuestos'];
$te->inicio_venta=$_POST['te_inicio_venta'];
$te->fin_venta=$_POST['te_fin_venta'];
$te->visibilidad=$_POST['te_visibilidad'];
$te->minimo_compra=$_POST['te_minimo_compra'];
$te->maximo_compra=$_POST['te_maximo_compra'];
$te->grupo=$_POST['te_grupo'];
$te->estado=$_POST['te_estado'];

$te->editarTipoEntrada();

header("Location:".$_POST['te_url_ref']."?eid=".$te->eventoId);

?>
