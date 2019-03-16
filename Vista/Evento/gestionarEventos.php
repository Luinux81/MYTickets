<?php
require_once '../../constantes.php';

require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Evento.php';

echo Html::cabeceraHtml() . Html::actionBar();

$res=Evento::getAllEventos();

$output="<ul id='evento-lista'>";

foreach ($res as $r){
    $output.="<li class='evento-item'>" . $r->nombre 
    . " <a href='./editarEvento.php?eid=" . $r->id . "'>Gestionar</a> "
    . " <a href='./listadoEvento.php?eid=" . $r->id . "' target='_blank'>Listado</a> "
    . "</li>";
}

$output.="</ul>";

echo $output;
?>
