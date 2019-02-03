<?php
require_once "../constantes.php";
require_once APP_ROOT . "/Modelo/GeneradorPDF.php";

$eid=$_GET['eid'];

GeneradorPDF::generaPDFListadoEvento($eid);

?>
