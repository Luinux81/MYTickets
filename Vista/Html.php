<?php
require_once APP_ROOT . '/Modelo/CarroCompra.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Tool.php';

class Html{
    
    public static function cabeceraHtml(){
        $aux="<head>"
            . "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.min.js'></script>"
            . "<script type='text/javascript' src='" . APP_URL . "/main.js'></script>"  
            . "<link rel='stylesheet' type='text/css' href='" . APP_URL . "/style.css" . "'>"    
            . "<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'> "
            . "</head>";
        
        return $aux;
    }
    
    public static function actionBar($location="home"){
        $aux="<div style='width:100%;text-align:right;'><ul>";
        $aux.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/home.php'>Home</a></li>";
        $aux.=self::addCarroCompra();
        
        switch($location){
            case "home":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/crearEvento.php'>Crear Evento</a></li>";
                break;
            case "perfilesOrganizador":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/crearPerfilOrganizador.php'>Crear Perfil</a></li>";
                break;
        }
        
        $aux.=self::verInfoLogin()
            ."</ul>"
            ."</div>";
        
        return $aux;
    }
    
    private static function verInfoLogin(){
        $out="";
        
        if(!isset($_SESSION['usuario'])){
            $out="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/index.php'>Log in </a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/Usuario/crearUsuario.php'>Register</a></li>";
        }
        else{
            $out.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/Evento/gestionarEventos.php'>Gestionar Eventos</a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/gestionarPerfilesOrganizador.php'>Gestionar Perfiles de organizador (" . $_SESSION['usuario']['nombre'] .")</a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/Usuario/cambiarPassword.php'>Cambiar password</a></li>";
            $out.=self::addVerEntradas();
            $out.="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Controlador/Usuario/cerrarSesion.php'>Log Out</a></li>";
        }
        
        return $out;
    }
    
    private static function addCarroCompra(){
        $out="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/verCarroCompra.php' id='link_ver_carro'>";
        
        $json=CarroCompra::getJSON();
        if(!empty($json)){
            $json=json_decode($json);
            if($json->numeroLineas!=0){
                $out.="Ver Carro ( " . $json->numeroLineas . " items )";
            }
        }
        
        $out.="</a></li>";
        return $out;
    }
    
    private static function addVerEntradas(){
        $entradas=Entrada::getAllEntradasUsuario($_SESSION['usuario']['id']);
        
        $out="<li style='display:inline;padding-right:10px;'><a href='" . Tool::getBaseURL() . "/Vista/verEntradasCompradas.php' id='link_ver_carro'>";
        if(!empty($entradas)){
            $out.="Ver Entradas ( " . count($entradas) . " )";
        }
        $out.="</a></li>";
        return $out;
    }
}

?>