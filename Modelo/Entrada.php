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
 * Esta clase modela una entrada
 *
 */
class Entrada{
    
    public $codigo;
    public $idEvento;
    public $idTipoEntrada;
    public $idUsuario;    
    public $idVenta;
    public $idLineaVenta;
    
    private static $dbh;
    
    /**
     * Genera un codigo que no este ya registrado por otra entrada en la base de datos. Permite pasar un objeto PDO como conexion a la base de datos, si se pasa el parametro se crea una nueva conexion.
     * @param PDO $dbh
     * @return string
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
     * Obtiene un objeto Entrada desde la base de datos seleccionado por su codigo
     * @param string $codigo
     * @param PDO $dbh
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
     * Obtiene en un array todas las entradas de una determinada venta en la base de datos
     * @param int $idVenta
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
     * Obtiene en un array todas las entradas de una determinada linea de venta en la base de datos 
     * @param string $idVenta
     * @param int $idLineaVenta
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
     * Obtiene en un array todas las entradas de un determinado usuario y un determinado evento de la base de datos
     * @param int $idEvento
     * @param int $idUsuario
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
     * Obtiene en un array todas las entradas de un determinado evento de la base de datos
     * @param int $idEvento
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
     * Obtiene en un array todas las entradas de un determinado usuario de la base de datos
     * @param int $idUsuario
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
     * Inserta una nueva entrada en la base de datos con los parametros especificados. Permite pasar un objeto PDO como conexión a la base de datos, en caso de no pasar esta conexión se creará una nueva
     * @param int $idTipoEntrada
     * @param int $idEvento
     * @param string $idVenta
     * @param int $idLineaVenta
     * @param int $idUsuario
     * @param string $dbh
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
     * Devuelve un objeto Evento con el que está asociado la entrada 
     * @return Evento
     */
    public function getEvento(){
        return Evento::getEvento($this->idEvento);
    }
        
    /**
     * Devuelve un objeto TipoEntrada con el que está asociado la entrada
     * @return TipoEntrada
     */
    public function getTipoEntrada(){
        return TipoEntrada::getTipoEntrada($this->idEvento, $this->idTipoEntrada);
    }
        
    /**
     * Devuelve un registro de entrada en la base de datos como un objeto Entrada
     * @param array $array
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
     * @param array $array
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
    
}