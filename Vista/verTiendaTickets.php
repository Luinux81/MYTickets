<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';

$eid=$_GET['eid'];

echo Html::cabeceraHtml() . Html::actionBar();

$out="<h2>Selecciona Tickets</h2><ul>";
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

$numItems=CarroCompra::getCountLineas();
if($numItems!=1){
    $textNumItem="(" . $numItems . " items)";
}
else{
    $textNumItem="(" . $numItems . " item)";
}

$out.="<input type='hidden' id='evento' value='". $eid . "'>" .
        "<p>Cantidad:<span id='texto_salida_cuenta'>" . CarroCompra::getCountEntradas() . " </span>  Precio:<span id='texto_salida_valor'>" . CarroCompra::getValorTotal() . "</span> e</p>" . 
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
				$("#div_salida").html(datahtml);
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
				$("#div_salida").html(datahtml);
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
	cuenta();
	cuentaEntradas();
	valor();	
}

function cuenta(){
	$.ajax({
		type:"GET",
		url:"../Controlador/ajax.php?accion=contar",
		success:function(datahtml){
			var out;
			if(datahtml=="1"){
				out="( 1 item )";
			}
			else{
				out="( " + datahtml + " items )";
			}
			$("#numeroItems").html(out);
			if(datahtml==0){
				$("#link_ver_carro").html("");
			}
			else{
				$("#link_ver_carro").html("Ver Carro "+out);
			}
			
		}
	});		
}

function cuentaEntradas(){
	$.ajax({
		type:"GET",
		url:"../Controlador/ajax.php?accion=contarEntradas",
		success:function(datahtml){
			$("#texto_salida_cuenta").html(datahtml);
		}
	});	
}

function valor(){
	$.ajax({
		type:"GET",
		url:"../Controlador/ajax.php?accion=valor",
		success:function(datahtml){
			$("#texto_salida_valor").html(datahtml);
		}
	});
}
</script>