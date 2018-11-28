<?php
require_once '../../constantes.php';
require_once APP_ROOT . '/Vista/Html.php';


echo Html::cabeceraHtml() . Html::actionBar();
?>

<form method="post" action="../../Controlador/Usuario/crearUsuario.php"  >

<p>Email</p>
<input name="email">
<p>Nombre</p>
<input name="nombre" />
<p>Password</p>
<input name="password">
<br>
<input type="submit" value="Crear Usuario">
</form>