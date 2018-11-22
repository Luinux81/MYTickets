<?php
require_once '../../constantes.php';
require_once APP_ROOT . '/Modelo/Usuario.php';

$id=$_POST['id'];
$pass=$_POST['nuevoPassword'];

Usuario::cambiarPassword($id, $pass);

header("Location:../../index.php");

?>