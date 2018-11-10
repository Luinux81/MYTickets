<?php
require_once APP_ROOT . '/Modelo/ModeloBD.php';

class Tool
{
    public static function adaptaFechaHora($date,$time){
        return $date . " " . $time;
    }
    
    public static function separaFechaHora($datetime,$fecha=true){
        $aux=substr($datetime,0,10);
        if(!$fecha)
        {
            $aux=substr($datetime,11,5);
        }
        
        return $aux;
    }
    
    public static function conectar(){
        return ModeloBD::getConexion();    
    }
    
    public static function desconectar(&$dbHandler){
        $dbHandler=null;
    }
    
    public static function getToken($longitud,$alfabeto=""){
        $token="";
        if ($alfabeto==""){
            $alfabeto="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            //$alfabeto.="abcdefghijklmnopqrstuvwxyz";
            $alfabeto.="0123456789";
        }
            
        $max=strlen($alfabeto);
            
        for($i=0;$i<$longitud;$i++){
            $token.=$alfabeto[random_int(0, $max-1)];
        }
        
        
        return $token;
    }
}

