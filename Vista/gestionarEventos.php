<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Evento.php';

echo Html::actionBar();

$res=Evento::getAllEventos();

$output="<ul>";

foreach ($res as $r){
    $output.="<li><a href='../Vista/editarEvento.php?eid=" . $r->id . "'>" . $r->nombre."</a></li>";
}

$output.="</ul>";

echo $output;
?>
