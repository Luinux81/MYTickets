<?php
/**
 * Clase Evento | Modelo/Evento.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */


require_once APP_ROOT . '/Modelo/ModeloBD.php';

/**
 * Esta clase modela un tipo de entrada de un evento 
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 *
 */
class TipoEntrada
{
    /**
     * @var int Identificador del evento.
     */
    public $id;
    
    /**
     * @var int Identificador del evento.
     */
    public $eventoId;
    
    /**
     * @var string Nombre del tipo de entrada.
     */    
    public $nombre;
    
    /**
     * @var string Descripcion del tipo de entrada.
     */    
    public $descripcion;
    
    /**
     * @var string Determina si la descripcion se muestra o no.
     */    
    public $mostrar_descripcion;
    
    /**
     * @var int Cantidad disponible del tipo de entrada para su venta.
     */    
    public $cantidad_disponible;
    
    /**
     * @var float Precio individual del tipo de entrada.
     */    
    public $precio;
    
    /**
     * @var string Canales de venta disponibles para el tipo de entrada.
     */    
    public $canales_venta;
    
    /**
     * @var string Impuestos aplicable al tipo de entrada.
     */    
    public $impuestos;
    
    /**
     * @var string Fecha inicial en la que el tipo de entrada esta disponible para su venta.
     */    
    public $inicio_venta;
    
    /**
     * @var string Fecha final en la que el tipo de entrada esta disponible para su venta.
     */    
    public $fin_venta;
    
    /**
     * @var string Visibilidad del tipo de entrada.
     */    
    public $visibilidad;
    
    /**
     * @var int Minimo numero de entradas disponible para adquirir en una venta determinada.
     */    
    public $minimo_compra;
    
    /**
     * @var int Maximo numero de entradas disponible para adquirir en una venta determinada.
     */    
    public $maximo_compra;
    
    /**
     * @var string Grupo del tipo de entrada.
     */    
    public $grupo;
    
    /**
     * @var string Estado del tipo de entrada.
     */    
    public $estado;
    
    /**
     * @var ModeloBD Handler de la conexion con la base de datos.
     */    
    private static $dbh;
    
    /**
     * Inserta un nuevo registro de tipo de entrada en la base de datos con los valores de los atributos del objeto actual ($this).
     */
    public function crearTipoEntrada(){
        $this->conectar();
        
        $sql="INSERT INTO tiposentrada "
            ."(Id,Id_Evento,Nombre,Descripcion,Mostrar_descripcion,Cantidad_disponible,Precio,"
            ."Canales_venta,Impuestos,Inicio_venta,Fin_venta,Visibilidad,Minimo_compra,Maximo_compra,Grupo_tipoEntrada,Estado) "
            ."VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->id);
        $query->bindParam(2,$this->eventoId);
        $query->bindParam(3,$this->nombre);
        $query->bindParam(4,$this->descripcion);
        $query->bindParam(5,$this->mostrar_descripcion);
        $query->bindParam(6,$this->cantidad_disponible);
        $query->bindParam(7,$this->precio);
        $query->bindParam(8,$this->canales_venta);
        $query->bindParam(9,$this->impuestos);
        $query->bindParam(10,$this->inicio_venta);
        $query->bindParam(11,$this->fin_venta);
        $query->bindParam(12,$this->visibilidad);
        $query->bindParam(13,$this->minimo_compra);
        $query->bindParam(14,$this->maximo_compra);
        $query->bindParam(15,$this->grupo);
        $query->bindParam(16,$this->estado);
        
        $query->execute();
        
        $this->desconectar();
    }
  
    /**
     * Modifica un registro de tipo de entrada en la base de datos determinado por $this->id con los valores de los atributos del objeto actual ($this).
     */
    public function editarTipoEntrada(){
        $this->conectar();
        
        $sql="UPDATE tiposentrada SET "
        ."Nombre=?,"
        ."Descripcion=?,"
        ."Mostrar_descripcion=?,"
        ."Cantidad_disponible=?,"
        ."Precio=?,"
        ."Canales_venta=?,"
        ."Impuestos=?,"
        ."Inicio_venta=?,"
        ."Fin_venta=?,"
        ."Visibilidad=?,"
        ."Minimo_compra=?,"
        ."Maximo_compra=?,"
        ."Grupo_tipoEntrada=?,"
        ."Estado=? "
            ."WHERE Id=? AND Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->mostrar_descripcion);
        $query->bindParam(4,$this->cantidad_disponible);
        $query->bindParam(5,$this->precio);
        $query->bindParam(6,$this->canales_venta);
        $query->bindParam(7,$this->impuestos);
        $query->bindParam(8,$this->inicio_venta);
        $query->bindParam(9,$this->fin_venta);
        $query->bindParam(10,$this->visibilidad);
        $query->bindParam(11,$this->minimo_compra);
        $query->bindParam(12,$this->maximo_compra);
        $query->bindParam(13,$this->grupo);
        $query->bindParam(14,$this->estado);
        $query->bindParam(15,$this->id);
        $query->bindParam(16,$this->eventoId);
        
        $query->execute();
        
        $this->desconectar();
    }
    
