<?php
require_once '../constantes.php';

include_once APP_ROOT . '/Modelo/Evento.php';
include_once APP_ROOT . '/Vista/Html.php';

$eid=$_GET['eid'];

$ev=Evento::getEvento($eid);

echo Html::actionBar();

echo "<h2>" . $ev->nombre . "</h2>";

echo "<h3>Descripción</h3>" .
        "<p>" . $ev->descripcion . "</p>" .
        "<h3>Fecha y hora</h3>" .
        "<p>" . $ev->fecha_inicio . "</p>" .
        "<p>" . $ev->fecha_fin . "</p>" .
        "<h3>Lugar</h3>" .
        "<p>". $ev->local . "</br>" . $ev->direccion . "</br>" . $ev->ciudad . "</p>" .
        "<h3>Imagen</h3>" .
        "<img src='data:image/*;base64," . base64_encode(stripslashes($ev->imagen)) . "' height='250px'/><br>" .
        "<a href='../Vista/verTiendaTickets.php?eid=" . $ev->id . "'>Tickets</a>";

?>