<?php

$email=$_GET['email'];
$codigo=$_GET['codigo'];

if(Usuario::activarCuentaUsuario($email, $codigo)){
    header("Location:../../home.php");
}
else{
    header("Location:../../home.php?err=Error_activacion_email");
}

?>