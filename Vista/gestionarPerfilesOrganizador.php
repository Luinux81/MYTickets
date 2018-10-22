<?php 
require_once '../Modelo/PerfilOrganizador.php';
require_once '../Vista/actionBar.php';

$idUsuario=$_SESSION['idusuario'];

$perfiles=PerfilOrganizador::getAllPerfilesOrganizador($idUsuario);

echo actionBar::Html("perfilesOrganizador");

$output="<h3>Perfiles de organizador</h3>
            <ul>";

foreach ($perfiles as $p){
    $output.="<li><a href='../Vista/editarPerfilOrganizador.php?idpo=". $p['Id'] ."'>" . $p['Nombre']. "</a> 
                <a href='../Controlador/eliminarPerfilOrganizador.php?idpo=" . $p['Id'] . "'> Eliminar </a></li>";
}

$output.="</ul>";

echo $output;
?>