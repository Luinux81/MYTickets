<?php
/**
 * Clase ModeloBD | Modelo/ModeloBD.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */


/**
 * Clase para gestionar la conexion a la base de datos implementando un patron singleton.
 */
class ModeloBD {

    /**
     * Variable para implementar el patron singleton.
     * @var ModeloBD
     */
    private static $instancia;
    
    /**
     * Handler real de la conexion con la base de datos.
     * @var PDO 
     */
    private $dbh;
    
    /**
     * Constructor de la clase
     */
    private function __construct()
    {
        try {
            
            $opciones = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
            );
            
            $this->dbh = new PDO(BD_HOST, BD_USERNAME, BD_PASSWORD, $opciones);
            $this->dbh->exec("SET CHARACTER SET utf8");
            
        } catch (PDOException $e) {            
            print "Error!: " . $e->getMessage();            
            die();
        }
    }
    
    /**
     * Prepara una sentencia SQL para ser ejecutada. Devuelve un objeto PDOStatement
     * 
     * @param string $sql
     * @return PDOStatement
     */
    public function prepare($sql)
    {        
        return $this->dbh->prepare($sql);        
    }
    
    /**
     * Inicia una transaccion en la base de datos
     * 
     * @return boolean
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    /**
     * Confirma una transaccion en cuerso en la base de datos
     * 
     * @return boolean
     */
    public function commit(){
        return $this->dbh->commit();
    }
    
    /**
     * Cancela una transaccion en curso en la base de datos
     * 
     * @return boolean
     */
    public function rollBack(){
        return $this->dbh->rollBack();
    }
   
    /**
     * Obtiene un objeto singleton ModeloDB para gestionar las operaciones con la base de datos
     * 
     * @return ModeloBD 
     */
    public static function getConexion()
    {        
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
            
        }
        
        return self::$instancia;        
    }
    
    
    /**
     * Evita que el objeto se pueda clonar
     */
    public function __clone()
    {        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);        
    }
    
}

?>