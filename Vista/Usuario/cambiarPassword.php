<?php
require_once '../../constantes.php';


?>

<form method="post" action="../../Controlador/Usuario/cambiarPassword.php">
<p>Nuevo Pass</p>
<input type="text" name="nuevoPassword">
<input type="hidden" name="id" value="<?php echo $_SESSION['usuario']['id']; ?>" >
<input type="submit" />
</form>
 