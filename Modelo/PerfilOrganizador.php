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
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    public static function getPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=? AND Id=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->bindParam(2,$id);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::adaptaArrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    public static function nuevoIdPerfilOrganizador($idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT MAX(Id) from perfilesorganizador WHERE Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_NUM);
        
        Tool::desconectar(self::$dbh);
        
        return $res[0]+1;
    }
    
    public function crearPerfilOrganizador(){
        self::$dbh=Tool::conectar();
        
        $sql="INSERT INTO perfilesorganizador (Id,Id_Usuario,Nombre,Descripcion,Website,Facebook,Twitter,Instagram,Mostrar_descripcion) VALUES (?,?,?,?,?,?,?,?,?)";
        
        $query=self::$dbh->prepare($sql);
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
        
        Tool::desconectar(self::$dbh);
    }
    
    public function editarPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE perfilesorganizador SET ".
            "Nombre=?,Descripcion=?,Website=?,Facebook=?,Twitter=?,Instagram=?,Mostrar_descripcion=? ".
            "WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
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
                
        
        Tool::desconectar(self::$dbh);
        
        return $query->errorInfo();
    }
    
    public static function borrarPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM perfilesorganizador WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
        
        return $query->errorInfo();
    }
    
    private static function adaptaArrayAObjeto($array){
        $p=new PerfilOrganizador();
        
        $p->id=$array['Id'];
        $p->idUsuario=$array['Id_Usuario'];
        $p->nombre=$array['Nombre'];
        $p->descripcion=$array['Descripcion'];
        $p->mostrarDescripcion=$array['Mostrar_descripcion'];
        $p->website=$array['Website'];
        $p->facebook=$array['Facebook'];
        $p->twitter=$array['Twitter'];
        $p->instagram=$array['Instagram'];
        
        return $p;
    }
    
    private static function arrayDeObjetos($array){
        $i=0;
        $res=array();
        
        foreach($array as $r){
            $res[$i]=self::adaptaArrayAObjeto($r);
            $i++;
        }
        
        return $res;
    }
}

