<?php
require_once '../Modelo/Tool.php';

class PerfilOrganizador
{
    public $id;
    public $idUsuario;
    public $nombre;
    public $descripcion;
    public $mostrarDescripcion;
    public $website;
    public $facebook;
    public $twitter;
    public $instagram;    
    
    private static $dbh;
    
    
    public static function getAllPerfilesOrganizador($idUsuario){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=?";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        Tool::desconectar(PerfilOrganizador::$dbh);
        
        return $query->fetchAll();
    }
    
    public static function getPerfilOrganizador($id,$idUsuario){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=? AND Id=?";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->bindParam(2,$id);
        $query->execute();
        
        Tool::desconectar(PerfilOrganizador::$dbh);
        
        return $query->fetch();
    }
    
    public static function nuevoIdPerfilOrganizador($idUsuario){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="SELECT MAX(Id) from perfilesorganizador WHERE Id_Usuario=?";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_NUM);
        
        Tool::desconectar(PerfilOrganizador::$dbh);
        
        return $res[0]+1;
    }
    
    public function crearPerfilOrganizador(){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="INSERT INTO perfilesorganizador (Id,Id_Usuario,Nombre,Descripcion,Website,Facebook,Twitter,Instagram,Mostrar_descripcion) VALUES (?,?,?,?,?,?,?,?,?)";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$this->id);
        $query->bindParam(2,$this->idUsuario);
        $query->bindParam(3,$this->nombre);
        $query->bindParam(4,$this->descripcion);
        $query->bindParam(5,$this->website);
        $query->bindParam(6,$this->facebook);
        $query->bindParam(7,$this->twitter);
        $query->bindParam(8,$this->instagram);
        $query->bindParam(9,$this->mostrarDescripcion);
        $query->execute();
        
        //print_r($query->errorInfo());
        
        Tool::desconectar(PerfilOrganizador::$dbh);
    }
    
    public function editarPerfilOrganizador($id,$idUsuario){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="UPDATE perfilesorganizador SET ".
            "Nombre=?,Descripcion=?,Website=?,Facebook=?,Twitter=?,Instagram=?,Mostrar_descripcion=? ".
            "WHERE Id=? AND Id_Usuario=?";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->website);
        $query->bindParam(4,$this->facebook);
        $query->bindParam(5,$this->twitter);
        $query->bindParam(6,$this->instagram);
        $query->bindParam(7,$this->mostrarDescripcion);
        $query->bindParam(8,$this->id);
        $query->bindParam(9,$this->idUsuario);
        $query->execute();
                
        
        Tool::desconectar(PerfilOrganizador::$dbh);
        
        return $query->errorInfo();
    }
    
    public static function borrarPerfilOrganizador($id,$idUsuario){
        PerfilOrganizador::$dbh=Tool::conectar();
        
        $sql="DELETE FROM perfilesorganizador WHERE Id=? AND Id_Usuario=?";
        
        $query=PerfilOrganizador::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        
        Tool::desconectar(PerfilOrganizador::$dbh);
        
        return $query->errorInfo();
    }
    
}

