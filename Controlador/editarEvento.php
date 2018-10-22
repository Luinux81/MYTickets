<?php
require_once '../Modelo/Evento.php';
require_once '../Modelo/Tool.php';

$id=$_POST['id'];
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

$e=new Evento($nombre, $descripcion, Tool::adaptaFechaHora($fecha_inicio, $hora_inicio), Tool::adaptaFechaHora($fecha_fin, $hora_fin));
$e->id=$id;
$e->aforo=$aforo;
$e->local=$local;
$e->direccion=$direccion;
$e->ciudad=$ciudad;
$e->pais=$pais;
$e->gps=$gps;


$e->editarEventoEnBD();

header("Location:../Vista/gestionarEventos.php");

?>