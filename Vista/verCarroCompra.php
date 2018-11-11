<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Evento.php';


echo Html::cabeceraHtml() . Html::actionBar();

$json=CarroCompra::getJSON();

$numLineas=0;


if(!empty($json)){
    $json=json_decode($json);
    $numLineas=$json->numeroLineas;
}

echo "<h2>Carro Compra ( " . $numLineas . " items )</h2>";

echo "<table id='tablaCarro'></table>";

?>

<script>
$(document).ready(function(){
	$.ajax({
		type:"GET",
		dataType:"json",
		url:"../Controlador/ajax.php?accion=getJSON",
		success:function(data){
			$("#tablaCarro").html(cargarTabla(data));
			addHandlerBotones();
		},
		error:function(){
		}
	});	
});


function addHandlerBotones(){
	$("button.btnEliminar").on("click",function(){
		//event.preventDefault();
		$.ajax({
			type:"GET",
			dataType:"json",
			url:"../Controlador/ajax.php?accion=add&eid=" + $(this).attr("data-eid") + "&tp=" + $(this).attr("data-tpid") + "&cantidad=0",
			success:function(data){
				$("#tablaCarro").html(cargarTabla(data));
				addHandlerBotones();
			},
			error:function(){
				alert("Error borrando");
			}
		});
	});
}

function cargarTabla(data){
	var out="<tr><th>Evento</th><th>Ticket</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>";
	
	$.each(data.lineas,function(key,value){
		out+="<tr data-eid='" + value['evento']['id'] + "' data-tpid='" + value['tipoentrada']['id'] + "'>";
		out+="<td>"+value['evento']['nombre']+"</td>";
		out+="<td>"+value['tipoentrada']['nombre']+"</td>";
		out+="<td>"+value['tipoentrada']['precio']+"</td>";
		out+="<td>"+value['cantidad']+"</td>";
		out+="<td>"+value['tipoentrada']['precio']*value['cantidad']+"</td>";
		out+="<td>";
		out+="<a href='../Vista/verTiendaTickets.php?eid=" + value['evento']['id'] + "'>Cambiar</a> ";
		out+="<button type='button' class='btnEliminar' data-eid='" + value['evento']['id'] + "' data-tpid='" + value['tipoentrada']['id'] + "'>Eliminar</button>";
		out+="</td>";
		out+="</tr>";
	});

	out+="tr><td colspan=4>Total Pedido</td><td>" + data.totalPrecio + " e</td><td></tr>";
	out+="<tr><td colspan=5><a href='../Controlador/checkout.php'>Checkout</a></td></tr>";

	return out;
}

</script>