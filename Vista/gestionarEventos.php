<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Vista/actionBar.php';
require_once APP_ROOT . '/Modelo/Evento.php';

echo actionBar::Html();

$res=Evento::getAllEventos();

$output="<ul>";

foreach ($res as $r){
    $output.="<li><a href='../Vista/editarEvento.php?eid=" . $r['Id'] . "'>" . $r['Nombre']."</a></li>";
}

$output.="</ul>";

echo $output;
?>
