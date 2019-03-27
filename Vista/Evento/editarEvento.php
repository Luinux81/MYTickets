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

$entradas=Entrada::getAllEntradasEvento($eid);

echo Html::cabeceraHtml() . Html::actionBar();

echo Html::menuGestionEvento();
?>

<main>
<div id='mainContent'>

	<div id="evento-panelcontrol-top-div">
		
		<div id="evento-panelcontrol-top-item1" class="evento-panelcontrol-top-item seccion-info">			
			<i class="evento-panelcontrol-top-icon fas fa-globe-americas"></i>
			<div>
				<h4>Estado</h4>
				<p>Tu evento está <?php echo $ev->estado?>.</p>
			</div>
		</div>
		
		<div id="evento-panelcontrol-top-item2" class="evento-panelcontrol-top-item seccion-info">
			<i class="evento-panelcontrol-top-icon fas fa-chart-line"></i>
			<div>
				<h4>Ventas</h4>
				<p>El total de ventas es <?php echo Evento::getRecaudacionTotal($ev->id)." €";?></p>
			</div>
		</div>
		<div id="evento-panelcontrol-top-item3" class="evento-panelcontrol-top-item seccion-info">
			<i class="evento-panelcontrol-top-icon fas fa-receipt"></i>
			<div>
				<h4>Entradas</h4>
				<p>Entradas vendidas: <?php echo count($entradas);?> / <?php echo trim($ev->aforo); ?></p>
				<div class="progressBarBG">
					<div class="progressBarProgress" style="width:<?php echo count($entradas)*100/$ev->aforo?>%" ></div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="evento-editar-div" class="seccion-info">
	
		<section id="seccion-editar-evento-detalle">
		<header>
			<h3 class="seccion-cabecera-linea"><span class="seccion-cabecera-numero">1</span> Detalles del evento</h3>
		</header>
		<div class="seccion-info-innerdiv">
        	<form method="post" action="../../Controlador/editarEvento.php" enctype="multipart/form-data" accept-charset="utf-8">
        		<div>
                	<label for="evento_nombre">Nombre</label>
                	<input type="text" id="evento_nombre" name="evento_nombre" value="<?php echo trim($ev->nombre); ?>">
                	
                	<label for="evento_descripcion">Descripción</label>
                	<input type="text" id="evento_descripcion" name="evento_descripcion" value="<?php echo trim($ev->descripcion); ?>">

                	<label for="evento_fecha_inicio">Fecha Inicio</label>
                	<input type="date" id="evento_fecha_inicio" name="evento_fecha_inicio" value="<?php echo Tool::separaFechaHora($ev->fecha_inicio,true); ?>">

                	<label for="evento_hora_inicio">Hora Inicio</label>
                	<input type="time" id="evento_hora_inicio" name="evento_hora_inicio" value="<?php echo Tool::separaFechaHora($ev->fecha_inicio,false); ?>">

                	<label for="evento_fecha_fin">Fecha Fin</label>
                	<input type="date" id="evento_fecha_fin" name="evento_fecha_fin" value="<?php echo Tool::separaFechaHora($ev->fecha_fin,true); ?>">

                	<label for="evento_hora_fin">Hora Fin</label>
                	<input type="time" id="evento_hora_fin" name="evento_hora_fin" value="<?php echo Tool::separaFechaHora($ev->fecha_fin,false); ?>">

                	<label for="evento_aforo">Aforo</label>
                	<input type="text" id="evento_aforo" name="evento_aforo" value="<?php echo trim($ev->aforo); ?>">

                	<label for="evento_local">Local</label>
                	<input type="text" id="evento_local" name="evento_local" value="<?php echo trim($ev->local); ?>">

                	<label for="evento_direccion">Direccion</label>
                	<input type="text" id="evento_direccion" name="evento_direccion" value="<?php echo trim($ev->direccion); ?>">

                	<label for="evento_ciudad">Ciudad</label>
                	<input type="text" id="evento_ciudad" name="evento_ciudad" value="<?php echo trim($ev->ciudad); ?>">

                	<label for="evento_pais">Pais</label>
                	<input type="text" id="evento_pais" name="evento_pais" value="<?php echo trim($ev->pais); ?>">

                	<label for="evento_gps">GPS</label>
                	<input type="text" id="evento_gps" name="evento_gps" value="<?php echo trim($ev->gps); ?>">
            	</div>
            	<div>
                	<p>Imagen</p>
                	<input type="file" id="imagen" name="imagen" accept="image/*" style="display:block;">
                	<img id="imagen_preview" height="250px" style="clear:both;" src="data:image/*;base64,<?php echo base64_encode(stripslashes($ev->imagen)); ?>">
                	<br>
                	<input type="hidden"  id="evento_id"name="id" value="<?php echo $ev->id; ?>">
            	</div>
            	<div class="seccion-footer">
            		<input type="submit" id="boton-editar-evento" value="Editar evento" class="boton">
        		</div>
            </form>
        </div>
        </section>
        
    
