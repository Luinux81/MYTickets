<?php
/**
 * Clase PerfilOrganizador | Modelo/PerfilOrganizador.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */

require_once '../Modelo/Tool.php';

/**
 * Esta clase modela un perfil de organizador de eventos.
 *
 */
class PerfilOrganizador
{
    /**
     * Identificador del perfil de organizador.
     * @var int
     */
    public $id;
    
    /**
     * Identificador del usuario.
     * @var int
     */
    public $idUsuario;
    
    /**
     * Nombre del perfil de organizador.
     * @var string
     */
    public $nombre;
    
    /**
     * Descripcion del perfil de organizador.
     * @var string
     */
    public $descripcion;
    
    /**
     * Mostrar descripcion 
     * @var string
     */
    public $mostrarDescripcion;
    
    /**
     * URL del sitio web
     * @var string
     */
    public $website;
    
    /**
     * URL del perfil de Facebook.
     * @var string
     */
    public $facebook;
    
    /**
     * URL del perfil de Twitter.
     * @var string
     */
    public $twitter;
    
    /**
     * URL del perfil de Instagram.
     * @var string
     */
    public $instagram;
    
    /**
     * Imagen del perfil.
     * @var string
     */
    public $imagen;
    
    /**
     * Handler de la conexion con la base de datos.
     * @var ModeloBD
     */
    private static $dbh;
    
    /**
     * Inserta un nuevo perfil de organizador en la base de datos con los valores del objeto actual ($this).
     */
    public function crearPerfilOrganizador(){
        self::$dbh=Tool::conectar();
        
        $sql="INSERT INTO perfilesorganizador (Id,Id_Usuario,Nombre,Descripcion,Website,Facebook,Twitter,Instagram,Mostrar_descripcion,Imagen) VALUES (?,?,?,?,?,?,?,?,?,?)";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$this->id);
        $query->bindParam(2,$this->idUsuario);
        $query->bindParam(3,$this->nombre);
        $query->bindParam(4,$this->descripcion);
        $query->bindParam(5,$this->website);
        $query->bindParam(6,$this->facebook);
        $query->bindParam(7,$this->twitter);
        $query->bindParam(8,$this->instagram);
        $query->bindParam(9,$this->mostrarDescripcion);
        $query->bindParam(10,$this->imagen);
        $query->execute();
        
        //print_r($query->errorInfo());
        
        Tool::desconectar(self::$dbh);
    }
    
    
    /**
     * Modifica un registro de perfil de organizador en la base de datos determinado por los parametros de entrada.
     * 
     * @param int $id Identificador del perfil de organizador.
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return array Devuelve un array con la informacion de error de PDOStatement->errorInfo().
     */
    public function editarPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="UPDATE perfilesorganizador SET ".
            "Nombre=?,Descripcion=?,Website=?,Facebook=?,Twitter=?,Instagram=?,Mostrar_descripcion=?,Imagen=?  ".
            "WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->website);
        $query->bindParam(4,$this->facebook);
        $query->bindParam(5,$this->twitter);
        $query->bindParam(6,$this->instagram);
        $query->bindParam(7,$this->mostrarDescripcion);
        $query->bindParam(8,$this->imagen);
        $query->bindParam(9,$this->id);
        $query->bindParam(10,$this->idUsuario);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
        
        return $query->errorInfo();
    }
    
    
    /**
     * Elimina un registro de perfil de organizador en la base de datos determinado por los parametros de entrada.
     *  
     * @param int $id Identificador del perfil de organizador.
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return array Devuelve un array con la informacion de error de PDOStatement->errorInfo().
     */
    public static function eliminarPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="DELETE FROM perfilesorganizador WHERE Id=? AND Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$id);
        $query->bindParam(2,$idUsuario);
        $query->execute();
        
        
        Tool::desconectar(self::$dbh);
        
        return $query->errorInfo();
    }
    
    
    /**
     * Obtiene un perfil de organizador de la base de datos determinado por los parametros de entrada.
     * 
     * @param int $id Identificador del perfil de organizador.
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return PerfilOrganizador
     */
    public static function getPerfilOrganizador($id,$idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=? AND Id=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->bindParam(2,$id);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::adaptaArrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    
    
   /**
   * Obtiene un array con todos los perfiles de organizador de un usuario determinado.
   * 
   * @param int $idUsuario Identificador del usuario
   * 
   * @return PerfilOrganizador[]
   */  
    public static function getAllPerfilesOrganizador($idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM perfilesorganizador WHERE Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        Tool::desconectar(self::$dbh);
        
        return self::arrayDeObjetos($query->fetchAll(PDO::FETCH_ASSOC));
    }
    

    /**
     * Devuelve el siguiente id no existente en la tabla perfilesorganizador de la base de datos para un determinado identificador de usuario.
     * 
     * @param int $idUsuario Identificador del usuario.
     * 
     * @return int
     */
    public static function nuevoIdPerfilOrganizador($idUsuario){
        self::$dbh=Tool::conectar();
        
        $sql="SELECT MAX(Id) from perfilesorganizador WHERE Id_Usuario=?";
        
        $query=self::$dbh->prepare($sql);
        $query->bindParam(1,$idUsuario);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_NUM);
        
        Tool::desconectar(self::$dbh);
        
        return $res[0]+1;
    }
    
    
    
    /**
     * Transforma un array asociativo con claves iguales a las columnas de la tabla perfilesOrganizador de la base de datos.
     * 
     * {Id,Id_Usuario,Nombre,Descripcion,Mostrar_descripcion,Website,Facebook,Twitter,Instagram,Imagen}
     * 
     * @param array $array Array con las claves definidas en la descripcion.
     * 
     * @return PerfilOrganizador
     */
    private static function adaptaArrayAObjeto($array){
        $p=new PerfilOrganizador();
        
        $p->id=$array['Id'];
        $p->idUsuario=$array['Id_Usuario'];
        $p->nombre=$array['Nombre'];
        $p->descripcion=$array['Descripcion'];
        $p->mostrarDescripcion=$array['Mostrar_descripcion'];
        $p->website=$array['Website'];
        $p->facebook=$array['Facebook'];
        $p->twitter=$array['Twitter'];
        $p->instagram=$array['Instagram'];
        $p->imagen=$array['Imagen'];
        
        return $p;
    }
    
    
    /**
     * Devuelve varios registros de perfil de organizador de la base de datos como un array de objetos PerfilOrganizador.
     *  
     * @param array $array [ {Id,Id_Usuario,Nombre,Descripcion,Mostrar_descripcion,Website,Facebook,Twitter,Instagram,Imagen} .... {} ]
     * 
     * @return PerfilOrganizador[]
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
}

