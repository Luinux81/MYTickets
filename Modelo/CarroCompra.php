<?php
//require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

/**
 * Clase para gestionar el carro de compra.
 * 
 * Esta clase utiliza una implementaci�n del carro de compra como una variable de sesion.
 * 
 * @author Luis Bre�a Calvo
 *
 */
class CarroCompra{
    
    private static $dbh;
    
    /**
     * La funci�n devuelve si existe un item guardado en el carro de compra correspondiente a un evento y tipo de entrada determinados.
     * 
     * @param int $eid Id de evento
     * @param int $tp Id de tipo de entrada
     * @return boolean True si existe, falso en caso contrario.
     */
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
    
    
    /**
     * Est� funci�n devuelve la cantidad correspondiente a un item guardado en el carro de compra.
     * 
     * Devuelve la cantidad de un item guardado en el carro de compra correspondiente a un evento y tipo de entrada determinados.
     * En caso de no existir el elemento la funci�n devuelve 0.
     * 
     * @param int $eid
     * @param int $tp
     * @return int
     */
    public static function getCantidad($eid,$tp){
        $res=0;
        if(isset($_SESSION['carro'])){
            if($_SESSION['carro']!=""){
                $carro=json_decode($_SESSION['carro'],true);
                $i=0;
                $encontrado=false;
                
                while($i<count($carro) && !$encontrado){
                    if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                        $res=$carro[$i]['cantidad'];
                        $encontrado=true;
                    }
                    else{
                        $i++;
                    }
                }
            }
        }
        
        return $res;
    }
    
    
    /**
     * Devuelve el contenido de la variable de sesi�n en formato JSON
     * 
     * FORMATO JSON: {numeroLineas:int ,numeroEntradas:int, totalPrecio:float,lineas[{evento:{id,nombre},tipoentrada:{id,nombre,precio},cantidad:int},{...}]}
     *  
     * @return string Contenido de la variable de sesi�n en formato JSON
     */
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
    
    
    /**
     * Guarda un nuevo item en la variable de carro de compra con los atributos determinados por los parametros de entrada
     * 
     * Esta funci�n a�ade un nuevo item en caso de que no exista en la variable de sesi�n 
     * o en caso de que el item si existe, se a�ade la cantidad a la ya guardada en la variable.
     * 
     * @param int $eid Id del evento
     * @param int $tp Id del tipo de entrada
     * @param int $cantidad Cantidad de entradas
     */
    public static function addItem($eid,$tp,$cantidad){
        
        if(!isset($_SESSION['carro'])){
            $_SESSION['carro']="";
        }
        
        if($_SESSION['carro']!=""){
            $encontrado=false;
            $carro=json_decode($_SESSION['carro'],true);

            
            for($i=0;$i<count($carro);$i++){
                //Si encontramos el item (evento,tipoentrada) se a�ade la cantidad a la existente
                if($carro[$i]['evento']==$eid && $carro[$i]['tipoentrada']==$tp){
                    $carro[$i]['cantidad']=$cantidad;
                    $encontrado=true;
                    break;
                }
                
            }

            //Si no se encuentra se a�ade una nueva l�nea al carro
            if(!$encontrado){
                $linea=array("evento"=>$eid,"tipoentrada"=>$tp,"cantidad"=>$cantidad);
                $carro[]=$linea;
            }
        }
        else{
            //Si la variable de sesi�n del carro est� vac�a a�adimos la nueva l�nea
            $linea=array("evento"=>$eid,"tipoentrada"=>$tp,"cantidad"=>$cantidad);
            $carro[]=$linea;
        }
        
        //Guardamos el carro modificado en la variable de sesi�n
        $_SESSION['carro']=json_encode(self::limpiaArrayDeVacios($carro));        
    }
    
    /**
     * Esta funci�n modifica la cantidad de un item guardado en la variable de sesi�n determinado por los par�metros de entrada. Si el item no existe no se modifica la variable de sesi�n.
     * 
     * @param int $eid
     * @param int $tp
     * @param int $cantidad
     */
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
    
    /**
     * Esta funci�n elimina un item de la variable de sesi�n determinado por los par�metros de entrada. Si el item no existe no se modifica la variable de sesi�n.
     * 
     * @internal Para la eliminaci�n se fija la cantidad del item a 0 y se invoca la funcion limpiaArrayDeVacios
     * 
     * @param int $eid Id de evento
     * @param int $tp Id de tipo de entrada
     */
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
    
    /**
     * Esta funci�n borra todos los items en la variable de sesi�n.
     */
    public static function vaciarCarro(){
        $_SESSION['carro']="";
    }
    
    /**
     * @access private
     * 
     * Esta funci�n recorre el array de entrada y a�ade al array de salida s�lo los elementos con cantidad mayor que 0
     * 
     * @param array $array
     * @return array
     */
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