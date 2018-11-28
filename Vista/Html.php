<?php
require_once APP_ROOT . '/Modelo/CarroCompra.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Tool.php';

class Html{
    
    public static function cabeceraHtml(){
        $aux="<head>"
            . "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.min.js'></script>"
            . "</head>";
        
        return $aux;
    }
    
    public static function actionBar($location="home"){
        $aux="<div style='width:100%;text-align:right;'><ul>";
        $aux.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/home.php'>Home</a></li>";
        $aux.=self::addCarroCompra();
        
        switch($location){
            case "home":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/crearEvento.php'>Crear Evento</a></li>";
                break;
            case "perfilesOrganizador":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/crearPerfilOrganizador.php'>Crear Perfil</a></li>";
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
            $out="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/index.php'>Log in </a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/Usuario/crearUsuario.php'>Register</a></li>";
        }
        else{
            $out.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/gestionarEventos.php'>Gestionar Eventos</a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/gestionarPerfilesOrganizador.php'>Gestionar Perfiles de organizador (" . $_SESSION['usuario']['nombre'] .")</a></li>";
            $out.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/Usuario/cambiarPassword.php'>Cambiar password</a></li>";
            $out.=self::addVerEntradas();
            $out.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Controlador/Usuario/cerrarSesion.php'>Log Out</a></li>";
        }
        
        return $out;
    }
    
    private static function addCarroCompra(){
        $out="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/verCarroCompra.php' id='link_ver_carro'>";
        
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
        
        $out="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/verEntradasCompradas.php' id='link_ver_carro'>";
        if(!empty($entradas)){
            $out.="Ver Entradas ( " . count($entradas) . " )";
        }
        $out.="</a></li>";
        return $out;
    }
}

?>