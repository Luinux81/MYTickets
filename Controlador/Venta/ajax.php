<?php 
require_once '../../constantes.php';
require_once APP_ROOT . "/Modelo/Venta.php";
require_once APP_ROOT . "/Modelo/LineaVenta.php";
require_once APP_ROOT . "/Modelo/Usuario.php";
require_once APP_ROOT . "/Modelo/Tool.php";
require_once APP_ROOT . "/Modelo/TipoEntrada.php";

switch($_GET['action']){
    
    case "crear":
        $param=$_GET['param'];        
        $json=json_decode($param,true);
        
        crearVenta($json);
        
        break;
        
}


function crearVenta($json){
    $usuario=Usuario::getUsuarioPorEmail($json["usuario"]["email"]);
    
    if(!$usuario){
        if(!Usuario::registroUsuario($json["usuario"]["email"], $json["usuario"]["nombre"], $json["usuario"]["email"],1)){
            $res=array("resultado"=>false,"data"=>"Error registrando nuevo usuario");
        }
        $usuario=Usuario::getUsuarioPorEmail($json["usuario"]["email"]);
    }
    
    $v=new Venta();
    
    $v->id=Venta::getNuevoId();
    $v->idUsuario=$usuario->id;
    $v->fecha=Tool::formatoFechaMysql(date('Y-m-d H:i:s'));
    $v->estado="completado";
    $v->paymentID="venta_manual";
    $v->importe=0;
    
    $idEvento=$json["evento"];
    $i=1;
    
    foreach($json["entradas"] as $entrada){
        $tipo=TipoEntrada::getTipoEntrada($idEvento, $entrada["tipo"]);
        
        $linea=new LineaVenta();
        $linea->id=$i++;
        $linea->idVenta=$v->id;
        $linea->idEvento=$idEvento;
        $linea->idTipoEntrada=$tipo->id;
        $linea->estado="completado";
        $linea->precio=$tipo->precio;
        $linea->cantidad=$entrada["cantidad"];
        
        $v->importe+=$linea->precio*$linea->cantidad;
        $v->lineasVenta[]=$linea;
    }
    
    if($v->crearVenta()){
        $res=array("resultado"=>true,"data"=>$v);
    }
    else{
        $res=array("resultado"=>false,"data"=>"Error registrando nueva venta");
    }
    echo json_encode($res);
}

?>