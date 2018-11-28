<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Venta.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/GeneradorPDF.php';

//session_start();

$idVenta=$_GET['v'];
$idLineaVenta=$_GET['lv'];
$idUsuario=$_SESSION['usuario']['id'];

$ventas=Venta::getVentasUsuario($idUsuario);
$aux="";

foreach ($ventas as $v){
    if($v->id==$idVenta){
        $aux=$v;
        break;
    }
}

if($aux!=""){
    foreach ($v->lineasVenta as $lv){
        if($lv->id==$idLineaVenta){
            $entradas=Entrada::getEntradasPorLineaVenta($idVenta, $idLineaVenta);
        }
    }    
}

GeneradorPDF::generaPDF($entradas);

?>