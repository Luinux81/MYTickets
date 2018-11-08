<?php
//require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

class CarroCompra{
    
    private static $dbh;
    
    public static function existeItem($eid,$tp){
        $encontrado=false;
        if($_SESSION['carro']!=""){
            $carro=json_decode($_SESSION['carro'],true);
            for($i=0;$i<count($carro);$i++){
                if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                    $encontrado=true;
                    break;
                }
            }
        }        
        return $encontrado;
    }
    
    public static function getCountLineas(){               
        if(!isset($_SESSION['carro'])){
            return 0;    
        }
        else{
            if($_SESSION['carro']!=""){
                $carro=json_decode($_SESSION['carro'],true);
                return count($carro);
            }
            else{
                return 0;
            }
        }
    }
    
    public static function getCountEntradas(){
        $res=0;
        if(isset($_SESSION['carro'])){
            if($_SESSION['carro']!=""){
                $carro=json_decode($_SESSION['carro'],true);
                for($i=0;$i<count($carro);$i++){
                    $res+=$carro[$i]['cantidad'];
                }
            }
        }
        
        return $res;
    }
    
    public static function getValorTotal(){
        $res=0;
        if(isset($_SESSION['carro'])){
            if($_SESSION['carro']!=""){
                $carro=json_decode($_SESSION['carro'],true);
                for($i=0;$i<count($carro);$i++){
                    $res+=$carro[$i]['cantidad']*(TipoEntrada::getTipoEntrada($carro[$i]['evento'], $carro[$i]['tipoentrada']))->precio;
                }
            }
        }
        
        return $res;
    }
    
    public static function getHTMLAllItems(){
        
        if(!isset($_SESSION['carro'])){
            $_SESSION['carro']="";
        }
        
        $out="<ul>";
        
        if($_SESSION['carro']!=""){
            $carro=json_decode($_SESSION['carro'],true);
            
            for($i=0;$i<count($carro);$i++){
                $out.="<li>Id Evento:". $carro[$i]['evento'] . " ID TP:" . $carro[$i]['tipoentrada'] . " Cantidad:" . $carro[$i]['cantidad'] . "</li>";
            }
        }
        
        $out.="</ul>";
        
        //$out.="Variable sesion carro: " . $_SESSION['carro'];
        
        return $out;
    }
    
    public static function addItem($eid,$tp,$cantidad){
        
        if(!isset($_SESSION['carro'])){
            $_SESSION['carro']="";
        }
        
        if($_SESSION['carro']!=""){
            $encontrado=false;
            $carro=json_decode($_SESSION['carro'],true);

            
            for($i=0;$i<count($carro);$i++){
                //Si encontramos el item (evento,tipoentrada) se añade la cantidad a la existente
                if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                    $carro[$i]['cantidad']=$cantidad;
                    $encontrado=true;
                    break;
                }
                
            }

            //Si no se encuentra se añade una nueva línea al carro
            if(!$encontrado){
                $linea=array("evento"=>$eid,"tipoentrada"=>$tp,"cantidad"=>$cantidad);
                $carro[]=$linea;
            }
        }
        else{
            //Si la variable de sesión del carro está vacía añadimos la nueva línea
            $linea=array("evento"=>$eid,"tipoentrada"=>$tp,"cantidad"=>$cantidad);
            $carro[]=$linea;
        }
        
        //Guardamos el carro modificado en la variable de sesión
        $_SESSION['carro']=json_encode(self::limpiaArrayDeVacios($carro));        
    }
    
    public static function editarCarro($eid,$tp,$cantidad){
        if($_SESSION['carro']!=""){
            $carro=json_decode($_SESSION['carro'],true);
            for($i=0;$i<count($carro);$i++){
                if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                    $carro[$i]['cantidad']=$cantidad;
                    break;
                }
            }
            $_SESSION['carro']=json_encode(self::limpiaArrayDeVacios($carro));
        }
    }
    
    public static function eliminarItemCarro($eid,$tp){
        if($_SESSION['carro']!=""){
            $carro=json_decode($_SESSION['carro'],true);
            for($i=0;$i<count($carro);$i++){
                if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                    $carro[$i]['cantidad']=0;
                    break;
                }
            }
            $_SESSION['carro']=json_encode(self::limpiaArrayDeVacios($carro));
        }
    }
    
    public static function vaciarCarro(){
        $_SESSION['carro']="";
    }
    
    private static function limpiaArrayDeVacios($array){
        $res=array();
        foreach($array as $a){
            if($a['cantidad']>0){
                $res[]=$a;
            }
        }
        
        return $res;
    }
}