<?php
require_once "./constantes.php";
require_once APP_ROOT . "/Modelo/Usuario.php";
require_once APP_ROOT . "/Modelo/Venta.php";
require_once APP_ROOT . "/Modelo/LineaVenta.php";
require_once APP_ROOT . "/Modelo/TipoEntrada.php";
require_once APP_ROOT . "/Modelo/Tool.php";

//$dbh=Tool::conectar();
echo "<h3>Usuarios</h3>";
addUsuarios("Compradores.sql");

echo "<br><h3>Compras</h3>";
//market transition 2019
//addCompras("Compras.sql",9,1);

//connection festival 2019
addCompras("Compras.sql",10,1);


function addUsuarios($file){
    $file=fopen($file,"r");
    
    
    while(!feof($file)){
        $linea=fgets($file);
        
        $aux=substr($linea,0,1);
        if($aux=="("){
            $linea=substr($linea,1,strlen($linea)-4);
            $linea=str_replace("'","", $linea);
            
            $res=explode(",", $linea);
            
            $email=trim($res[0]);
            $nombre=trim($res[1] . " " . $res[2]);
            
            if(!Usuario::existeEmail($email)){
                echo "Registro de usuario " . $email . ":";
                
                if(Usuario::registroUsuario($email, $nombre, $email,1)){
                    echo "Exito<br>";
                }
                else{
                    echo "Error<br>";
                }
                
            }
            else{
                echo "Usuario " . $email . " existente<br>";
            }
            
        }
    }
    
}

function addCompras($file,$idevento,$idtipo){
    $file=fopen($file,"r");
    
    while(!feof($file)){
        $linea=fgets($file);
        
        $aux=substr($linea,0,1);
        if($aux=="("){
            $linea=substr($linea,1,strlen($linea)-4);
            $linea=str_replace("'","", $linea);
            
            $res=explode(",", $linea);
            
            $codigo=trim($res[0]);
            $email=trim($res[1]);
            $fecha=trim($res[3]);
            $importe=trim($res[4]);
            $cantidad=trim($res[5]);
            
            if (Venta::getVenta($codigo)->id){
                echo "Venta código " . $codigo . " ya registrada<br>";
                continue;
            }
            
            $v=new Venta();
            $v->id=$codigo;
            $v->importe=$importe;
            $v->fecha=$fecha;
            $v->estado="Importado";
            
            if(!Usuario::existeEmail($email)){
                echo "Usuario " . $email . " no localizado<br>" . Usuario::$ultimoError;
                continue;
            }
            else{
                echo "Usuario localizado";
                $v->idUsuario=Usuario::existeEmail($email);
            }
            
            $v->paymentID=$codigo;
            
            $lv=new LineaVenta();
            $lv->id=1;
            $lv->idVenta=$codigo;
            $lv->idEvento=$idevento;
            $lv->idTipoEntrada=$idtipo;
            $lv->cantidad=$cantidad;
            $lv->estado="Importado";
            $lv->precio=TipoEntrada::getTipoEntrada($idevento, $idtipo)->precio;
            
            $v->lineasVenta=array($lv);
            
            print_r($v);
            echo "<br>";
            $v->crearVenta();
            
            updateEntradas($codigo);
        }
    }
}

function updateEntradas($idVenta){
    $dbh=Tool::conectar();
    
    $query=$dbh->prepare("SELECT * from entradas where Id_Venta=?");
    $query->bindParam(1,$idVenta);
    $query->execute();
    
    $res=$query->fetchAll(PDO::FETCH_ASSOC);
    $i=0;
    
    foreach($res as $r){
        $sql="UPDATE entradas SET Codigo=? WHERE Codigo=?";
        $query2=$dbh->prepare($sql);
        
        $aux=$idVenta . $i++;
        
        $query2->bindParam(1,$aux);
        $query2->bindParam(2,trim($r['Codigo']));
        $query2->execute();
        echo "Entrada " . trim($r['Codigo']) . " cambiada por " . $aux . "<br>";
    }
    
    Tool::desconectar($dbh);
}

?>