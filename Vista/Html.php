<?php
//require_once '/mytickets_dev/constantes.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';

session_start();

class Html{
    
    public static function cabeceraHtml(){
        $aux="<head>"
            . "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.min.js'></script>"
            . "</head>";
        
        return $aux;
    }
    
    public static function actionBar($location="home"){
        $aux="<div style='width:100%;text-align:right;'><ul>";
        switch($location){
            case "home":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/home.php'>Home</a></li>"
                    ."<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/crearEvento.php'>Crear Evento</a></li>"
                    ."<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/gestionarEventos.php'>Gestionar Eventos</a></li>"
                    ."<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/gestionarPerfilesOrganizador.php'>Gestionar Perfiles de organizador (" . $_SESSION['nombre'] .")</a></li>"
                    ;
                break;
            case "perfilesOrganizador":
                $aux.="<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/home.php'>Home</a></li>"
                    ."<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/crearPerfilOrganizador.php'>Crear Perfil</a></li>"
                    ."<li style='display:inline;padding-right:10px;'><a href='/mytickets_dev/Vista/gestionarPerfilesOrganizador.php'>Gestionar Perfiles de organizador (" . $_SESSION['nombre'] .")</a></li>"
                    ;
                break;
        }
        
        $aux.=self::addCarroCompra()
                ."</ul>"
                ."</div>";
        
        return $aux;
    }
    
    private static function addCarroCompra(){
        $out="<li style='display:inline;padding-right:10px;'><a href='' id='link_ver_carro'>";
        if(CarroCompra::getCountLineas()>0){
            $out.="Ver Carro ( " . CarroCompra::getCountLineas() . " items )";
        }
        $out.="</a></li>";
        return $out;
    }
}

?>