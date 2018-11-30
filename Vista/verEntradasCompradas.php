<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Venta.php';

echo Html::cabeceraHtml() . Html::actionBar();

//$entradas=Entrada::getAllEntradasUsuario($_SESSION['usuario']['id']);
$ventas=Venta::getVentasUsuario($_SESSION['usuario']['id']);

$out="<ul>";

foreach($ventas as $v){
    $aux="";
    $primero=true;
    
    $out.="<li>";
    foreach ($v->lineasVenta as $linea){
        $eventoActual=$linea->getEvento()->nombre;
        if($aux!=$eventoActual){
            if($primero){
                $primero=false;
            }
            else{
                $out.="</ul>";
            }
            $aux=$eventoActual;
            $out.="<ul>" . $aux;
        }
        $out.="<li><a href='./visualizadorEntradas.php?v=" . $v->id . "&lv=" . $linea->id ."' >" . $linea->getTipoEntrada()->nombre . " (" . $linea->cantidad . ")</a>   "; 
        $out.="<a href='../Controlador/enviarEntradas.php?v=" . $v->id . "&lv=" . $linea->id ."' >Enviar email</a></li>";
        
    }
    $out.="</ul></li>";
}

$out.="</ul>";

echo $out;

?>