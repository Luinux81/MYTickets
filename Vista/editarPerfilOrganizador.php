<?php
require_once '../Modelo/PerfilOrganizador.php';
require_once '../Vista/actionBar.php';

$idPo=$_GET['idpo'];
$idUsuario=$_SESSION['idusuario'];

$perfil=PerfilOrganizador::getPerfilOrganizador($idPo, $idUsuario);

echo actionBar::Html("perfilesOrganizador");
?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/editarPerfilOrganizador.php">
    	<h3>Detalles del perfil de organizador</h3>
    	<p>Nombre</p>
    	<input type="text" id="nombre" name="nombre" value="<?php echo $perfil['Nombre']; ?>">
    	<p>Descripcion</p>
    	<input type="text" id="descripcion" name="descripcion" value="<?php echo $perfil['Descripcion']; ?>">    	
    	<p>Website</p>
    	<input type="text" id="website" name="website" value="<?php echo $perfil['Website']; ?>">
    	<p>Facebook</p>
    	<input type="text" id="facebook" name="facebook" value="<?php echo $perfil['Facebook']; ?>">
    	<p>Instagram</p>
    	<input type="text" id="instagram" name="instagram" value="<?php echo $perfil['Instagram']; ?>">
    	<p>Twitter</p>
    	<input type="text" id="twitter" name="twitter" value="<?php echo $perfil['Twitter']; ?>">
    	<p>Mostrar_descripcion</p>
    	<input type="text" id="mostrar_descripcion" name="mostrar_descripcion" value="<?php echo $perfil['Mostrar_descripcion']; ?>">
    	<input type="hidden" name="idPerfil" value="<?php echo $idPo; ?>">
    	<br>
    	<input type="submit" value="Editar perfil">
    </form>
</div>