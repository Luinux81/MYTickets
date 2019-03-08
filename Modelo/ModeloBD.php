<?php

/**
 * Clase para gestionar la conexión a la base de datos
 * 
 * Esta clase gestiona la conexión a la base de datos implementando un patron singleton
 * 
 * @author Luis Breña Calvo
 *
 */
class ModeloBD {

    private static $instancia;
    private $dbh;
    
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
     * Esta función prepara una sentencia SQL para ser ejecutada. Devuelve un objeto PDOStatement
     * 
     * @param string $sql
     * @return PDOStatement
     */
    public function prepare($sql)
    {        
        return $this->dbh->prepare($sql);        
    }
    
    /**
     * Está función inicia una transacción en la base de datos
     * 
     * @return boolean
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    /**
     * Está función confirma una transacción en la base de datos
     * 
     * @return boolean
     */
    public function commit(){
        return $this->dbh->commit();
    }
    
    /**
     * Está función cancela una transacción en la base de datos
     * 
     * @return boolean
     */
    public function rollBack(){
        return $this->dbh->rollBack();
    }
   
    /**
     * Esta función devuelve un objeto ModeloDB para gestionar las operaciones con la base de datos
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
    
    
    // Evita que el objeto se pueda clonar
    public function __clone()
    {        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);        
    }
    
}

?>