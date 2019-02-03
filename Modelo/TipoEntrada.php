<?php
require_once APP_ROOT . '/Modelo/ModeloBD.php';

class TipoEntrada
{
    public $id;
    public $eventoId;
    public $nombre;
    public $descripcion;
    public $mostrar_descripcion;
    public $cantidad_disponible;
    public $precio;
    public $canales_venta;
    public $impuestos;
    public $inicio_venta;
    public $fin_venta;
    public $visibilidad;
    public $minimo_compra;
    public $maximo_compra;
    public $grupo;
    public $estado;
    
    private static $dbh;
    
    public function crearTipoEntrada(){
        $this->conectar();
        
        $sql="INSERT INTO tiposentrada "
            ."(Id,Id_Evento,Nombre,Descripcion,Mostrar_descripcion,Cantidad_disponible,Precio,"
            ."Canales_venta,Impuestos,Inicio_venta,Fin_venta,Visibilidad,Minimo_compra,Maximo_compra,Grupo_tipoEntrada,Estado) "
            ."VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->id);
        $query->bindParam(2,$this->eventoId);
        $query->bindParam(3,$this->nombre);
        $query->bindParam(4,$this->descripcion);
        $query->bindParam(5,$this->mostrar_descripcion);
        $query->bindParam(6,$this->cantidad_disponible);
        $query->bindParam(7,$this->precio);
        $query->bindParam(8,$this->canales_venta);
        $query->bindParam(9,$this->impuestos);
        $query->bindParam(10,$this->inicio_venta);
        $query->bindParam(11,$this->fin_venta);
        $query->bindParam(12,$this->visibilidad);
        $query->bindParam(13,$this->minimo_compra);
        $query->bindParam(14,$this->maximo_compra);
        $query->bindParam(15,$this->grupo);
        $query->bindParam(16,$this->estado);
        
        $query->execute();
        
        $this->desconectar();
    }
    
    public function editarTipoEntrada(){
        $this->conectar();
        
        $sql="UPDATE tiposentrada SET "
        ."Nombre=?,"
        ."Descripcion=?,"
        ."Mostrar_descripcion=?,"
        ."Cantidad_disponible=?,"
        ."Precio=?,"
        ."Canales_venta=?,"
        ."Impuestos=?,"
        ."Inicio_venta=?,"
        ."Fin_venta=?,"
        ."Visibilidad=?,"
        ."Minimo_compra=?,"
        ."Maximo_compra=?,"
        ."Grupo_tipoEntrada=?,"
        ."Estado=? "
            ."WHERE Id=? AND Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$this->nombre);
        $query->bindParam(2,$this->descripcion);
        $query->bindParam(3,$this->mostrar_descripcion);
        $query->bindParam(4,$this->cantidad_disponible);
        $query->bindParam(5,$this->precio);
        $query->bindParam(6,$this->canales_venta);
        $query->bindParam(7,$this->impuestos);
        $query->bindParam(8,$this->inicio_venta);
        $query->bindParam(9,$this->fin_venta);
        $query->bindParam(10,$this->visibilidad);
        $query->bindParam(11,$this->minimo_compra);
        $query->bindParam(12,$this->maximo_compra);
        $query->bindParam(13,$this->grupo);
        $query->bindParam(14,$this->estado);
        $query->bindParam(15,$this->id);
        $query->bindParam(16,$this->eventoId);
        
        $query->execute();
        
        $this->desconectar();
    }
    
    public static function eliminarTipoEntrada($eid,$teid){
        TipoEntrada::conectar();
        
        $sql="DELETE FROM tiposentrada WHERE Id_Evento=? AND Id=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        $query->bindParam(2,$teid);
        
        $query->execute();
        
        TipoEntrada::desconectar();        
    }
    
    public static function nuevoIdTipoEntrada($eid){
        TipoEntrada::conectar();
        
        $sql="SELECT MAX(Id) from tiposentrada WHERE Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_NUM);
        
        TipoEntrada::desconectar();
        
        return $res[0]+1;
    }
    
    public static function getAllTipoEntradas($eid){
        TipoEntrada::conectar();
        
        $sql="SELECT * FROM tiposentrada WHERE Id_Evento=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        $query->bindParam(1,$eid);
        $query->execute();
        
        TipoEntrada::desconectar();
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        
        $out=array();
        $i=0;
        foreach($res as $r){
            $out[$i]=TipoEntrada::arrayAObjeto($r);
            $i++;
        }
        return $out; 
    }
    
    /**
     * @param int eid
     * @param int tpid
     * @return TipoEntrada
     */
    public static function getTipoEntrada($eid,$tpid){
        TipoEntrada::conectar();
        
        $sql="SELECT * FROM tiposentrada WHERE Id_Evento=? AND Id=?";
        
        $query=TipoEntrada::$dbh->prepare($sql);
        
        $query->bindParam(1,$eid);
        $query->bindParam(2,$tpid);
        
        $query->execute();
        
        TipoEntrada::desconectar();
        
        return TipoEntrada::arrayAObjeto($query->fetch(PDO::FETCH_ASSOC));
    }
    
    
    private static function conectar(){
        TipoEntrada::$dbh=ModeloBD::getConexion();
    }
    
    private static function desconectar(){
        TipoEntrada::$dbh=null;
    }
    
    private static function arrayAObjeto($arrayTp){
        $tp=new TipoEntrada();
        
        $tp->id=$arrayTp['Id'];
        $tp->eventoId=$arrayTp['Id_Evento'];
        $tp->nombre=$arrayTp['Nombre'];
        $tp->descripcion=$arrayTp['Descripcion'];
        $tp->mostrar_descripcion=$arrayTp['Mostrar_descripcion'];
        $tp->cantidad_disponible=$arrayTp['Cantidad_disponible'];
        $tp->precio=$arrayTp['Precio'];
        $tp->canales_venta=$arrayTp['Canales_venta'];
        $tp->impuestos=$arrayTp['Impuestos'];
        $tp->inicio_venta=$arrayTp['Inicio_venta'];
        $tp->fin_venta=$arrayTp['Fin_venta'];
        $tp->visibilidad=$arrayTp['Visibilidad'];
        $tp->minimo_compra=$arrayTp['Minimo_compra'];
        $tp->maximo_compra=$arrayTp['Maximo_compra'];
        $tp->grupo=$arrayTp['Grupo_tipoEntrada'];
        $tp->estado=$arrayTp['Estado'];
        
        return $tp;
    }

}

