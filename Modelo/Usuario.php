<?php
/**
 * Clase Usuario | Modelo/Usuario.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */


require_once APP_ROOT . '/Modelo/ModeloBD.php';
require_once APP_ROOT . '/Modelo/Tool.php';


/**
 * Esta clase modela un usuario de la aplicacion.
 *
 */
class Usuario
{
    
    private static $instancia;
    private static $user;
    public static $ultimoError;
    
    /**
     * Handler de la conexion con la base de datos.
     * @var ModeloBD
     */
    private $dbh;
    
    /**
     * Identificador del usuario.
     * @var int 
     */
    public $id;
    
    /**
     * Email del usuario.
     * @var string
     */
    public $email;
    
    /**
     * Nombre completo del usuario.
     * @var string
     */
    public $nombre;
    
    /**
     * Hash del password del usuario.
     * @var string
     */
    public $passHash;
    
    
    /**
     * Constructor de la clase
     */
    private function __construct()
    {
        
        $this->dbh = ModeloBD::getConexion();
        
    }
    
    
    /**
     * Comprueba si el email y el codigo de confirmacion son correctos. En caso afirmativo se actualiza el registro de usuario en la base de datos y se configura la variable de sesion $_SESSION['usuario']{id,nombe,email,rol}.
     *
     * @param string $email Email del usuario.
     * @param string $codigo Codigo de confirmacion de cuenta de usuario.
     *
     * @return boolean True en caso de exito, false en caso contrario.
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
     * Modifica el hash de password asociado al usuario pasado como parametro de entrada en la base de datos.
     *
     * @param int $id Identificador el usuario.
     * @param string $pass Nuevo password.
     *
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
    
    
    
    /**
     * Determina si existe un usuario en la base de datos con el email pasado como parametro de entrada.
     *
     * @param string $email Email del usuario.
     *
     * @return int|boolean Devuelve el id del (primer)usuario con el email pasado como entrada, false si no existe.
     */
    public static function existeEmail($email){
        $dbh=Tool::conectar();
        
        $sql="SELECT * FROM usuarios WHERE Email=?";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$email);
        $query->execute();
        
        if($query->rowCount()>0){
            $aux=$query->fetch(PDO::FETCH_ASSOC);
            $out=$aux['Id'];
        }
        else{
            $out=false;
        }
        
        Tool::desconectar($dbh);
        return $out;
    }
    
    /**
     * Envia un email con un codigo de confirmacion de usuario a la direccion pasada como parametro.
     *
     * @param string $email Email del Usuario
     *
     * @return boolean True si el email se envia con exito, false en caso contrario.
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
            
            return Tool::enviaEmail($email,"druida@transitionfestival.org","Test Mailer", $asunto, $mensaje, $headers);
            
            
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
     * Obtiene un hash del password pasado como parametro de entrada.
     *
     * @param string $pass Password.
     *
     * @return string
     */
    public static function getHash($pass){
        return password_hash($pass, PASSWORD_DEFAULT);
    }
    
    
    
    /**
     * Obtiene un usuario de la base de datos determinado por el parametro de entrada.
     *
     * @param int $id Identificador del usuario.
     *
     * @return Usuario|boolean False en caso de que el id no corresponde a ningun usuario.
     */
    public static function getUsuario($id){
        $dbh=Tool::conectar();
        
        $sql="SELECT * FROM usuarios WHERE Id=?";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->execute();
        
        Tool::desconectar($dbh);
        
        if($query->rowCount()>0){
            $aux=$query->fetch(PDO::FETCH_ASSOC);
            return self::adaptaArrayAObjeto($aux);
        }
        else{
            return false;
        }
    }
    
    
    
    /**
     * Obtiene el usuario logeado actualmente.
     *
     * @return Usuario
     */
    public static function getUsuarioLogeado(){
        return self::$user;
    }
    
    
    
    /**
     * Obtiene el usuario con el email deteminado por el parametro de entrada.
     *
     * @param string $email
     * @return Usuario|boolean
     */
    public static function getUsuarioPorEmail($email){
        $dbh=Tool::conectar();
        
        $sql="SELECT * FROM usuarios WHERE email=?";
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$email);
        $query->execute();
        
        Tool::desconectar($dbh);
        
        if($query->rowCount()>0){
            $aux=$query->fetch(PDO::FETCH_ASSOC);
            return self::adaptaArrayAObjeto($aux);
        }
        else{
            return false;
        }
    }
    
    
    
    
    /**
     * Comprueba que el login de usuario con los parametros de entrada es correcto. 
     * 
     * En caso de login exitoso, esta funcion configura la variable de sesion $_SESSION['usuario']:{id,nombre,email,rol} y se guarda la informacion en los atributos del objeto actual ($this).
     * 
     * @param string $email Email del usuario.
     * @param string $password Password del usuario.
     * 
     * @return boolean True si el login del usuario tiene exito, false en caso contrario.
     */
    public function login_users($email,$password)
    {        
        $res=false;
        
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
                    
                    $res=true;
                }
            }
        
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }
        finally {
            return $res;
        }
        
    }
    
    /**
     * Registra un nuevo usuario en la base de datos.
     * 
     * @param string $email Email del usuario.
     * @param string $nombre Nombre completo del usuario.
     * @param string $pass Password del usuario.
     * @param boolean $confirmado Si el valor es false(por defecto) se registra el usuario como "no confirmado" y se envia un email de verificacion. En caso contrario se registra como confirmado.
     * 
     * @return boolean Registro con exito.
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
    
    
    /**
     * Obtiene un objeto Usuario usando un patron sigleton
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
    
    
    
    
    /**
     * Transforma un array asociativo con claves iguales a las columnas de la tabla Entradas de la base de datos.
     * 
     * @param array $usuario Array con la definicion {Id,Email,Nombre,Password}
     * 
     * @return Usuario
     */
    private static function adaptaArrayAObjeto($usuario){
        $u=new Usuario();
        
        $u->id=$usuario['Id'];
        $u->email=$usuario['Email'];
        $u->nombre=$usuario['Nombre'];
        $u->passHash=$usuario['Password'];
        
        return $u;
    }
    
    /**
     * Evita la clonacion del objeto.
     */
    public function __clone()
    {
        
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
        
    }
    
}
