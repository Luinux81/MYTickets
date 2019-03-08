<?php

/**
 * Clase para gestionar la conexi�n a la base de datos
 * 
 * Esta clase gestiona la conexi�n a la base de datos implementando un patron singleton
 * 
 * @author Luis Bre�a Calvo
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
     * Esta funci�n prepara una sentencia SQL para ser ejecutada. Devuelve un objeto PDOStatement
     * 
     * @param string $sql
     * @return PDOStatement
     */
    public function prepare($sql)
    {        
        return $this->dbh->prepare($sql);        
    }
    
    /**
     * Est� funci�n inicia una transacci�n en la base de datos
     * 
     * @return boolean
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    /**
     * Est� funci�n confirma una transacci�n en la base de datos
     * 
     * @return boolean
     */
    public function commit(){
        return $this->dbh->commit();
    }
    
    /**
     * Est� funci�n cancela una transacci�n en la base de datos
     * 
     * @return boolean
     */
    public function rollBack(){
        return $this->dbh->rollBack();
    }
   
    /**
     * Esta funci�n devuelve un objeto ModeloDB para gestionar las operaciones con la base de datos
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
        trigger_error('La clonaci�n de este objeto no est� permitida', E_USER_ERROR);        
    }
    
}

?>