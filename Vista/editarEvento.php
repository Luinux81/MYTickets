<?php
require_once '../Vista/actionBar.php';
require_once '../Modelo/Evento.php';
require_once '../Modelo/TipoEntrada.php';
require_once '../Modelo/Tool.php';

$eid=$_GET['eid'];

$ev=Evento::getEvento($eid);

echo actionBar::Html();

?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/editarEvento.php">
    	<h3>Detalles del evento</h3>
    	<p>Nombre</p>
    	<input type="text" id="evento_nombre" name="evento_nombre" value="<?php echo trim($ev['Nombre']); ?>">
    	<p>Descripcion</p>
    	<input type="text" id="evento_descripcion" name="evento_descripcion" value="<?php echo trim($ev['Descripcion']); ?>">
    	<p>Fecha Inicio</p>
    	<input type="date" id="evento_fecha_inicio" name="evento_fecha_inicio" value="<?php echo Tool::separaFechaHora($ev['Fecha_inicio'],true); ?>">
    	<p>Hora Inicio</p>
    	<input type="time" id="evento_hora_inicio" name="evento_hora_inicio" value="<?php echo Tool::separaFechaHora($ev['Fecha_inicio'],false); ?>">
    	<p>Fecha Fin</p>
    	<input type="date" id="evento_fecha_fin" name="evento_fecha_fin" value="<?php echo Tool::separaFechaHora($ev['Fecha_fin'],true); ?>">
    	<p>Hora Fin</p>
    	<input type="time" id="evento_hora_fin" name="evento_hora_fin" value="<?php echo Tool::separaFechaHora($ev['Fecha_fin'],false); ?>">
    	<p>Aforo</p>
    	<input type="text" id="evento_aforo" name="evento_aforo" value="<?php echo trim($ev['Aforo']); ?>">
    	<p>Local</p>
    	<input type="text" id="evento_local" name="evento_local" value="<?php echo trim($ev['Local']); ?>">
    	<p>Direccion</p>
    	<input type="text" id="evento_direccion" name="evento_direccion" value="<?php echo trim($ev['Direccion']); ?>">
    	<p>Ciudad</p>
    	<input type="text" id="evento_ciudad" name="evento_ciudad" value="<?php echo trim($ev['Ciudad']); ?>">
    	<p>Pais</p>
    	<input type="text" id="evento_pais" name="evento_pais" value="<?php echo trim($ev['Pais']); ?>">
    	<p>GPS</p>
    	<input type="text" id="evento_gps" name="evento_gps" value="<?php echo trim($ev['GPS']); ?>">
    	<br>
    	<input type="hidden"  name="id" value="<?php echo $ev['Id']; ?>">
    	<input type="submit" value="Editar evento">
    </form>
    
<?php 

$res=TipoEntrada::getAllTipoEntradas($ev['Id']);

echo "<h3>Tipos de entradas</h3><ul>";
foreach ($res as $tp){
    echo "<li>" . $tp['Nombre'] . " Precio:" . $tp['Precio'] 
        . " <a href='../Vista/editarTipoEntrada.php?eid=". $tp['Id_Evento'] ."&tpid=". $tp['Id'] ."'>Editar</a> "
        . " <a href='../Controlador/eliminarTipoEntrada.php?eid=". $tp['Id_Evento'] ."&tpid=". $tp['Id'] ."'>Eliminar</a></li>";
}
echo "</ul>";

?>
    
    <form method="post" action="../Vista/crearTipoEntrada.php">
    	<input type="hidden" name="id_evento" value="<?php echo $ev['Id']; ?>">
    	<input type="hidden" name="url_ref" value="Vista/editarEvento.php">
    	<input type="submit" value="Crear nuevo Tipo de Entrada">
    </form>
</div>
