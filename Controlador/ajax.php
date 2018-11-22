<?php
require_once '../constantes.php';
require_once APP_ROOT . "/Modelo/CarroCompra.php";

$accion=$_GET['accion'];

switch($accion){
    case "add":        
        $eid=$_GET['eid'];
        $tp=$_GET['tp'];
        $cantidad=$_GET['cantidad'];
        CarroCompra::addItem($eid, $tp, $cantidad);
        echo CarroCompra::getJSON();
        break;
        
    case "limpiar":
        CarroCompra::vaciarCarro();
        break;
        
    case "getJSON":
        echo CarroCompra::getJSON();
        break;
}

?>