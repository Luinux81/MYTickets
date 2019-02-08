<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Modelo/Tool.php';

$file=fopen(LOG_FILE,"r");

while(!feof($file)){
    $linea=fgets($file);
    
    echo $linea . "<br>";
}

?>