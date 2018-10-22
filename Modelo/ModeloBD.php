<?php

class ModeloBD {

    private static $instancia;
    private $dbh;
    
    private function __construct()
    {
        try {
            
            $this->dbh = new PDO('mysql:host=localhost;dbname=mytickets_dev', 'root', '');
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