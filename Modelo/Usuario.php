<?php
require_once APP_ROOT . '/Modelo/ModeloBD.php';
require_once APP_ROOT . '/Modelo/Tool.php';

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
    
    public function login_users($email,$password)
    {
        
        try {
            
            $sql = "SELECT * from usuarios WHERE Email=?";
            $query = $this->dbh->prepare($sql);
            $query->bindParam(1,$email);
            $query->execute();
                       
            $usuario=$query->fetch(PDO::FETCH_ASSOC);
            
            $this->dbh = null;
            
            if($usuario!=false){
                if(password_verify($password,$usuario['Password'])){
                    $_SESSION['usuario']['id']=$usuario['Id'];
                    $_SESSION['usuario']['nombre']=$usuario['Nombre'];
                    $_SESSION['usuario']['email']=$usuario['Email'];
                    
                    return TRUE;
                }
            }
        
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }
        
    }
    
    /**
     * 
     * @param int $id
     * @param string $pass
     */
    public static function cambiarPassword($id,$pass){
        if (isset($id) && isset($pass)){
            $dbh=Tool::conectar();
            
            $sql="UPDATE usuarios SET Password=? WHERE Id=?";            
            $query=$dbh->prepare($sql);
            
            $query->bindParam(1,self::getHash($pass));
            $query->bindParam(2,$id);
            $query->execute();
            
            
            Tool::desconectar($dbh);
        }
    }
    
    public static function getHash($pass){
        return password_hash($pass, PASSWORD_DEFAULT);
    }
    
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
        
    }
    
}
