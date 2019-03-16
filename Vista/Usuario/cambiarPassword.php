<?php
require_once '../../constantes.php';

require_once APP_ROOT . "/Vista/Html.php";

echo Html::cabeceraHtml() . Html::actionBar();
?>

<form method="post" action="../../Controlador/Usuario/cambiarPassword.php">
<p>Nuevo Pass</p>
<input type="text" name="nuevoPassword">
<input type="hidden" name="id" value="<?php echo $_SESSION['usuario']['id']; ?>" >
<input type="submit" />
</form>
 