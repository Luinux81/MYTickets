<?php
require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/LineaVenta.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';

class Venta{
    
    public $id;
    public $idUsuario;
    public $importe;
    public $fecha;
    public $estado;
    public $lineasVenta;
    public $paymentID;
    
    private static $dbh;
    
    public static function importarJSONCarro($json){
        $v=new Venta();
        
        $id=0;
        $totalPrecio=0;
        $lineasVenta=array();
        
        if(!empty($json)){
            $carro=json_decode($json,true);
            
            $id=self::getNuevoId();
            $totalPrecio=$carro["totalPrecio"];
            
            foreach($carro['lineas'] as $linea){
                $lv=new LineaVenta();
                
                $lv->id=LineaVenta::getNuevoId($id);;
                $lv->idVenta=$id;
                $lv->idEvento=$linea['evento']['id'];
                $lv->idTipoEntrada=$linea['tipoentrada']['id'];
                $lv->precio=$linea['tipoentrada']['precio'];
                $lv->cantidad=$linea['cantidad'];;
                $lv->estado="";
                
                
                $lineasVenta[]=$lv;
            }
        }
        
        $v->id=$id;
        $v->idUsuario=$_SESSION['idusuario'];
        $v->importe=$totalPrecio;
        $v->fecha=date('Y-m-d h:i:s');
        $v->lineasVenta=$lineasVenta;
        
        return $v;
    }
    
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
        $v->paymentID=$res['payment_id'];
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
        
        $sql="INSERT INTO ventas (Id,Id_Usuario,Importe,Fecha,Estado,payment_id) VALUES (?,?,?,?,?,?)";
        
        try {
            self::$dbh->beginTransaction();
            
            $query=self::$dbh->prepare($sql);
            $query->bindParam(1,$this->id);
            $query->bindParam(2,$this->idUsuario);
            $query->bindParam(3,$this->importe);
            $query->bindParam(4,$this->fecha);
            $query->bindParam(5,$this->estado);
            $query->bindParam(6,$this->paymentID);
            $query->execute();
            
            foreach($this->lineasVenta as $l){
                LineaVenta::crearLineaVenta($l->id, $l->idVenta, $l->idEvento, $l->idTipoEntrada, $l->precio, $l->cantidad, $l->estado,self::$dbh);
            }
            
            self::$dbh->commit();
            
            CarroCompra::vaciarCarro();
            
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