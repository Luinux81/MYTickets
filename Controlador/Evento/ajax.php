<?php 
require_once '../../constantes.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/Tool.php';


if(isset($_GET['action'])){
    $accion=$_GET['action'];
    
    switch ($accion){
        case "editar":
            $id=$_GET['id'];
            $nombre=$_GET['nombre'];
            $descripcion=$_GET['descripcion'];
            $fecha_inicio=$_GET['fecha_inicio'];
            $hora_inicio=$_GET['hora_inicio'];
            $fecha_fin=$_GET['fecha_fin'];
            $hora_fin=$_GET['hora_fin'];
            $aforo=$_GET['aforo'];
            $local=$_GET['local'];
            $ciudad=$_GET['ciudad'];
            $direccion=$_GET['direccion'];
            $pais=$_GET['pais'];
            $gps=$_GET['gps'];
            $imagen=$_GET['imagen'];
            
            
            $e=new Evento($nombre, $descripcion, Tool::adaptaFechaHora($fecha_inicio, $hora_inicio), Tool::adaptaFechaHora($fecha_fin, $hora_fin));
            $e->id=$id;
            $e->aforo=$aforo;
            $e->local=$local;
            $e->direccion=$direccion;
            $e->ciudad=$ciudad;
            $e->pais=$pais;
            $e->gps=$gps;
            
            $e->editarEventoEnBD();
            
            $res=array("operacion"=>"editar","tipo"=>"evento","resultado"=>TRUE,"datos"=>"");
            
            break;
    }
    
    header("Content-type: application/json");
    
    echo json_encode($res);
}

?>