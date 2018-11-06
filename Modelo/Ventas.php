<?php
require_once APP_ROOT . '/Modelo/Tool.php';

class Venta{
    
    public $id;
    public $idEvento;
    public $idUsuario;
    public $importe;
    public $fecha;
    public $estado;
    
    private static $dbh;
    
    public static function getVenta($id,$idEvento,$idUsuario){
        $v=new Venta();
        
        Venta::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM ventas WHERE Id=? AND Id_Evento=? AND Id_Usuario=?";
        
        $query=Venta::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idEvento);
        $query->bindParam(3,$idUsuario);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_ASSOC);
        
        $v->id=$res['Id'];
        $v->idEvento=$res['Id_Evento'];
        $v->idUsuario=$res['Id_Usuario'];
        $v->importe=$res['Importe'];
        $v->fecha=$res['Fecha'];
        $v->estado=$res['Estado'];
        
        Tool::desconectar(Venta::$dbh);
        
        return $v;
        
    }
    
    public function crearVenta(){
        Venta::$dbh=Tool::conectar();
        
        $sql="INSERT INTO ventas (Id,Id_Evento,Id_Usuario,Importe,Fecha,Estado) VALUES (?,?,?,?,?,?)";
        
        $query=Venta::$dbh->prepare($sql);
        $query->bindParam(1,$this->id);
        $query->bindParam(2,$this->idEvento);
        $query->bindParam(3,$this->idUsuario);
        $query->bindParam(4,$this->importe);
        $query->bindParam(5,$this->fecha);
        $query->bindParam(6,$this->estado);
        $query->execute();
        
        Tool::desconectar(Venta::$dbh);
    }
    
    public function editarVenta(){
        Venta::$dbh=Tool::conectar();
        
        $sql="UPDATE ventas SET Importe=?, Fecha=?, Estado=? WHERE Id=? AND Id_Evento=? AND Id_Usuario=?";
        
        $query=Venta::$dbh->prepare($sql);
        $query->bindParam(1,$this->importe);
        $query->bindParam(2,$this->fecha);
        $query->bindParam(3,$this->estado);
        $query->bindParam(4,$this->id);
        $query->bindParam(5,$this->idEvento);
        $query->bindParam(6,$this->idUsuario);
        $query->execute();
        
        Tool::desconectar(Venta::$dbh);
    }
    
    public static function eliminarVenta($idVenta,$idEvento,$idUsuario){
        Venta::$dbh=Tool::conectar();
        
        $sql="DELETE FROM ventas WHERE Id=? AND Id_Evento=? AND Id_Usuario=?";
        
        $query=Venta::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->bindParam(2,$idEvento);
        $query->bindParam(3,$idUsuario);
        $query->execute();
        
        Tool::desconectar(Venta::$dbh);
    }
    
    public static function cambiarEstado($idVenta,$idEvento,$estado){
        Venta::$dbh=Tool::conectar();
        
        $sql="UPDATE ventas SET Estado=? WHERE Id=? AND Id_Evento=?";
        $query=Venta::$dbh->prepare($sql);
        $query->bindParam(1,$estado);
        $query->bindParam(2,$idVenta);
        $query->bindParam(3,$idEvento);
        $query->execute();
        
        Tool::desconectar(Venta::$dbh);
    }
    
    public static function addLineaVenta(){
        
    }
    
    public static function eliminarLineaVenta($idLinea){
        
    }
    
    
    
    
}