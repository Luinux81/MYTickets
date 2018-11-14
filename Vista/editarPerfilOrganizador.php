<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';
require_once APP_ROOT . '/Vista/Html.php';

$idPo=$_GET['idpo'];
$idUsuario=$_SESSION['idusuario'];

$perfil=PerfilOrganizador::getPerfilOrganizador($idPo, $idUsuario);

echo Html::actionBar("perfilesOrganizador");
?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/editarPerfilOrganizador.php">
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
    	<input type="hidden" name="idPerfil" value="<?php echo $idPo; ?>">
    	<br>
    	<input type="submit" value="Editar perfil">
    </form>
</div>