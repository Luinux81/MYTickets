	
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/Usuario.php';


//$_SESSION['carro']="";

$nuevoSingleton = Usuario::singleton_login();

if(isset($_POST['email']))
{
 $email = $_POST['email'];
 $password = $_POST['password'];
 
 if($_POST['url']!=""){
     $urlRetorno=urldecode($_POST['url']);
 }
 else{
     $urlRetorno="";
 }
 
 
 //accedemos al método usuarios y los mostramos
 $usuario = $nuevoSingleton->login_users($email,$password);
 
 //print_r($usuario);
 
 
 if($usuario == TRUE)
 {
     if($urlRetorno==""){
         header("Location:../home.php");
     }
     else{
         header("Location:" . $urlRetorno);
         //print_r($urlRetorno);
     }
 }
 else
 {
     header("Location:../index.php");
 }
 
}
?>