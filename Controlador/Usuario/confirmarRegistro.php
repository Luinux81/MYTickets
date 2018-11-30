<?php
require_once '../../constantes.php';
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Usuario.php';


$email=$_GET['email'];
$codigo=$_GET['codigo'];

if(Usuario::activarCuentaUsuario($email, $codigo)){
    header("Location:" . Tool::getBaseURL() ."home.php");
}
else{
    header("Location:" . Tool::getBaseURL() ."home.php?err=Error_activacion_email");
}

?>