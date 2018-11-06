<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

$nombre=$_POST['te_nombre'];
$descripcion=$_POST['te_descripcion'];
$mostrar_descripcion=$_POST['te_mostrar_descripcion'];
$cantidad_disponible=$_POST['te_cantidad_disponible'];
$precio=$_POST['te_precio'];
$canales=$_POST['te_canales'];
$impuestos=$_POST['te_impuestos'];
$inicio_venta=$_POST['te_inicio_venta'];
$fin_venta=$_POST['te_fin_venta'];
$visibilidad=$_POST['te_visibilidad'];
$minimo_compra=$_POST['te_minimo_compra'];
$maximo_compra=$_POST['te_maximo_compra'];
$grupo=$_POST['te_grupo'];
$estado=$_POST['te_estado'];
$idevento=$_POST['te_id_evento'];
$url_retorno=$_POST['te_url_ref'];

$tp=new TipoEntrada();
$tp->id=TipoEntrada::nuevoIdTipoEntrada($idevento);
$tp->eventoId=$idevento;
$tp->nombre=$nombre;
$tp->descripcion=$descripcion;
$tp->mostrar_descripcion=$mostrar_descripcion;
$tp->cantidad_disponible=$cantidad_disponible;
$tp->precio=$precio;
$tp->canales_venta=$canales;
$tp->impuestos=$impuestos;
$tp->inicio_venta=$inicio_venta;
$tp->fin_venta=$fin_venta;
$tp->visibilidad=$visibilidad;
$tp->minimo_compra=$minimo_compra;
$tp->maximo_compra=$maximo_compra;
$tp->grupo=$grupo;
$tp->estado=$estado;

$tp->crearTipoEntrada();

header("Location:../".$url_retorno."?eid=".$idevento);

?>