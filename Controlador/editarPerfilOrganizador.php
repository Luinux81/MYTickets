<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';

session_start();


$perfil=new PerfilOrganizador();

$perfil->idUsuario=$_SESSION['usuario']['id'];
$perfil->id=$_POST['idPerfil'];
$perfil->nombre=$_POST['nombre'];
$perfil->descripcion=$_POST['descripcion'];
$perfil->website=$_POST['website'];
$perfil->facebook=$_POST['facebook'];
$perfil->twitter=$_POST['twitter'];
$perfil->instagram=$_POST['instagram'];
$perfil->mostrarDescripcion=$_POST['mostrar_descripcion'];

if($_FILES['imagen']['error']===0){
    $perfil->imagen=addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
}

$res=$perfil->editarPerfilOrganizador($perfil->id, $perfil->idUsuario);

if ($res[0]!="00000"){
    print_r($res);
}
else{
    header("Location:../Vista/gestionarPerfilesOrganizador.php");
}

?>