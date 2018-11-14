<?php 
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/PerfilOrganizador.php';
require_once APP_ROOT . '/Vista/Html.php';

$idUsuario=$_SESSION['idusuario'];

$perfiles=PerfilOrganizador::getAllPerfilesOrganizador($idUsuario);

echo Html::actionBar("perfilesOrganizador");

$output="<h3>Perfiles de organizador</h3>
            <ul>";

foreach ($perfiles as $p){
    $output.="<li><a href='../Vista/editarPerfilOrganizador.php?idpo=". $p->id ."'>" . $p->nombre. "</a> 
                <a href='../Controlador/eliminarPerfilOrganizador.php?idpo=" . $p->id . "'> Eliminar </a></li>";
}

$output.="</ul>";

echo $output;
?>