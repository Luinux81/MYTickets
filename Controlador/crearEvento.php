<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Evento.php';

//captura de parametros
$nombre=$_POST['evento_nombre'];
$descripcion=$_POST['evento_descripcion'];
$fecha_inicio=$_POST['evento_fecha_inicio'];
$hora_inicio=$_POST['evento_hora_inicio'];
$fecha_fin=$_POST['evento_fecha_fin'];
$hora_fin=$_POST['evento_hora_fin'];
$aforo=$_POST['evento_aforo'];
$local=$_POST['evento_local'];
$direccion=$_POST['evento_direccion'];
$ciudad=$_POST['evento_ciudad'];
$pais=$_POST['evento_pais'];
$gps=$_POST['evento_gps'];

$ev=new Evento($nombre, $descripcion, $fecha_inicio, $fecha_fin);
$ev->hora_inicio=$hora_inicio;
$ev->hora_fin=$hora_fin;
$ev->aforo=$aforo;
$ev->local=$local;
$ev->direccion=$direccion;
$ev->ciudad=$ciudad;
$ev->pais=$pais;
$ev->gps=$gps;

if($_FILES['imagen']['error']===0){
    $ev->imagen=addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
}

$ev->guardarEventoEnBD();

header("Location:../Vista/gestionarEventos.php")

?>