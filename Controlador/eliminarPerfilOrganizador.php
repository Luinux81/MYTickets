<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';

session_start();

$res=PerfilOrganizador::borrarPerfilOrganizador($_GET['idpo'], $_SESSION['usuario']['id']);

if($res[0]!="00000"){
    print_r($res);
}
else{
    header("Location:../Vista/gestionarPerfilesOrganizador.php");
}

?>