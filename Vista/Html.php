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
        $aux="<div id='menu-div'>
                <ul class='nav'>
                <li><a href='" . Tool::getBaseURL() . "/home.php'>Home</a></li>";
        
        $aux.=self::addCarroCompra();
        
        switch($location){
            case "home":
                $aux.="<li><a href='" . Tool::getBaseURL() . "/Vista/crearEvento.php'>Crear Evento</a></li>";
                break;
            case "perfilesOrganizador":
                $aux.="<li><a href='" . Tool::getBaseURL() . "/Vista/crearPerfilOrganizador.php'>Crear Perfil</a></li>";
                break;
        }
        
        $aux.=self::verInfoLogin($location)
        
            ."</ul>"
            ."</div>";
        
        return $aux;
    }
    
    private static function verInfoLogin(){
        $out="";
        
        if(!isset($_SESSION['usuario'])){
            //Menu sin usuario logueado
            $out="<li><a href='" . Tool::getBaseURL() . "/index.php'>Log in </a></li>";
            $out.="<li><a href='" . Tool::getBaseURL() . "/Vista/Usuario/crearUsuario.php'>Register</a></li>";
        }
        else{
            //Menu con usuario logeado
            $out.="
                    <li>
                        <a href=''>Acciones</a>
                        <ul>
                            <li><a href='" . Tool::getBaseURL() . "/Vista/Evento/gestionarEventos.php'>Gestionar Eventos</a></li>
                            <li><a href='" . Tool::getBaseURL() . "/Vista/gestionarPerfilesOrganizador.php'>Gestionar Perfiles de organizador (" . $_SESSION['usuario']['nombre'] .")</a></li>
                            <li><a href='" . Tool::getBaseURL() . "/Vista/Usuario/cambiarPassword.php'>Cambiar password</a></li>
                            " . self::addVerEntradas() . "
                            <li><a href='" . Tool::getBaseURL() . "/Controlador/Usuario/cerrarSesion.php'>Log Out</a></li>
                        </ul>
                    </li>
                    
                ";
        }
        
        return $out;
    }
    
    private static function addCarroCompra(){
        $out="";
        
        $json=CarroCompra::getJSON();
        if(!empty($json)){
            $json=json_decode($json);
            if($json->numeroLineas!=0){
                $out="  <li>
                            <a href='" . Tool::getBaseURL() . "/Vista/verCarroCompra.php' id='link_ver_carro'>Ver Carro ( " . $json->numeroLineas . " items )</a>
                        </li>";
            }
        }
        
        return $out;
    }
    
    private static function addVerEntradas(){
        $entradas=Entrada::getAllEntradasUsuario($_SESSION['usuario']['id']);
        $out="";
        
        if(!empty($entradas)){
            $out="  <li>
                        <a href='" . Tool::getBaseURL() . "/Vista/verEntradasCompradas.php' id='link_ver_carro'>Ver Entradas ( " . count($entradas) . " )</a>
                    </li>";
        }
        
        return $out;
    }
}

?>