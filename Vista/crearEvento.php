<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/actionBar.php';

echo actionBar::Html();
?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/crearEvento.php">
    	<h3>Detalles del evento</h3>
    	<p>Nombre</p>
    	<input type="text" id="evento_nombre" name="evento_nombre">
    	<p>Descripcion</p>
    	<input type="text" id="evento_descripcion" name="evento_descripcion">
    	<p>Fecha Inicio</p>
    	<input type="date" id="evento_fecha_inicio" name="evento_fecha_inicio">
    	<p>Hora Inicio</p>
    	<input type="time" id="evento_hora_inicio" name="evento_hora_inicio">
    	<p>Fecha Fin</p>
    	<input type="date" id="evento_fecha_fin" name="evento_fecha_fin">
    	<p>Hora Fin</p>
    	<input type="time" id="evento_hora_fin" name="evento_hora_fin">
    	<p>Aforo</p>
    	<input type="number" id="evento_aforo" name="evento_aforo">
    	<p>Local</p>
    	<input type="text" id="evento_local" name="evento_local">
    	<p>Direccion</p>
    	<input type="text" id="evento_direccion" name="evento_direccion">
    	<p>Ciudad</p>
    	<input type="text" id="evento_ciudad" name="evento_ciudad">
    	<p>Pais</p>
    	<input type="text" id="evento_pais" name="evento_pais">
    	<p>GPS</p>
    	<input type="text" id="evento_gps" name="evento_gps">
    	<br>
    	<input type="submit" value="Crear evento">
    </form>
</div>