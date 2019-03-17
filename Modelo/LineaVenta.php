<?php
/**
 * Clase LineaVenta | Modelo/LineaVenta.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1 
 */

require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

/**
 * Esta clase modela una linea de venta.
 */
class LineaVenta{
    
    /**     
     * Identificador de la linea de venta.
     * @var int 
     */
    public $id;
    
    /**
     * Identificador de la venta.
     * @var string
     */
    public $idVenta;
    
    /**
     * Identificador del evento registrado en la linea de venta.
     * @var int
     */
    public $idEvento;
    
    /**
     * Identificador del tipo de entrada registrado en la linea de venta.
     * @var int
     */
    public $idTipoEntrada;
    
    /**
     * Precio del tipo de entrada registrado en la linea de venta.
     * @var float
     */
    public $precio;
    
    /**
     * Cantidad de entradas registradas en la linea de venta.
     * @var int
     */
    public $cantidad;
    
    /**
     * Estado de la linea de venta.
     * @var string
     */
    public $estado;
    
    /**
     * Handler de la conexion con la base de datos.
     * @var ModeloBD
     */
    private static $dbh;
    
    
    /**
     * Crea un registro de linea de venta y entradas en la base de datos.
     * 
     * @param int $id
     * @param string $idVenta
     * @param int $idEvento
     * @param int $idTipoEntrada
     * @param float $precio
     * @param int $cantidad
     * @param string $estado
     * @param ModeloBD $dbh Opcional. En caso de pasarse como parametro un handler de conexion con la base de datos, la conexion no se cerrara al terminar la operacion.
     */
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
    
    
    /**
     * Modifica un registro de linea de venta en la base de datos determinado por los parametros id e idVenta.
     * 
     * @param int $id
     * @param string $idVenta
     * @param int $idEvento
     * @param int $idTipoEntrada
     * @param float $precio
     * @param int $cantidad
     * @param string $estado
     */
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
    
    
    /**
     * Elimina un registro de linea de venta de la base de datos determinado por los parametros de entrada
     * @param int $id Identificador de la linea de venta.
     * @param string $idVenta Identificador de la venta.
     */
    public static function eliminarLineaVenta($id,$idVenta){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM lineasventa WHERE Id=? AND Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
    
    /**
     * Obtiene el evento asociado a la linea de venta determinada por el atributo $this->idEvento.
     * 
     * @return Evento
     */
    public function getEvento(){
        return Evento::getEvento($this->idEvento);
    }
    
    
    /**
     * Obtiene un array con todas las entradas registradas en la base de datos determinadas por los atributos $this->idVenta y $this->id.
     * 
     * @return Entrada[]
     */
    public function getEntradas(){
        return Entrada::getEntradasPorLineaVenta($this->idVenta, $this->id);
    }
    
    
    
    /**
     * Obtiene un registro de linea de venta de la base de datos determinada por los parametros de entrada.
     * 
     * @param int $id Identificador de la linea de venta.
     * @param string $idVenta Identificador de la venta.
     * 
     * @return LineaVenta
     */
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
    
    
    /**
     * Obtiene un array con las lineas de venta asociadas a una venta determinado por el parametro de entrada.
     * 
     * @param string $idVenta Identificador de la venta.
     * 
     * @return LineaVenta[]
     */
    public static function getLineasVenta($idVenta){
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
       
    
    
    /**
     * Devuelve el siguiente id no existente en la tabla lineasventa de la base de datos para un determinado identificador de venta.
     * 
     * @param string $idVenta Identificador de la venta.
     * 
     * @return int
     */
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
    
    
    
    /**
     * Obtiene el tipo de entrada asociado a la linea de venta determinada por los atributos $this->idEvento y $this->idTipoEntrada.
     * 
     * @return TipoEntrada
     */
    public function getTipoEntrada(){
        return TipoEntrada::getTipoEntrada($this->idEvento, $this->idTipoEntrada);
    }

    
    
    /**
     * Transforma un array asociativo con claves iguales a las columnas de la tabla LineasVenta de la base de datos.
     * 
     * [{Id, Id_Venta, Id_Evento. Id_TipoEntrada, Precio, Cantidad, Estado}]
     * 
     * @param array $array Array con las claves definidas en la descripcion.
     * 
     * @return LineaVenta
     */
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