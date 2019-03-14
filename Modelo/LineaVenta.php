<?php
/**
 * Clase LineaVenta | Modelo/LineaVenta.php
 *
 * @author      Luis Bre�a Calvo <luinux81@gmail.com>
 * @version     v.0.1 
 */

require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

/**
 * Esta clase modela una linea de venta
 *
 */
class LineaVenta{
    public $id;
    public $idVenta;
    public $idEvento;
    public $idTipoEntrada;
    public $precio;
    public $cantidad;
    public $estado;
    
    private static $dbh;
    
    public static function getNuevoId($idVenta){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT MAX(Id) FROM lineasventa WHERE Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->execute();
        
        $max=$query->fetch(PDO::FETCH_NUM);
        
        Tool::desconectar(self::$dbh);
        
        return $max[0]+1;
    }
    
    public static function getLineaVenta($id,$idVenta){
        self::$dbh=Tool::conectar();        
        
        $sql="SELECT * FROM lineasventa WHERE Id=? AND Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idVenta);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_ASSOC);

        Tool::desconectar(self::$dbh);
        
        return self::arrayAObjeto($res);
    }
    
    public static function getAllLineasVenta($idVenta){
        self::$dbh=Tool::conectar();
        
        $res=array();
        $sql="SELECT * FROM lineasventa WHERE Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->execute();
        
        $aux=$query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($aux as $lv){
            $res[]=self::arrayAObjeto($lv);
        }
        
        Tool::desconectar(self::$dbh);
        
        return $res;
    }
        
    public static function crearLineaVenta($id,$idVenta,$idEvento,$idTipoEntrada,$precio,$cantidad,$estado,$dbh=""){
        $desconectar=false;
        if($dbh==""){
            self::$dbh=Tool::conectar();
            $dbh=self::$dbh;
            $desconectar=true;
        }
        
        
        $sql="INSERT INTO lineasventa (Id,Id_Venta,Id_Evento,Id_TipoEntrada,Precio,Cantidad,Estado) VALUES (?,?,?,?,?,?,?)";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idVenta);
        $query->bindParam(3,$idEvento);
        $query->bindParam(4,$idTipoEntrada);
        $query->bindParam(5,$precio);
        $query->bindParam(6,$cantidad);
        $query->bindParam(7,$estado);
        $query->execute();
        
        $i=1;
        while($i<=$cantidad){
            //TODO: Cambiar clase entrada para que no necesite el idusuario, aqui se pasa siempre 0 
            Entrada::crearEntrada($idTipoEntrada, $idEvento, $idVenta, $id, 0, $dbh);
            $i++;
        }
        
        if($desconectar){
            Tool::desconectar(self::$dbh);
        }
        
    }
    
    public static function editarLineaVenta($id,$idVenta,$idEvento,$idTipoEntrada,$precio,$cantidad,$estado){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE lineasventa SET Id_Evento=?,Id_TipoEntrada=?,Precio=?,Cantidad=?,Estado=?) WHERE Id=? AND Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        
        $query->bindParam(1,$idEvento);
        $query->bindParam(2,$idTipoEntrada);
        $query->bindParam(3,$precio);
        $query->bindParam(4,$cantidad);
        $query->bindParam(5,$estado);
        $query->bindParam(6,$id);
        $query->bindParam(7,$idVenta);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
    }
    
    public static function eliminarLineaVenta($id,$idVenta){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM lineasventa WHERE Id=? AND Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    public function getEvento(){
        return Evento::getEvento($this->idEvento);
    }
    
    /**
     * 
     * @return TipoEntrada
     */
    public function getTipoEntrada(){
        return TipoEntrada::getTipoEntrada($this->idEvento, $this->idTipoEntrada);
    }
    
    public function getEntradas(){
        return Entrada::getEntradasPorLineaVenta($this->idVenta, $this->id);
    }
    
    private static function arrayAObjeto($array){
        $l=new LineaVenta();

        $l->id=$array['Id'];
        $l->idVenta=$array['Id_Venta'];
        $l->idEvento=$array['Id_Evento'];
        $l->idTipoEntrada=$array['Id_TipoEntrada'];
        $l->precio=$array['Precio'];
        $l->cantidad=$array['Cantidad'];
        $l->estado=$array['Estado'];
        
        return $l;
    }
}
?>