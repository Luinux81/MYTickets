<?php
require_once '../Vista/actionBar.php';

echo actionBar::Html("perfilesOrganizador");
?>

<div style='width: 100%;' id='mainContent'>
	<form method="post" action="../Controlador/crearPerfilOrganizador.php">
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
    	<br>
    	<input type="submit" value="Crear perfil">
    </form>
</div>