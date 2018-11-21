<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Vista/Html.php';

echo Html::cabeceraHtml() . Html::actionBar("perfilesOrganizador");
?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/crearPerfilOrganizador.php" enctype="multipart/form-data" accept-charset="utf-8">
    	<h3>Detalles del perfil de organizador</h3>
    	<p>Nombre</p>
    	<input type="text" id="nombre" name="nombre">
    	<p>Descripcion</p>
    	<input type="text" id="descripcion" name="descripcion">    	
    	<p>Website</p>
    	<input type="text" id="website" name="website">
    	<p>Facebook</p>
    	<input type="text" id="facebook" name="facebook">
    	<p>Instagram</p>
    	<input type="text" id="instagram" name="instagram">
    	<p>Twitter</p>
    	<input type="text" id="twitter" name="twitter">
    	<p>Mostrar_descripcion</p>
    	<input type="text" id="mostrar_descripcion" name="mostrar_descripcion">
    	<p>Imagen</p>
    	<input type="file" accept="image/*" id="imagen" name="imagen"  style="display:block;">
    	<img src="" id="imagen_preview" name="imagen_preview" height="250px"/>
    	<br>
    	<input type="submit" value="Crear perfil">
    </form>
</div>

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