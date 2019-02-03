<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Evento.php';

echo Html::actionBar();

$res=Evento::getAllEventos();

$output="<ul>";

foreach ($res as $r){
    $output.="<li>" . $r->nombre 
    . " <a href='../Vista/editarEvento.php?eid=" . $r->id . "'>Editar</a> "
    . " <a href='../Vista/listadoEvento.php?eid=" . $r->id . "'>Listado</a> "
        . "</li>";
}

$output.="</ul>";

echo $output;
?>
