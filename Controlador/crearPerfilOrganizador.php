<?php
require_once '../Modelo/PerfilOrganizador.php';

session_start();

$perfil=new PerfilOrganizador();

$perfil->id=PerfilOrganizador::nuevoIdPerfilOrganizador($_SESSION['idusuario']);

$perfil->idUsuario=$_SESSION['idusuario'];

$perfil->nombre=$_POST['nombre'];
$perfil->descripcion=$_POST['descripcion'];
$perfil->mostrarDescripcion=$_POST['mostrar_descripcion'];
$perfil->website=$_POST['website'];
$perfil->facebook=$_POST['facebook'];
$perfil->twitter=$_POST['twitter'];
$perfil->instagram=$_POST['instagram'];

$perfil->crearPerfilOrganizador();

header("Location:../Vista/gestionarPerfilesOrganizador.php");

?>