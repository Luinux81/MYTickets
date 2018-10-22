<?php
require_once '../Modelo/TipoEntrada.php';

TipoEntrada::eliminarTipoEntrada($_GET['eid'], $_GET['tpid']);

header("Location:../Vista/editarEvento.php?eid=" . $_GET['eid']);
?>