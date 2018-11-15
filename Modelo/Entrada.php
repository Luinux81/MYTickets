<?php
require_once APP_ROOT . '/Modelo/Tool.php';

class Entrada{
    
    public $codigo;
    public $idEvento;
    public $idTipoEntrada;
    public $idUsuario;    
    public $idVenta;
    public $idLineaVenta;
    
    private static $dbh;
    
    public static function getNuevoCodigo($dbh=""){
        $desconectar=false;
        if($dbh==""){
            self::$dbh=Tool::conectar();
            $dbh=self::$dbh;
            $desconectar=true;
        }
        
        $encontrado=false;
        while(!$encontrado){
            $res=Tool::getToken(TICKET_CODE_LENGTH,"0123456789");
            
            if(self::getEntrada($res,$dbh)->codigo==""){
                $encontrado=true;
            }
        }
        
        if($desconectar){
            Tool::desconectar(self::$dbh);
        }
        
        return $res;
    }
    
    public static function getEntrada($codigo,$dbh=""){
        $desconectar=false;
        if($dbh==""){
            self::$dbh=Tool::conectar();
            $dbh=self::$dbh;
            $desconectar=true;
        }
        
        $sql="SELECT * FROM entradas WHERE Codigo=?";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->execute();
        
        if($desconectar){
            Tool::desconectar(self::$dbh);
        }
        
        return self::adaptaArrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    public static function getAllEntradasEventoUsuario($idEvento,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Evento=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idEvento);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    public static function getAllEntradasEvento($idEvento){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Evento=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idEvento);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    public static function crearEntrada($idTipoEntrada,$idEvento,$idVenta,$idLineaVenta, $idUsuario, $dbh=""){
        $desconectar=false;
        if($dbh==""){
            self::$dbh=Tool::conectar();
            $dbh=self::$dbh;
            $desconectar=true;
        }
        
        $codigo=self::getNuevoCodigo($dbh);
        
        $sql="INSERT INTO entradas (Codigo,Id_TipoEntrada,Id_Evento,Id_Usuario,Id_Venta,Id_LineaVenta) VALUES (?,?,?,?,?,?)";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->bindParam(2,$idTipoEntrada);
        $query->bindParam(3,$idEvento);
        $query->bindParam(4,$idUsuario);
        $query->bindParam(5,$idVenta);
        $query->bindParam(6,$idLineaVenta);
        $query->execute();
        
        
        if($desconectar){
            Tool::desconectar(self::$dbh);
        }
    }
    
    public function ___crearEntrada(){
        self::$dbh=Tool::conectar();
        
        $sql="INSERT INTO entradas (Id_Evento,Codigo,Id_TipoEntrada,Id_Usuario,Id_Venta,Id_LineaVenta) VALUES (?,?,?,?)";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$this->idEvento);
        $query->bindParam(2,$this->codigo);
        $query->bindParam(3,$this->idTipoEntrada);
        $query->bindParam(4,$this->idUsuario);
        $query->bindParam(5,$this->idVenta);
        $query->bindParam(6,$this->idLineaVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public function editarEntrada(){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE entradas SET Id_Evento=?,Codigo=?,Id_TipoEntrada=?,Id_Usuario=?,Id_Venta=?,Id_LineaVenta=? WHERE Codigo=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$this->idEvento);
        $query->bindParam(2,$this->codigo);
        $query->bindParam(3,$this->idTipoEntrada);
        $query->bindParam(4,$this->idUsuario);
        $query->bindParam(5,$this->idventa);
        $query->bindParam(6,$this->idLineaVenta);
        $query->bindParam(7,$this->codigo);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public static function eliminarEntrada($codigo){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM entradas WHERE Codigo=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public function getEntradaPDF($codigo,$idEvento){
        
    }
    
    private static function adaptaArrayAObjeto($array){
        $e=new Entrada();
        
        $e->idEvento=$array['Id_Evento'];
        $e->idTipoEntrada=$array['Id_TipoEntrada'];
        $e->idUsuario=$array['Id_Usuario'];
        $e->codigo=$array['Codigo'];
        
        return $e;
    }
    
    private static function arrayDeObjetos($array){
        $i=0;
        $res=array();
        
        foreach($array as $r){
            $res[$i]=adaptaArrayAObjeto($r);
            $i++;
        }
        
        return $res;
    }
}