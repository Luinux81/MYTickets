	
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/Usuario.php';


$nuevoSingleton = Usuario::singleton_login();

if(isset($_POST['nick']))
{
 $nick = $_POST['nick'];
 $password = $_POST['password'];
 //accedemos al método usuarios y los mostramos
 $usuario = $nuevoSingleton->login_users($nick,$password);
 
 
 
 if($usuario == TRUE)
 {
    header("Location:../home.php");
 }
 else
 {
     header("Location:../index.php");
 }
}
?>