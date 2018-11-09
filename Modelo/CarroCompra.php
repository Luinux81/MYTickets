<?php
//require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Evento.php';
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
    
    
    //FORMATO JSON: {numeroLineas:int ,numeroEntradas:int, totalPrecio:float,lineas[{evento:{id,nombre},tipoentrada:{id,nombre,precio},cantidad:int},{...}]}
    public static function getJSON(){
            $numeroLineas=0;
            $numeroEntradas=0;
            $totalPrecio=0;
            $lineas=array();
            
            if(isset($_SESSION['carro'])){
                if($_SESSION['carro']!=""){
                    $carro=json_decode($_SESSION['carro'],true);
                    
                    $numeroLineas=count($carro);
                    for($i=0;$i<count($carro);$i++){
                        
                        $cantidad=$carro[$i]['cantidad'];
                        $idTipoEntrada=$carro[$i]['tipoentrada'];
                        $idEvento=$carro[$i]['evento'];
                        
                        $aux=Evento::getEvento($idEvento);
                        $evento=array("id"=>$idEvento,"nombre"=>$aux->nombre);
                        
                        $aux=TipoEntrada::getTipoEntrada($idEvento, $idTipoEntrada);
                        $tipoEntrada=array("id"=>$idTipoEntrada,"nombre"=>$aux->nombre,"precio"=>$aux->precio);
                        
                        $numeroEntradas+=$cantidad;
                        $totalPrecio+=$cantidad*$aux->precio;
                        $lineas[]=array("evento"=>$evento,"tipoentrada"=>$tipoEntrada,"cantidad"=>$cantidad);
                    }
                }
            }
            
            $res=array("numeroLineas"=>$numeroLineas,"numeroEntradas"=>$numeroEntradas,"totalPrecio"=>$totalPrecio,"lineas"=>$lineas);
            return json_encode($res);
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