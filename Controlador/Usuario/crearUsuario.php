<?php
require_once '../../constantes.php';
require_once APP_ROOT . '/Modelo/Usuario.php';

//session_start();

$email=$_POST['email'];
$nombre=$_POST['nombre'];
$pass=$_POST['password'];

if(!Usuario::existeEmail($email)){
    $u=Usuario::singleton_login();
   
    if($u->registroUsuario($email, $nombre, $pass)){
        if($u->login_users($email, $pass)){
            header("Location:../../home.php");
        }
        else{
            header("Location:../../Vista/Usuario/crearUsuario.php?err=Error_login_usuario_" . urlencode(Usuario::$ultimoError));
        }
    }
    else{
        header("Location:../../Vista/Usuario/crearUsuario.php?err=Error_registro_usuario" . urlencode(Usuario::$ultimoError));
    }
    
}
else{
    header("Location:../../Vista/Usuario/crearUsuario.php?err=Existe_email" . urlencode(Usuario::$ultimoError));
}
?>