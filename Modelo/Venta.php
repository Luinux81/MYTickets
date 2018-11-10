<?php
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/LineaVenta.php';

class Venta{
    
    public $id;
    public $idUsuario;
    public $importe;
    public $fecha;
    public $estado;
    public $lineasVenta;
    
    private static $dbh;
    
    public static function getNuevoId(){
        $idValido=false;
        
        while(!$idValido){
            $token=Tool::getToken(ID_VENTA_LENGHT);
            $res=self::getVenta($token);
            if($res->id==""){
                $idValido=true;
            }
        }
        
        return $token;
    }
    
    public static function getVenta($id){
        $v=new Venta();
        
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM ventas WHERE Id=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_ASSOC);
        
        $v->id=$res['Id'];        
        $v->idUsuario=$res['Id_Usuario'];
        $v->importe=$res['Importe'];
        $v->fecha=$res['Fecha'];
        $v->estado=$res['Estado'];
        if($res['Id']!=""){
            $v->lineasVenta=LineaVenta::getAllLineasVenta($res['Id']);
        }
        else{
            $v->lineasVenta=array();
        }
        
        
        Tool::desconectar(self::$dbh);
        
        return $v;
        
    }
    
    public function crearVenta(){
        self::$dbh=Tool::conectar();
        
        $sql="INSERT INTO ventas (Id,Id_Usuario,Importe,Fecha,Estado) VALUES (?,?,?,?,?)";
        
        try {
            self::$dbh->beginTransaction();
            
            $query=self::$dbh->prepare($sql);
            $query->bindParam(1,$this->id);
            $query->bindParam(2,$this->idUsuario);
            $query->bindParam(3,$this->importe);
            $query->bindParam(4,$this->fecha);
            $query->bindParam(5,$this->estado);
            $query->execute();
            
            foreach($this->lineasVenta as $l){
                LineaVenta::crearLineaVenta($l->id, $l->idVenta, $l->idEvento, $l->idTipoEntrada, $l->precio, $l->cantidad, $l->estado);
            }
            
            self::$dbh->commit();
            
        } catch (Exception $e) {
            self::$dbh->rollBack();
            
        }
        
        
        Tool::desconectar(self::$dbh);
    }
    
    public function editarVenta(){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE ventas SET Importe=?, Fecha=?, Estado=? WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$this->importe);
        $query->bindParam(2,$this->fecha);
        $query->bindParam(3,$this->estado);
        $query->bindParam(4,$this->id);
        $query->bindParam(5,$this->idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public static function eliminarVenta($idVenta,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM ventas WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public static function cambiarEstado($idVenta,$estado){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE ventas SET Estado=? WHERE Id=?";
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$estado);
        $query->bindParam(2,$idVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public static function addLineaVenta(){
        
    }
    
    public static function eliminarLineaVenta($idLinea){
        
    }
    
    
    
    
}