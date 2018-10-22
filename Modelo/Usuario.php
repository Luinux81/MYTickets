<?php
require_once '../Modelo/ModeloBD.php';


session_start();

class Usuario
{
    
    private static $instancia;
    private $dbh;
    
    private function __construct()
    {
        
        $this->dbh = ModeloBD::getConexion();
        
    }
    
    public static function singleton_login()
    {
        
        if (!isset(self::$instancia)) {
            
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
            
        }
        
        return self::$instancia;
        
    }
    
    public function login_users($nick,$password)
    {
        
        try {
            
            $sql = "SELECT * from usuarios WHERE nombre = ? AND password = ?";
            $query = $this->dbh->prepare($sql);
            $query->bindParam(1,$nick);
            $query->bindParam(2,$password);
            $query->execute();
            $this->dbh = null;
            
            //si existe el usuario
            if($query->rowCount() == 1)
            {
                
                $fila  = $query->fetch();
                $_SESSION['idusuario']=$fila['Id'];
                $_SESSION['nombre'] = $fila['Nombre'];
                return TRUE;
                
            }
            
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }
        
    }
    
    
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
        
    }
    
}
