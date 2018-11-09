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
foreach ($res as $te){
    $out.="<li id=" . $te->id . "><span class='nombre'>" . $te->nombre . "</span> <span class='precio'>". $te->precio . "</span>" .  
            "<select>" .
            "<option value='0'>0</option>";
    
    for($i=$te->minimo_compra;$i<=$te->maximo_compra;$i++){
        $out.="<option value='" . $i . "'>" . $i . "</option>";    
    }
    
    $out.="</select></li>";
}
$out.="</ul>";

$numItems=$carro->numeroLineas;
if($numItems!=1){
    $textNumItem="(" . $numItems . " items)";
}
else{
    $textNumItem="(" . $numItems . " item)";
}

$out.="<input type='hidden' id='evento' value='". $eid . "'>" .
        "<p>Cantidad:<span id='texto_salida_cuenta'>" . $carro->numeroEntradas . " </span>  Precio:<span id='texto_salida_valor'>" . $carro->totalPrecio . "</span> e</p>" . 
        "<p>Carro Compra <span id='numeroItems'>" . $textNumItem . "<span></p>" .
        "<div id='div_salida'>" . CarroCompra::getHTMLAllItems() . "</div>";

$out.="<br><button id='limpiarCarro'>Limpiar carro</button>";
echo $out;

?>

<script>
$(document).ready(function(){
	$("select").change(function(){
		var precio=$(this).parent().children("span.precio").text();
		var cantidad=$(this).val();
		
		$.ajax({
			type:"GET",
			url:"../Controlador/ajax.php?eid="+ $("#evento").attr("value") + "&tp=" + $(this).parent().attr("id") + "&cantidad=" + cantidad + "&accion=add",
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
			actualizaLineas(data.lineas);
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
	$("#numeroItems").html(out);
	if(numItems==0){
		$("#link_ver_carro").html("");
	}
	else{
		$("#link_ver_carro").html("Ver Carro "+out);
	}	
}

function actualizaLineas(lineas){
	var out="<ul>";
	
	$.each(lineas, function(key,value){
		out+="<li>Evento->"+value['evento']['id']+"  TP->"+value['tipoentrada']+" Cantidad->"+value['cantidad']+"</li>";
	});

	out+="</ul>";

	$("#div_salida").html(out);
}

</script>