    /**
     * Elimina un registro de tipo de entrada de la base de datos determinado por los parametros de entrada
     * 
     * @param int $eid Identificador del evento.
     * @param int $teid Identificador del tipo de entrada.
     */
    public static function eliminarTipoEntrada($eid,$teid){
        TipoEntrada::conectar();
        
        $sql="DELETE FROM tiposentrada WHERE Id_Evento=? AND Id=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        $query->bindParam(2,$teid);
        
        $query->execute();
        
        TipoEntrada::desconectar();        
    }
    


    /**
     * Obtiene un array con todos los tipos de entrada de un determinado evento determinado por el parametro de entrada.
     * 
     * @param int $eid Identificador del evento.
     * 
     * @return TipoEntrada[]
     */
    public static function getAllTipoEntradas($eid){
        TipoEntrada::conectar();
        
        $sql="SELECT * FROM tiposentrada WHERE Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        $query->bindParam(1,$eid);
        $query->execute();
        
        TipoEntrada::desconectar();
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        
        $out=array();
        $i=0;
        foreach($res as $r){
            $out[$i]=TipoEntrada::arrayAObjeto($r);
            $i++;
        }
        return $out; 
    }
    
    /**
     * Obtiene un tipo de entrada determinado por los parametros de entrada.
     * 
     * @param int $eid  Identificador del evento.
     * @param int $tpid Identificador del tipo de entrada.
     *  
     * @return TipoEntrada
     */
    public static function getTipoEntrada($eid,$tpid){
        TipoEntrada::conectar();
        
        $sql="SELECT * FROM tiposentrada WHERE Id_Evento=? AND Id=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        $query->bindParam(2,$tpid);
        
        $query->execute();
        
        TipoEntrada::desconectar();
        
        return TipoEntrada::arrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    
    /**
     * Obtiene el siguiente identificador valido para un registro nuevo en la base de datos de un tipo de entrada asociado al evento determinado con el parametro de entrada.
     *
     * @param int $eid Identificador del evento.
     *
     * @return int
     */
    public static function nuevoIdTipoEntrada($eid){
        TipoEntrada::conectar();
        
        $sql="SELECT MAX(Id) from tiposentrada WHERE Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_NUM);
        
        TipoEntrada::desconectar();
        
        return $res[0]+1;
    }
    
    
    
    /**
     * Define el handler de la conexion con la base de datos.
     */
    private static function conectar(){
        TipoEntrada::$dbh=ModeloBD::getConexion();
    }
    
    /**
     * Elimina el handler de la conexion con la base de datos.
     */
    private static function desconectar(){
        TipoEntrada::$dbh=null;
    }
    
    /**
     * Transforma un array asociativo con claves iguales a las columnas de la tabla TiposEntrada de la base de datos.
     * 
     *  [{Id, Id_evento,  Nombre, Descripcion, Mostrar_descripcion, Cantidad_disponible, Precio, Canales_venta, Impuestos, Inicio_venta, Fin_venta, Visibilidad, Minimo_compra, Maximo_compra, Grupo_tipoEntrada, Estado}]
     *
     * @param array $arrayTp Array con las claves definidas en la descripcion.
     *
     * @return TipoEntrada
     */
    private static function arrayAObjeto($arrayTp){
        $tp=new TipoEntrada();
        
        $tp->id=$arrayTp['Id'];
        $tp->eventoId=$arrayTp['Id_Evento'];
        $tp->nombre=$arrayTp['Nombre'];
        $tp->descripcion=$arrayTp['Descripcion'];
        $tp->mostrar_descripcion=$arrayTp['Mostrar_descripcion'];
        $tp->cantidad_disponible=$arrayTp['Cantidad_disponible'];
        $tp->precio=$arrayTp['Precio'];
        $tp->canales_venta=$arrayTp['Canales_venta'];
        $tp->impuestos=$arrayTp['Impuestos'];
        $tp->inicio_venta=$arrayTp['Inicio_venta'];
        $tp->fin_venta=$arrayTp['Fin_venta'];
        $tp->visibilidad=$arrayTp['Visibilidad'];
        $tp->minimo_compra=$arrayTp['Minimo_compra'];
        $tp->maximo_compra=$arrayTp['Maximo_compra'];
        $tp->grupo=$arrayTp['Grupo_tipoEntrada'];
        $tp->estado=$arrayTp['Estado'];
        
        return $tp;
    }

}

