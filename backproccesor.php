<?php
require_once './constantes.php';
require_once APP_ROOT . '/Modelo/IPNManager.php';
require_once APP_ROOT . '/Modelo/Usuario.php';
require_once APP_ROOT . '/Modelo/Venta.php';
require_once APP_ROOT . '/Modelo/LineaVenta.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/GeneradorPDF.php';
require_once APP_ROOT . '/Modelo/Tool.php';


$ipn=new IPNManager();

$ipn->getDataFromPaypal();

Tool::log("Nuevo IPN Paypal:");

if(Venta::existePaymentID($ipn->pago_id)){
    Tool::log("IPN venta duplicada:" . $ipn->pago_id);
    exit();    
}

$aux=Usuario::existeEmail($ipn->payer_email);
$idUsuario="";

if(!$aux){
    //echo "Nuevo usuario <br />";        
    if(Usuario::registroUsuario($ipn->payer_email, $ipn->payer_name . " " . $ipn->payer_lastname, $ipn->payer_email,1)){
        Tool::log("Usuario Creado: " . $ipn->payer_email . " " . $ipn->payer_name . " " . $ipn->payer_lastname);
        $idUsuario=Usuario::existeEmail($ipn->payer_email);
    }
    else{
        //echo "Error creando usuario";
        //error creando usuario, enviar a log
        Tool::log("ERROR creando usuario: " . $ipn->payer_email . " " . $ipn->payer_name . " " . $ipn->payer_lastname);
        return false;
    }
}
else{
    //echo "Usuario existente <br />";
    Tool::log("Usuario existente: " . $ipn->payer_email . " " . $ipn->payer_name . " " . $ipn->payer_lastname);
    $idUsuario=$aux;
}


//adaptaci�n a connection festival 2019
if($ipn->custom_idevento==""){
    $ipn->custom_idevento="10";
    $ipn->custom_idtipoentrada="1";
}

$tipoEntrada=TipoEntrada::getTipoEntrada($ipn->custom_idevento, $ipn->custom_idtipoentrada);

$v=new Venta();
$v->id=Venta::getNuevoId();
$v->importe=$ipn->importe;
$v->idUsuario=$idUsuario;
$v->fecha=Tool::formatoFechaMysql($ipn->pago_fecha);
$v->estado=$ipn->pago_estado;
$v->paymentID=$ipn->pago_id;


$lv=new LineaVenta();
$lv->id=1;
$lv->idVenta=$v->id;
$lv->idEvento=$ipn->custom_idevento;
$lv->idTipoEntrada=$ipn->custom_idtipoentrada;
$lv->cantidad=$ipn->item_cantidad;
$lv->precio=$tipoEntrada->precio;
$lv->estado=$ipn->pago_estado;

$v->lineasVenta[]=$lv;

if($v->crearVenta()){
    Tool::log("Venta creada correctamente " . $v->id);
    
    $entradas=Entrada::getEntradasPorVenta($v->id);
    $pdfs=GeneradorPDF::generaPDF($entradas,"cadena");
    
    switch($ipn->custom_idevento){
        case 9:
            $from="market@transitionfestival.org";
            $fromNombre="Transition Market";
            $msg=Venta::getMensajeEmail($v->id,"market");
            
            //$to="market@transitionfestival.org";
            $to="druida@transitionfestival.org";
            break;
        case 10:
            $from="tickets@connectionfestival.es";
            $fromNombre="Connection Mailer";
            $msg=Venta::getMensajeEmail($v->id,"connection");
            
            $to="tickets@connectionfestival.es";
            //$to="druida@transitionfestival.org";
            break;
        case 11:
            $from="market@transitionfestival.org";
            $fromNombre="Transition Market";
            $msg=Venta::getMensajeEmail($v->id,"market");
            
            //$to="market@transitionfestival.org";
            $to="druida@transitionfestival.org";
            break;   
    }
    
    Tool::enviaEmail($to, $from, $fromNombre, "Tickets", $msg, "",$pdfs);
    
    //TODO: Descomentar en produccion
    Tool::enviaEmail($ipn->payer_email, $from, $fromNombre, "Tickets", $msg, "",$pdfs);
}
else{
    Tool::log("Error creando venta");
}


?>

