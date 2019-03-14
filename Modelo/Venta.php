<?php
/**
 * Clase Venta | Modelo/Venta.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1 
 */

require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/LineaVenta.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';


/**
 * Esta clase modela una venta de entradas
 *
 */
class Venta{
    
    public $id;
    public $idUsuario;
    public $importe;
    public $fecha;
    public $estado;
    public $lineasVenta;
    public $paymentID;
    
    private static $dbh;
    
    /**
     * Obtiene un objeto Venta a partir del valor de la variable de entrada en formato JSON
     * 
     * @param string $json
     * @return Venta
     */
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
        $v->idUsuario=$_SESSION['usuario']['id'];
        $v->importe=$totalPrecio;
        $v->fecha=date('Y-m-d h:i:s');
        $v->lineasVenta=$lineasVenta;
        
        return $v;
    }
    
    /**
     * Devuelve un id no existente en la tabla ventas de la base de datos
     *
     * @return string Id valido para una nueva venta en la base de datos.
     */
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
    
    /**
     * Obtiene todas las ventas de un usuario determinado en un array
     * 
     * @param int $idUsuario Id del usuario
     * @return Venta[]
     */
    public static function getVentasUsuario($idUsuario){
        self::$dbh=Tool::conectar();
        $ventas=array();
        
        $sql="SELECT * FROM ventas WHERE Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        foreach($res as $r){
            $ventas[]=self::getVenta($r['Id']);
        }
        
        Tool::desconectar(self::$dbh);
        
        return $ventas;
    }
    
    /**
     * Obtiene todas las ventas de un evento determinado en un array
     * 
     * @param int $idEvento Id del evento
     * @return array
     */
    public static function getVentasEvento($idEvento){
        self::$dbh=Tool::conectar();
        //$ventas=array();
        
        $sql="SELECT * FROM ventas AS v INNER JOIN lineasventa AS lv ON v.Id=lv.Id_venta WHERE lv.Id_Evento=? ORDER BY v.Fecha ASC";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idEvento);
        $query->execute();
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        
        /*
        foreach($res as $r){
            $ventas[]=self::getVenta($r['Id']);
        }
        
        Tool::desconectar(self::$dbh);
        
        return $ventas;
        */
        Tool::desconectar(self::$dbh);
        return $res;
    }
    
    /**
     * Obtiene una venta determinada de la base de datos en un objeto Venta
     * 
     * @param string $id Id de la Venta
     * @return Venta
     */
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
    
    public static function getAllLineasVenta($idVenta){
        return LineaVenta::getAllLineasVenta($idVenta);
    }
    
    /**
     * Crea registros de venta, lineas de venta y entradas en la base de datos con los datos del objeto actual usando una transaccion.
     * 
     * @return boolean Devuelve true si se realiza la insercion correcta de venta,lineas de venta y entradas, false en caso de error
     */
    public function crearVenta(){
        self::$dbh=Tool::conectar();
        $aux=true;
        
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
            $aux=false;
            
        } finally{
            Tool::desconectar(self::$dbh);
            return $aux;
        }
    }
    
    /**
     * Se modifica un registro de la base de datos determinado por el atributo id del objeto actual ($this), el registro se actualiza con los atributos del objeto actual ($this)
     */
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
    
    /**
     * Elimina un registro de venta de la base de datos
     * 
     * @internal Tambien se eliminan las lineas de venta y entradas asociadas a traves de las foreing_keys de la base de datos
     * @param string $idVenta Id de la venta
     * @param int $idUsuario Id del usuario
     */
    public static function eliminarVenta($idVenta,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM ventas WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    /**
     * Cambia el atributo estado en un registro de venta en la base de datos determinado por el parametro de entrada
     * 
     * @param string $idVenta Id de la venta
     * @param string $estado Estado que quedara guardado en el base de datos
     */
    public static function cambiarEstado($idVenta,$estado){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE ventas SET Estado=? WHERE Id=?";
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$estado);
        $query->bindParam(2,$idVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    /**
     * Obtiene el mensaje para enviar en el email de notificacion 
     * 
     * @param string $idVenta Id de la venta
     * @param string $modelo Las opciones son "", "market" y "connection"
     * @return string
     */
    public static function getMensajeEmail($idVenta,$modelo=""){
        $v=self::getVenta($idVenta);
        $u=Usuario::getUsuario($v->idUsuario);
        
        switch ($modelo){
            case "market":
                $aux="<img src='http://market.transitionfestival.org/typo.festival.market.png' width='350' height='100' >";
                break;
            case "connection":
                $aux="<img src='http://connection.transitionfestival.org/tipografia_negro.png' width='350' height='100' >";
                break;
            default:
                $aux="";
        }
        
        $msg="<html>
                <head></head>
                <body>
                    ". $aux . "<br>
                        <b>Reserva Realizada</b> <br>ID:" . $v->id . "<br><br> 
                        <b>Datos del comprador:</b> <br>   
                            Nombre completo: " . $u->nombre . " <br>
                            Email: " . $u->email . "<br><br>
                        <b>Detalles de la compra:</b><br><br>   ";
        foreach ($v->lineasVenta as $l){
            $msg.=      $l->cantidad . " x " . TipoEntrada::getTipoEntrada($l->idEvento, $l->idTipoEntrada)->nombre . "<br>"; 
        }
        
        $msg.=  "   <b>Total: " . $v->importe . " euros</b>
                </body>
                </html>";
        
        return $msg;
    }

    /**
     * Determina si existe una venta en la base de datos con un determinado paymentID
     * 
     * @param string $id Payment Id
     * @return boolean
     */
    public static function existePaymentID($id){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM ventas WHERE payment_id=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1, $id);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return($query->rowCount()>0);
    }
}