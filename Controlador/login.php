	
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/Usuario.php';


$_SESSION['carro']="";

$nuevoSingleton = Usuario::singleton_login();

if(isset($_POST['email']))
{
 $email = $_POST['email'];
 $password = $_POST['password'];
 //accedemos al método usuarios y los mostramos
 $usuario = $nuevoSingleton->login_users($email,$password);
 
 //print_r($usuario);
 
 
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