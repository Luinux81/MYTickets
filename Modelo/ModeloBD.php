<?php

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
    
    public function prepare($sql)
    {        
        return $this->dbh->prepare($sql);        
    }
    
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    public function commit(){
        return $this->dbh->commit();
    }
    
    public function rollBack(){
        return $this->dbh->rollBack();
    }
    
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