<?php
require_once 'constantes.php';

require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/Usuario.php';

echo Html::actionBar();

$res=Evento::getAllEventos();

$out="<ul>";

foreach($res as $ev){
    $out .="<li><a href='Vista/verEvento.php?eid=" . $ev->id . "'>" . $ev->nombre . "</a></li>";    
}
$out.="</ul>";

echo $out;

//echo Usuario::getHash("luis");
?>