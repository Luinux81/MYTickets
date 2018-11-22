<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';
require_once APP_ROOT . '/Vista/Html.php';

$idPo=$_GET['idpo'];
$idUsuario=$_SESSION['usuario']['id'];

$perfil=PerfilOrganizador::getPerfilOrganizador($idPo, $idUsuario);

echo Html::cabeceraHtml() . Html::actionBar("perfilesOrganizador");
?>

<div style='width: 100%;' id='mainContent'>
	<form accept-charset="utf-8" method="post" action="../Controlador/editarPerfilOrganizador.php" enctype="multipart/form-data">
    	<h3>Detalles del perfil de organizador</h3>
    	<p>Nombre</p>
    	<input type="text" id="nombre" name="nombre" value="<?php echo $perfil->nombre; ?>">
    	<p>Descripcion</p>
    	<input type="text" id="descripcion" name="descripcion" value="<?php echo $perfil->descripcion; ?>">    	
    	<p>Website</p>
    	<input type="text" id="website" name="website" value="<?php echo $perfil->website; ?>">
    	<p>Facebook</p>
    	<input type="text" id="facebook" name="facebook" value="<?php echo $perfil->facebook; ?>">
    	<p>Instagram</p>
    	<input type="text" id="instagram" name="instagram" value="<?php echo $perfil->instagram; ?>">
    	<p>Twitter</p>
    	<input type="text" id="twitter" name="twitter" value="<?php echo $perfil->twitter; ?>">
    	<p>Mostrar_descripcion</p>
    	<input type="text" id="mostrar_descripcion" name="mostrar_descripcion" value="<?php echo $perfil->mostrarDescripcion; ?>">
    	<p>Imagen</p>
    	<input type="file" id="imagen" name="imagen" accept="image/*" style="display:block;">
    	<img id="imagen_preview" height="250px" style="clear:both;" src="data:image/*;base64,<?php echo base64_encode(stripslashes($perfil->imagen)); ?>">
    	<input type="hidden" name="idPerfil" value="<?php echo $idPo; ?>">
    	<br>
    	<input type="submit" value="Editar perfil">
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