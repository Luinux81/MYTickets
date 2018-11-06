<?php
require_once APP_ROOT . '/Modelo/Tool.php';

class Entrada{
    
    public $idEvento;
    public $codigo;
    public $idTipoEntrada;
    public $idUsuario;    
    
    private static $dbh;
    
    public static function getEntrada($codigo,$idEvento){
        Entrada::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Codigo=? AND Id_Evento=?";
        
        $query=Entrada::$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->bindParam(2,$idEvento);
        $query->execute();
        
        Tool::desconectar(Entrada::$dbh);
        
        return adaptaArrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    public static function getAllEntradasUsuario($idEvento,$idUsuario){
        Entrada::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Evento=? AND Id_Usuario=?";
        
        $query=Entrada::$dbh->prepare($sql);
        $query->bindParam(1,$idEvento);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        Tool::desconectar(Entrada::$dbh);
        
        return arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    public function crearEntrada(){
        Entrada::$dbh=Tool::conectar();
        
        $sql="INSERT INTO entradas (Id_Evento,Codigo,Id_TipoEntrada,Id_Usuario) VALUES (?,?,?,?)";
        
        $query=Entrada::$dbh->prepare($sql);
        $query->bindParam(1,$this->idEvento);
        $query->bindParam(2,$this->codigo);
        $query->bindParam(3,$this->idTipoEntrada);
        $query->bindParam(4,$this->idUsuario);
        $query->execute();
        
        Tool::desconectar(Entrada::$dbh);
    }
    
    public function editarEntrada(){
        Entrada::$dbh=Tool::conectar();
        
        $sql="UPDATE entradas SET Id_Evento=?,Codigo=?,Id_TipoEntrada=?,Id_Usuario=? WHERE Id_Evento=? AND Codigo=?";
        
        $query=Entrada::$dbh->prepare($sql);
        $query->bindParam(1,$this->idEvento);
        $query->bindParam(2,$this->codigo);
        $query->bindParam(3,$this->idTipoEntrada);
        $query->bindParam(4,$this->idUsuario);
        $query->bindParam(5,$this->idEvento);
        $query->bindParam(6,$this->codigo);
        $query->execute();
        
        Tool::desconectar(Entrada::$dbh);
    }
    
    public static function eliminarEntrada($codigo,$idEvento){
        Entrada::$dbh=Tool::conectar();
        
        $sql="DELETE FROM entradas WHERE Codigo=? AND Id_Evento=?";
        
        $query=Entrada::$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->bindParam(2,$idEvento);
        $query->execute();
        
        Tool::desconectar(Entrada::$dbh);
    }
    
    public function getEntradaPDF($codigo,$idEvento){
        
    }
    
    private function adaptaArrayAObjeto($array){
        $e=new Entrada();
        
        $e->idEvento=$array['Id_Evento'];
        $e->idTipoEntrada=$array['Id_TipoEntrada'];
        $e->idUsuario=$array['Id_Usuario'];
        $e->codigo=$array['Codigo'];
        
        return $e;
    }
    
    private function arrayDeObjetos($array){
        $i=0;
        $res=array();
        
        foreach($array as $r){
            $res[$i]=adaptaArrayAObjeto($r);
            $i++;
        }
        
        return $res;
    }
}