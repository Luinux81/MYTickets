<?php
require_once '../../constantes.php';

require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Venta.php';
require_once APP_ROOT . '/Modelo/Usuario.php';

$eid=$_GET['eid'];

$ev=Evento::getEvento($eid);

echo Html::cabeceraHtml() . Html::actionBar();

?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/editarEvento.php" enctype="multipart/form-data" accept-charset="utf-8">
    	<h3>Detalles del evento</h3>
    	<p>Nombre</p>
    	<input type="text" id="evento_nombre" name="evento_nombre" value="<?php echo trim($ev->nombre); ?>">
    	<p>Descripcion</p>
    	<input type="text" id="evento_descripcion" name="evento_descripcion" value="<?php echo trim($ev->descripcion); ?>">
    	<p>Fecha Inicio</p>
    	<input type="date" id="evento_fecha_inicio" name="evento_fecha_inicio" value="<?php echo Tool::separaFechaHora($ev->fecha_inicio,true); ?>">
    	<p>Hora Inicio</p>
    	<input type="time" id="evento_hora_inicio" name="evento_hora_inicio" value="<?php echo Tool::separaFechaHora($ev->fecha_inicio,false); ?>">
    	<p>Fecha Fin</p>
    	<input type="date" id="evento_fecha_fin" name="evento_fecha_fin" value="<?php echo Tool::separaFechaHora($ev->fecha_fin,true); ?>">
    	<p>Hora Fin</p>
    	<input type="time" id="evento_hora_fin" name="evento_hora_fin" value="<?php echo Tool::separaFechaHora($ev->fecha_fin,false); ?>">
    	<p>Aforo</p>
    	<input type="text" id="evento_aforo" name="evento_aforo" value="<?php echo trim($ev->aforo); ?>">
    	<p>Local</p>
    	<input type="text" id="evento_local" name="evento_local" value="<?php echo trim($ev->local); ?>">
    	<p>Direccion</p>
    	<input type="text" id="evento_direccion" name="evento_direccion" value="<?php echo trim($ev->direccion); ?>">
    	<p>Ciudad</p>
    	<input type="text" id="evento_ciudad" name="evento_ciudad" value="<?php echo trim($ev->ciudad); ?>">
    	<p>Pais</p>
    	<input type="text" id="evento_pais" name="evento_pais" value="<?php echo trim($ev->pais); ?>">
    	<p>GPS</p>
    	<input type="text" id="evento_gps" name="evento_gps" value="<?php echo trim($ev->gps); ?>">
    	<p>Imagen</p>
    	<input type="file" id="imagen" name="imagen" accept="image/*" style="display:block;">
    	<img id="imagen_preview" height="250px" style="clear:both;" src="data:image/*;base64,<?php echo base64_encode(stripslashes($ev->imagen)); ?>">
    	<br>
    	<input type="hidden"  name="id" value="<?php echo $ev->id; ?>">
    	<input type="submit" value="Editar evento">
    </form>
    
<?php 

$res=TipoEntrada::getAllTipoEntradas($ev->id);

echo "<h3>Tipos de entradas</h3><ul>";
foreach ($res as $tp){
    /*
    echo "<li>" . $tp['Nombre'] . " Precio:" . $tp['Precio'] 
        . " <a href='../Vista/editarTipoEntrada.php?eid=". $tp['Id_Evento'] ."&tpid=". $tp['Id'] ."'>Editar</a> "
        . " <a href='../Controlador/eliminarTipoEntrada.php?eid=". $tp['Id_Evento'] ."&tpid=". $tp['Id'] ."'>Eliminar</a></li>";
    */
    echo "<li>" . $tp->nombre . " Precio:" . $tp->precio
        . " <a href='../Vista/editarTipoEntrada.php?eid=". $tp->eventoId ."&tpid=". $tp->id ."'>Editar</a> "
    	    . " <a href='../Controlador/eliminarTipoEntrada.php?eid=". $tp->eventoId ."&tpid=". $tp->id ."'>Eliminar</a></li>";
}
echo "</ul>";

?>
    
    <form method="post" action="../Vista/crearTipoEntrada.php">
    	<input type="hidden" name="id_evento" value="<?php echo $ev->id; ?>">
    	<input type="hidden" name="url_ref" value="Vista/editarEvento.php">
    	<input type="submit" value="Crear nuevo Tipo de Entrada">
    </form>
</div>
<?php 

echo "<h3>Lista de entradas</h3>";

$aux=Venta::getVentasEvento($ev->id);

echo "<table>" .
        "<tr><th>#</th><th>Id</th><th>Nombre</th><th>Email</th><th>Cantidad</th><th>Fecha</th></tr>";
$i=1;
foreach ($aux as $v){
    $u=Usuario::getUsuario($v['Id_Usuario']);
    echo "<tr><td>" . $i++ . "</td><td><a href='../visualizadorEntradas.php?v=" . $v['Id_Venta'] . "&lv=" . $v['Id'] . "&u=" . $v['Id_Usuario'] . "' target='_blank'>" . $v['Id_Venta'] . "</a></td><td>" . $u->nombre . "</td><td>" . $u->email . "</td><td>" . $v['Cantidad'] . "</td><td>" . $v['Fecha'] . "</td></tr>";
}
echo "</table>";

?>
<h3>Nueva Venta Manual</h3>

<h4>Usuario</h4>

<label for='user_email'>Email</label><input id='user_email' ><span id='user_resultado'></span><br>
<label for='user_nombre'>Nombre</label><input id='user_nombre' >

<h4>Entradas</h4>

<?php 

foreach ($res as $tp){
    echo "<label for='entrada_" . $tp->id . "'>" . $tp->nombre . " " . $tp->precio . "e</label>";
    echo "<input id='entrada_" . $tp->id . "' class='cantidadTipoEntrada' type='number' min='0' value='0'>";
    
}

echo "<input type='hidden' id='evento_id' value='" . $ev->id . "'>";

?>


<br>
<button id='venta_enviar' disabled>Registrar Venta</button>

<script>
$("#imagen").change(function(){
	if(this.files && this.files[0]){
		var reader=new FileReader();
		reader.onload=function(e){
			$("#imagen_preview").attr("src",e.target.result);
		}
		reader.readAsDataURL(this.files[0]);
	}	
});
</script>