<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';

$eid=$_GET['eid'];

echo Html::cabeceraHtml() . Html::actionBar();

$out="<h2>Selecciona Tickets</h2><ul>";

$carro=json_decode(CarroCompra::getJSON());

$res=TipoEntrada::getAllTipoEntradas($eid);

$out.="<table>";
$out.="<tr> <th>Ticket</th> <th>Precio</th> <th>Cantidad</th> </tr>";
foreach ($res as $te){
    $out.="<tr>";
    $out.="<td class='nombre'>" . $te->nombre . "</td>";
    $out.="<td class='precio'>" . $te->precio . "</td>";
    
    $out.="<td><select data-eid='" . $eid . "' data-tpid='" . $te->id . "'><option value='0'>0</option>";
    
    $cantidadEnCarro=CarroCompra::getCantidad($eid, $te->id);
    
    for($i=$te->minimo_compra;$i<=$te->maximo_compra;$i++){
        if($i==$cantidadEnCarro){
            $aux=" selected ";            
        }
        else{
            $aux="";
        }
        
        $out.="<option value='" . $i . "' " . $aux . ">" . $i . "</option>";    
    }
    
    $out.="</select></td></tr>";
}
$out.="</table>";

$out.="<input type='hidden' id='evento' value='". $eid . "'>" .
        "<p>Cantidad:<span id='texto_salida_cuenta'>" . $carro->numeroEntradas . " </span>  Precio:<span id='texto_salida_valor'>" . $carro->totalPrecio . "</span> e</p>";

$out.="<button id='limpiarCarro'>Limpiar carro</button>";
echo $out;

?>

<script>
$(document).ready(function(){
	$("select").change(function(){
		var precio=$(this).parent().parent().children("td.precio").html();
		var cantidad=$(this).val();
		
		$.ajax({
			type:"GET",
			url:"../Controlador/ajax.php?eid="+ $(this).attr("data-eid") + "&tp=" + $(this).attr("data-tpid") + "&cantidad=" + cantidad + "&accion=add",
			success:function(datahtml){
				actualiza();
			},
			error:function(){
				alert("Error");
			}
		});
	});

	$("#limpiarCarro").on("click",function(){
		$.ajax({
			type:"GET",
			url:"../Controlador/ajax.php?accion=limpiar",
			success:function(datahtml){
				$("select > option[value=0]").prop("selected",true);
				actualiza();
			},
			error:function(){
				alert("Error");
			}
		});
	});
});

function actualiza(){
	$.ajax({
		type:"GET",
		dataType:"json",
		url:"../Controlador/ajax.php?accion=getJSON",
		success:function(data){
			actualizaLinkCarro(data.numeroLineas);
			$("#texto_salida_cuenta").html(data.numeroEntradas);
			$("#texto_salida_valor").html(data.totalPrecio);
		},
		error:function(){
			alert("Error");
		}		
	});
}

function actualizaLinkCarro(numItems){
	var out;
	if(numItems=="1"){
		out="( 1 item )";
	}
	else{
		out="( " + numItems + " items )";
	}
	
	if(numItems==0){
		$("#link_ver_carro").html("");
	}
	else{
		$("#link_ver_carro").html("Ver Carro "+out);
	}	
}


</script>