<?php 

$res=TipoEntrada::getAllTipoEntradas($ev->id);

$aux="<section id='seccion-editar-evento-tipos'>
        <header><h3 class='seccion-cabecera-linea'><span class='seccion-cabecera-numero'>2</span>Tipos de entradas</h3></header>
        <div class='seccion-info-innerdiv'>
        <ul>";

foreach ($res as $tp){
    $aux.="<li>" . $tp->nombre . " Precio:" . $tp->precio
        . "<div>
                <a href='../../Vista/editarTipoEntrada.php?eid=". $tp->eventoId ."&tpid=". $tp->id ."'><i class='fas fa-wrench evento-panelcontrol-top-icon'></i></a> "
        .     " <a href='../../Controlador/eliminarTipoEntrada.php?eid=". $tp->eventoId ."&tpid=". $tp->id ."'><i class='fas fa-trash-alt evento-panelcontrol-top-icon'></i></a>
           </div>
           </li>";
}
$aux.= "</ul>

        <form method='post' action='../../Vista/crearTipoEntrada.php'>
	       <input type='hidden' name='id_evento' value='<?php echo $ev->id; ?>'>
	       <input type='hidden' name='url_ref' value='Vista/editarEvento.php'>
            <div class='seccion-footer'>
	           <input type='submit' value='Crear nuevo Tipo de Entrada' class='boton'>
            </div>
	    </form>
        </div>
    </section>
";

echo $aux;
?>
    
</div>


<?php 

echo " <div id='evento-listado-div' class='seccion-info'>
        <section>
        <header><h3>Lista de entradas</h3></header>";

$aux=Venta::getVentasEvento($ev->id);

echo "  <table>" .
        "<tr><th>#</th><th>Id</th><th>Nombre</th><th>Email</th><th>Cantidad</th><th>Fecha</th></tr>";
$i=1;
foreach ($aux as $v){
    $u=Usuario::getUsuario($v['Id_Usuario']);
    echo "<tr><td>" . $i++ . "</td><td><a href='../visualizadorEntradas.php?v=" . $v['Id_Venta'] . "&lv=" . $v['Id'] . "&u=" . $v['Id_Usuario'] . "' target='_blank'>" . $v['Id_Venta'] . "</a></td><td>" . $u->nombre . "</td><td>" . $u->email . "</td><td>" . $v['Cantidad'] . "</td><td>" . $v['Fecha'] . "</td></tr>";
}
echo "  </table>
        </section>
      </div>
    ";

?>
    <div id="seccion-evento-nuevaventa" class="seccion-info">
    <section>
    <header><h3>Nueva Venta Manual</h3></header>
    
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
    <div class="seccion-footer">
    	<button id='venta_enviar' disabled class="boton">Registrar Venta</button>
    </div>
    </section>
	</div>
</div>
</main>

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

$("#boton-editar-evento").click(function(e){
	/*
	e.preventDefault();
	console.log("Editar evento Pulsado");

	if(verificaElementos()){
		var parametros=getParametrosParaEditar();

		$.ajax({
				url:"../../Controlador/Evento/ajax.php?action=editar&"+encodeURI(parametros) ,
				success: function(result){
					console.log(result);
				}
			});
	}
	*/
});


function verificaElementos(){
	return true;	
}

function getParametrosParaEditar(){
	var res;

	res="id="+document.getElementById("evento_id").value+"&";
	res+="nombre="+document.getElementById("evento_nombre").value+"&";
	res+="descripcion="+document.getElementById("evento_descripcion").value+"&";
	res+="fecha_inicio="+document.getElementById("evento_fecha_inicio").value+"&";
	res+="hora_inicio="+document.getElementById("evento_hora_inicio").value+"&";
	res+="fecha_fin="+document.getElementById("evento_fecha_fin").value+"&";
	res+="hora_fin="+document.getElementById("evento_hora_fin").value+"&";
	res+="aforo="+document.getElementById("evento_aforo").value+"&";
	res+="local="+document.getElementById("evento_local").value+"&";
	res+="ciudad="+document.getElementById("evento_ciudad").value+"&";
	res+="direccion="+document.getElementById("evento_direccion").value+"&";
	res+="pais="+document.getElementById("evento_pais").value+"&";
	res+="gps="+document.getElementById("evento_gps").value+"&";
	res+="imagen="+document.getElementById("imagen").value;

	return res;
}
</script>


