<?php
require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Tool.php';

class Evento{
    
    public $id;
    public $nombre;
    public $descripcion;
    public $fecha_inicio;
    public $hora_inicio;
    public $fecha_fin;
    public $hora_fin;
    public $aforo;
    public $local;
    public $direccion;
    public $ciudad;
    public $pais;
    public $gps;
    public $estado;
    
    private static $dbh;
    
    public function __construct($nombre,$descripcion,$fecha_inicio,$fecha_fin){
        $this->nombre=$nombre;
        $this->descripcion=$descripcion;
        $this->fecha_inicio=$fecha_inicio;
        $this->fecha_fin=$fecha_fin;        
    }
    
    public function guardarEventoEnBD(){
        Evento::$dbh=Tool::conectar();
        
        $sql="INSERT INTO eventos (Nombre,Descripcion,Fecha_inicio,Fecha_fin,Aforo,Local,Direccion,Ciudad,Pais,GPS) VALUES (?,?,?,?,?,?,?,?,?,?)";
        
        $query=Evento::$dbh->prepare($sql);
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        
        $aux=Tool::adaptaFechaHora($this->fecha_inicio, $this->hora_inicio);
        $query->bindParam(3,$aux);
        
        $aux=Tool::adaptaFechaHora($this->fecha_fin, $this->hora_fin);
        $query->bindParam(4,$aux);
        
        $query->bindParam(5,$this->aforo);
        $query->bindParam(6,$this->local);
        $query->bindParam(7,$this->direccion);
        $query->bindParam(8,$this->ciudad);
        $query->bindParam(9,$this->pais);
        $query->bindParam(10,$this->gps);
        $query->execute();        
        
        
        Tool::desconectar(Evento::$dbh);
    }
    
    public function editarEventoEnBD(){
        Evento::$dbh=Tool::conectar();
        
        $sql="UPDATE eventos SET ".
            "Nombre=?,".
            "Descripcion=?,".
            "Fecha_inicio=?,".
            "Fecha_fin=?,".
            "Aforo=?,".
            "Local=?,".
            "Direccion=?,".
            "Ciudad=?,".
            "Pais=?,".
            "GPS=? ".
            " WHERE Id=?";
        
        $query=Evento::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->fecha_inicio);
        $query->bindParam(4,$this->fecha_fin);
        $query->bindParam(5,$this->aforo);
        $query->bindParam(6,$this->local);
        $query->bindParam(7,$this->direccion);
        $query->bindParam(8,$this->ciudad);
        $query->bindParam(9,$this->pais);
        $query->bindParam(10,$this->gps);
        $query->bindParam(11,$this->id);
        
        $query->execute();        
        
        Tool::desconectar(Evento::$dbh);
    }
    
    public static function getAllEventos(){        
        Evento::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM eventos";
        
        $query=Evento::$dbh->prepare($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $query->execute();
             
        Tool::desconectar(Evento::$dbh);
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        $out=array();
        $i=0;
        foreach ($res as $r){
            $out[$i]=self::arrayAObjeto($r);
            $i++;
        }
        
        return $out;
    }
        
    public static function getEvento($eid){
        Evento::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM eventos WHERE Id=?";
        
        $query=Evento::$dbh->prepare($sql);
        $query->bindParam(1,$eid);
        $query->execute();
        
        Tool::desconectar(Evento::$dbh);
        
        return self::arrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    private static function arrayAObjeto($res){
        $ev=new Evento($res['Nombre'], $res['Descripcion'], $res['Fecha_inicio'], $res['Fecha_fin']);
        $ev->id=$res['Id'];
        $ev->aforo=$res['Aforo'];
        $ev->local=$res['Local'];
        $ev->direccion=$res['Direccion'];
        $ev->ciudad=$res['Ciudad'];
        $ev->pais=$res['Pais'];
        $ev->gps=$res['GPS'];
        $ev->estado=$res['Estado'];
        
        return $ev;
    }
}
?>