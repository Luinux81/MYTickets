<?php 
require_once '../../constantes.php';
require_once APP_ROOT . '/Modelo/Usuario.php';


$accion=$_GET['action'];
$valor=$_GET['value'];


switch ($accion){
    case "check_user_email":
        if(Usuario::existeEmail($valor)){
            echo json_encode(array("existe"=>1,"usuario"=>Usuario::getUsuarioPorEmail($valor)));            
        }
        else{
            echo json_encode(array("existe"=>0,"usuario"=>""));
        }
        break;
}

?>