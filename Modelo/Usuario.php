<?php
require_once APP_ROOT . '/Modelo/ModeloBD.php';
require_once APP_ROOT . '/Modelo/Tool.php';

class Usuario
{
    
    private static $instancia;
    private static $user;
    public static $ultimoError;
    
    private $dbh;
    
    public $id;
    public $email;
    public $nombre;
    public $passHash;
    
    private function __construct()
    {
        
        $this->dbh = ModeloBD::getConexion();
        
    }
    
    /**
     * 
     * @return Usuario
     */
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
                    $_SESSION['usuario']['rol']=$usuario['Rol'];
                    
                    $this->user=self::adaptaArrayAObjeto($usuario);
                    
                    return TRUE;
                }
            }
        
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }
        
    }
    
    /**
     * 
     * @param string $email
     * @param string $nombre
     * @param string $pass
     * @param boolean $confirmado
     */
    public static function registroUsuario($email,$nombre,$pass,$confirmado=0){
        $dbh=Tool::conectar();
        
        if(self::existeEmail($email)){
           return false; 
        }
        
        if(!(isset($email) && isset($nombre) && isset($pass) && filter_var($email,FILTER_VALIDATE_EMAIL))){
            return false;
        }
        
        $pass=self::getHash($pass);
        $code=self::getHash(date("Y-M-d H:m:s") . $email );
        //$confirmado=0;
        $rol="cliente";
        
        $sql="INSERT INTO usuarios (Email,Nombre,Password,Codigo_confirmacion,Confirmado,Rol) VALUES (?,?,?,?,?,?)";
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$email);
        $query->bindParam(2,$nombre);
        $query->bindParam(3,$pass);
        $query->bindParam(4,$code);
        $query->bindParam(5,$confirmado);
        $query->bindParam(6,$rol);
        $query->execute();
        
        Tool::desconectar($dbh);
        
        if($query->rowCount()<=0){
            self::$ultimoError="Error guardando nuevo usuario";
            return false;
        }
        else{
            if(!$confirmado){
                return self::enviarEmailConfirmacion($email);
            }
            else{
                return true;
            }
        }
    }
    
    public static function getUsuarioLogeado(){
        return self::$user;
    }
        
    /**
     * 
     * @param string $email
     */
    public static function existeEmail($email){
        $dbh=Tool::conectar();
        
        $sql="SELECT * FROM usuarios WHERE Email=?";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$email);
        $query->execute();
        
        Tool::desconectar($dbh);
        return ($query->rowCount()>0);
    }
    
    /**
     * 
     * @param string $email
     */
    public static function enviarEmailConfirmacion($email){
        $dbh=Tool::conectar();
        
        $sql="SELECT Codigo_confirmacion FROM usuarios WHERE Email=? LIMIT 1";
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$email);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_ASSOC);
        
        Tool::desconectar($dbh);
        
        if($query->rowCount()<=0){
            self::$ultimoError="Error obteniendo codigo de confirmación";
            return false;
        }
        else{
            $asunto="Confirmación de registro";
            $mensaje="Confirma tu registro en <a href='". Tool::getBaseURL() . "Controlador/Usuario/confirmarRegistro.php?codigo=" . $res['Codigo_confirmacion'] . "&email=" . urlencode($email) . "'> este enlace </a>";
            $headers="X-Mailer: PHP/" . phpversion();
            
            return Tool::enviaEmail($email, $asunto, $mensaje, $headers);
            
            
            /*
            if($res=mail($email,$asunto,$mensaje,$headers)){                
                return true;
            }
            else{
                self::$ultimoError="Error enviando email";
                return false;                
            }
            */
        }
    }
    
    /**
     * 
     * @param string $email
     * @param string $codigo
     */
    public static function activarCuentaUsuario($email,$codigo){
        $dbh=Tool::conectar();
        
        $sql="SELECT * FROM usuarios WHERE Email=? AND Codigo_confirmacion=? LIMIT 1";
        $query=$dbh->prepare($sql);
        $query->execute([$email,$codigo]);
        $usuario=$query->fetch(PDO::FETCH_ASSOC);
        
        if(count($usuario)>0){
            //El email y codigo existen            
            if($usuario['Confirmado']==1){
                //Ya está confirmado
                $res=true;
            }
            else{ 
                //Confirmamos usuario
                $sql="UPDATE usuarios SET Confirmado=1 WHERE Email=? AND Codigo_confirmacion=? LIMIT 1";
                $query=$dbh->prepare($sql);                
                $query->execute([$email,$codigo]);
                
                if($query->rowCount()>0){
                    //Exito al confirmar
                    $_SESSION['usuario']['id']=$usuario['Id'];
                    $_SESSION['usuario']['nombre']=$usuario['Nombre'];
                    $_SESSION['usuario']['email']=$usuario['Email'];
                    $_SESSION['usuario']['rol']=$usuario['Rol'];
                    
                    self::$user=self::adaptaArrayAObjeto($usuario);
                    $res=true;
                }
                else{
                    self::$ultimoError="Error al confirmar cuenta de usuario";
                    $res=false;
                }
            }
        }
        else{
            self::$ultimoError="El usuario no con email y codigo con existe";
            $res=false;
        }
        
        Tool::desconectar($dbh);
        return $res;
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
       
    /**
     * 
     * @param array $usuario
     */
    private static function adaptaArrayAObjeto($usuario){
        $u=new Usuario();
        
        $u->id=$usuario['Id'];
        $u->email=$usuario['Email'];
        $u->nombre=$usuario['Nombre'];
        $u->passHash=$usuario['Password'];
        
        return $u;
    }
    
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
        
    }
    
}
