<?php
/**
 * Clase Entrada | Modelo/Entrada.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */

require_once APP_ROOT . '/Modelo/Tool.php';
require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

/**
 * Esta clase modela una entrada individual.
 *
 */
class Entrada{
    
    /**
     * @var string Codigo identificativo unico de la entrada.
     */
    public $codigo;
    
    /**
     * @var int Identificador del evento al que corresponde la entrada. 
     */
    public $idEvento;
    
    /**
     * @var int Identificador del tipo de entrada.
     */
    public $idTipoEntrada;
    
    /**
     * @var int Identificador del usuario que realiza la compra de la entrada.
     */
    public $idUsuario;    
    
    /**
     * @var string  Identificador de la venta de la entrada.
     */
    public $idVenta;
    
    /**
     * @var int Identificador de la linea de venta a la que corresponde la entrada.
     */
    public $idLineaVenta;
    
    /**
     * @var ModeloBD Handler de la conexion con la base de datos.
     */
    private static $dbh;
    
    
    
    /**
     * Genera un codigo que no este ya registrado por otra entrada en la base de datos de longitud determinada por la constante TICKET_CODE_LENGTH.
     * 
     * @param ModeloBD $dbh Handler de la conexion con la base de datos. Si se pasa el parametro de entrada la conexion no se cierra despues de realizar la operacion.
     *  
     * @return string Codigo para la entrada verificado que no esta duplicado en la base de datos.
     */
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
    
    /**
     * Obtiene un objeto Entrada desde la base de datos seleccionado por su codigo.
     * 
     * @param string $codigo Codigo de la entrada a obtener.      
     * @param ModeloBD $dbh Handler de la conexion con la base de datos. Si se pasa el parametro de entrada la conexion no se cierra despues de realizar la operacion.
     * 
     * @return Entrada
     */
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
    
    /**
     * Obtiene en un array todas las entradas de una determinada venta en la base de datos.
     * 
     * @param string $idVenta Identificador de la venta.
     *  
     * @return Entrada[]
     */
    public static function getEntradasPorVenta($idVenta){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Venta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1, $idVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    /**
     * Obtiene en un array todas las entradas de una determinada linea de venta en la base de datos.
     *  
     * @param string $idVenta Identificador de la venta.
     * @param int $idLineaVenta Identificador de la linea de venta.
     * 
     * @return Entrada[]
     */
    public static function getEntradasPorLineaVenta($idVenta,$idLineaVenta){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Venta=? AND Id_LineaVenta=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idVenta);
        $query->bindParam(2,$idLineaVenta);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    
    /**
     * Obtiene en un array todas las entradas de un determinado usuario y un determinado evento de la base de datos.
     * 
     * @param int $idEvento Identificador del evento.
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return Entrada[]
     */
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
    
    /**
     * Obtiene en un array todas las entradas de un determinado evento de la base de datos.
     * 
     * @param int $idEvento Identificador del Evento
     * 
     * @return Entrada[]
     */
    public static function getAllEntradasEvento($idEvento){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM entradas WHERE Id_Evento=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idEvento);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
     
    /**
     * Obtiene en un array todas las entradas de un determinado usuario de la base de datos.
     * 
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return Entrada[]
     */
    public static function getAllEntradasUsuario($idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT e.Codigo,e.Id_TipoEntrada,e.Id_Evento,e.Id_Venta,e.Id_LineaVenta,v.Id_Usuario FROM entradas AS e 
                INNER JOIN ventas AS v ON e.Id_Venta=v.Id 
                WHERE v.Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
     
    /**
     * Inserta una nueva entrada en la base de datos con los parametros especificados. En caso de pasar un handler de la base de datos como parametro de entrada no se cerrara la conexion al terminar la operacion.
     * 
     * @param int       $idTipoEntrada  Identificador del tipo de entrada.
     * @param int       $idEvento       Identificador del evento.
     * @param string    $idVenta        Identificador de la venta.
     * @param int       $idLineaVenta   Identificador de la linea de venta.
     * @param int       $idUsuario      Identificador del usuario.
     * @param ModeloBD  $dbh            Handler de la conexion con la base de datos.
     */
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
    
    /**
     * Devuelve un objeto Evento con el que esta asociado la entrada.
     * 
     * @return Evento
     */
    public function getEvento(){
        return Evento::getEvento($this->idEvento);
    }
        
    /**
     * Devuelve un objeto TipoEntrada con el que esta asociado la entrada.
     * 
     * @return TipoEntrada
     */
    public function getTipoEntrada(){
        return TipoEntrada::getTipoEntrada($this->idEvento, $this->idTipoEntrada);
    }
        
    /**
     * Devuelve un registro de entrada en la base de datos como un objeto Entrada.
     * 
     * @param array $array Array con la definicion { [Codigo, Id_TipoEntrada, Id_Evento, Id_Venta, Id_LineaVenta, Id_Usuario] }
     * 
     * @return Entrada
     */
    private static function adaptaArrayAObjeto($array){
        $e=new Entrada();
        
        $e->codigo=$array['Codigo'];
        $e->idTipoEntrada=$array['Id_TipoEntrada'];
        $e->idEvento=$array['Id_Evento'];
        $e->idVenta=$array['Id_Venta'];
        $e->idLineaVenta=$array['Id_LineaVenta'];
        $e->idUsuario=$array['Id_Usuario'];
        
        
        return $e;
    }
    
    /**
     * Devuelve varios registros de entradas de la base de datos como un array de objetos Entrada
     * 
     * @param array $array Array con la definicion { {[Codigo, Id_TipoEntrada, Id_Evento, Id_Venta, Id_LineaVenta, Id_Usuario]}, ..... }
     * 
     * @return Entrada[]
     */
    private static function arrayDeObjetos($array){
        $i=0;
        $res=array();
        
        foreach($array as $r){
            $res[$i]=self::adaptaArrayAObjeto($r);
            $i++;
        }
        
        return $res;
    }
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Modifica el registro de la base de datos determinado por el atributo codigo del objeto actual con los valores de los atributos del objeto actual.
     * 
     */
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
    
    /**
     * Elimina el registro de la base de datos determinado por el campo codigo pasado como parametro de entrada.
     * 
     * @param string $codigo Codigo de la entrada a eliminar.
     */
    public static function eliminarEntrada($codigo){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM entradas WHERE Codigo=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$codigo);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
    }
    
}