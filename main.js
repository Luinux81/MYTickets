$(document).ready(function(){
	
	asignaHandlerInputEmail();
	asignaHandlerInputNombre();	
	asignaHandlerInputCantidad();	
	asignaHandlerBotonEnviar();
});

function asignaHandlerInputEmail(){
	
	$("#user_email").focusout(function(){
		var url=getURLBase()+"../../Controlador/Usuario/ajax.php?action=check_user_email&value="+$(this).val();
		
		
		$.ajax({
			url: ''+url,
			success:function(result){
				var res=JSON.parse(result);
				if(res.existe==1){
					$("#user_nombre").val(res.usuario.nombre).prop("disabled",true);					
				}
				else{
					$("#user_nombre").val("");
					$("#user_nombre").val(res.usuario.nombre).prop("disabled",false);
				}
				verificaForm();
			},
			error:function(request, status, error){
				console.log("Error " + request.responseText);
				console.log("***");
				console.log(request);
			}
		});
	});
	
}

function asignaHandlerInputNombre(){
	$("#user_nombre").change(function(){		
		verificaForm();	
	});
}

function asignaHandlerInputCantidad(){
	
	$(".cantidadTipoEntrada").change(function(){
		verificaForm();
	});
	
}

function verificaForm(){
	var todosZero=true;
	
	$(".cantidadTipoEntrada").each(function(index){
		if($(this).val()!=0){
			todosZero=false;
			return false;
		}
	});
	
			
	$("#venta_enviar").prop("disabled",todosZero || $("#user_nombre").val()=="");
}

function asignaHandlerBotonEnviar(){
	
	$("#venta_enviar").click(function(){
	
		var url=getURLBase()+"../../Controlador/Venta/ajax.php?action=crear&param=";	

		url+=encodeURIComponent( creaJSON() );
	
		$.ajax({
			url:url,
			success:function(result){
				console.log(result);
			}
		});
	});
	
}

function creaJSON(){
	var json='{' +
		'"usuario":{"email":"'+ $("#user_email").val() +'","nombre":"'+ $("#user_nombre").val() +'"},' +
		'"evento":"' + $("#evento_id").val() + '",' +
		'"entradas":[';
	
	var primero=true;
	var idTipo;
	
	$(".cantidadTipoEntrada").each(function(){
	if(primero){
		primero=false;
	}
	else{
		json+=",";
	}
	
	idTipo=$(this).prop("id");
	idTipo=idTipo.substring(idTipo.lastIndexOf("_")+1);
	
	json+='{"tipo":"' + idTipo + '","cantidad":"' + $(this).val() + '"}';			
	});
	
	json+=	']' +
		'}';
	
	return json;

}

function getURLBase(){
	var url=window.location.href;

	return url.substring(0,url.lastIndexOf("/")+1);
}