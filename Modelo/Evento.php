<?php
/**
 * Clase Evento | Modelo/Evento.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */


require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Tool.php';


/**
 * Esta clase modela un evento
 * 
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 *
 */
class Evento{
    /**
     * @var int Identificador del evento.
     */
    public $id;

    /**
     * @var string Nombre del evento.
     */
    public $nombre;
    
    /**
     * @var string Descripcion del evento.
     */
    public $descripcion;
    
    /**
     * @var string Fecha de inicio del evento.
     */
    public $fecha_inicio;
    
    /**
     * @var string Hora de inicio del evento.
     */
    public $hora_inicio;
    
    /**
     * @var string Fecha de finalizacion del evento.
     */
    public $fecha_fin;
    
    /**
     * @var string Hora de finalizacion del evento.
     */
    public $hora_fin;
    
    /**
     * @var int Aforo del evento.
     */
    public $aforo;
    
    /**
     * @var string Nombre del local donde se realiza el evento.
     */
    public $local;
    
    /**
     * @var string Direccion del local donde se realiza el evento.
     */
    public $direccion;
    
    /**
     * @var string Ciudad donde se realiza el evento.
     */
    public $ciudad;
    
    /**
     * @var string Pais donde se realiza el evento.
     */
    public $pais;
    
    /**
     * @var string Coordenadas GPS del evento.
     */
    public $gps;
    
    /**
     * @var string Estado del evento.
     */
    public $estado;
    
    /**
     * @var string Imagen del evento.
     */
    public $imagen;
    
    /**
     * @var ModeloBD Handler de la conexion con la base de datos.
     */
    private static $dbh;
    
    /**
     * Constructor de la clase
     * @param string $nombre Nombre del evento.
     * @param string $descripcion Descripcion del evento.
     * @param string $fecha_inicio Fecha de inicio del evento.
     * @param string $fecha_fin Fecha de finalizacion del evento.
     */
    public function __construct($nombre,$descripcion,$fecha_inicio,$fecha_fin){
        $this->nombre=$nombre;
        $this->descripcion=$descripcion;
        $this->fecha_inicio=$fecha_inicio;
        $this->fecha_fin=$fecha_fin;        
    }
    
    /**
     * Inserta un nuevo registro de evento en la base de datos con los valores de los atributos del objeto actual ($this).
     */
    public function guardarEventoEnBD(){
        Evento::$dbh=Tool::conectar();
        
        $sql="INSERT INTO eventos (Nombre,Descripcion,Fecha_inicio,Fecha_fin,Aforo,Local,Direccion,Ciudad,Pais,GPS,Imagen) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        
        $query=Evento::$dbh->prepare($sql);
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        
        $aux=Tool::adaptaFechaHora($this->fecha_inicio, $this->hora_inicio);
        $query->bindParam(3,$aux);
        
        $aux=Tool::adaptaFechaHora($this->fecha_fin, $this->hora_fin);
        $query->bindParam(4,$aux);
        
        $query->bindParam(5,$this->aforo);
        $query->bindParam(6,$this->local);
        $query->bindParam(7,$this->direccion);
        $query->bindParam(8,$this->ciudad);
        $query->bindParam(9,$this->pais);
        $query->bindParam(10,$this->gps);
        $query->bindParam(11,$this->imagen);
        $query->execute();        
        
        
        Tool::desconectar(Evento::$dbh);
    }
 
    /**
     * Modifica un registro de evento en la base de datos determinado por $this->id con los valores de los atributos del objeto actual ($this).
     */
    public function editarEventoEnBD(){
        Evento::$dbh=Tool::conectar();
        
        $sql="UPDATE eventos SET ".
            "Nombre=?,".
            "Descripcion=?,".
            "Fecha_inicio=?,".
            "Fecha_fin=?,".
            "Aforo=?,".
            "Local=?,".
            "Direccion=?,".
            "Ciudad=?,".
            "Pais=?,".
            "GPS=?, ".
            "Imagen=? ".
            " WHERE Id=?";
        
        $query=Evento::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->fecha_inicio);
        $query->bindParam(4,$this->fecha_fin);
        $query->bindParam(5,$this->aforo);
        $query->bindParam(6,$this->local);
        $query->bindParam(7,$this->direccion);
        $query->bindParam(8,$this->ciudad);
        $query->bindParam(9,$this->pais);
        $query->bindParam(10,$this->gps);
        $query->bindParam(11,$this->imagen);
        $query->bindParam(12,$this->id);
        
        $query->execute();        
        
        Tool::desconectar(Evento::$dbh);
    }
    
    /**
     * Obtiene todos los eventos de la base de datos.
     * 
     * @return Evento[]
     */
    public static function getAllEventos(){        
        Evento::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM eventos";
        
        $query=Evento::$dbh->prepare($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $query->execute();
             
        Tool::desconectar(Evento::$dbh);
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        $out=array();
        $i=0;
        foreach ($res as $r){
            $out[$i]=self::arrayAObjeto($r);
            $i++;
        }
        
        return $out;
    }
     
    /**
     * Obtiene el evento de la base de datos determinado por el parametro de entrada.
     * 
     * @param int $eid Identificador del evento.
     * 
     * @return Evento
     */
    public static function getEvento($eid){
        Evento::$dbh=Tool::conectar();
        
        $sql="SELECT * FROM eventos WHERE Id=?";
        
        $query=Evento::$dbh->prepare($sql);
        $query->bindParam(1,$eid);
        $query->execute();
        
        Tool::desconectar(Evento::$dbh);
        
        return self::arrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }

    
    //TODO:Aclarar formato fechas en la documentacion
    /**
     * Transforma un array asociativo con claves iguales a las columnas de la tabla Eventos de la base de datos.
     *  [{Id, Nombre, Descripcion, Fecha_inicio, Fecha_fin, Aforo, ..... }]
     *  
     * @param array $res
     * 
     * @return Evento
     */
    private static function arrayAObjeto($res){
        $ev=new Evento($res['Nombre'], $res['Descripcion'], $res['Fecha_inicio'], $res['Fecha_fin']);
        $ev->id=$res['Id'];
        $ev->aforo=$res['Aforo'];
        $ev->local=$res['Local'];
        $ev->direccion=$res['Direccion'];
        $ev->ciudad=$res['Ciudad'];
        $ev->pais=$res['Pais'];
        $ev->gps=$res['GPS'];
        $ev->estado=$res['Estado'];
        $ev->imagen=$res['Imagen'];
        
        return $ev;
    }
}
?>