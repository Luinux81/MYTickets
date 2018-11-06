<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';

session_start();


$perfil=new PerfilOrganizador();

$perfil->idUsuario=$_SESSION['idusuario'];
$perfil->id=$_POST['idPerfil'];
$perfil->nombre=$_POST['nombre'];
$perfil->descripcion=$_POST['descripcion'];
$perfil->website=$_POST['website'];
$perfil->facebook=$_POST['facebook'];
$perfil->twitter=$_POST['twitter'];
$perfil->instagram=$_POST['instagram'];
$perfil->mostrarDescripcion=$_POST['mostrar_descripcion'];

$res=$perfil->editarPerfilOrganizador($perfil->id, $perfil->idUsuario);

if ($res[0]!="00000"){
    print_r($res);
}
else{
    header("Location:../Vista/gestionarPerfilesOrganizador.php");
}

?>