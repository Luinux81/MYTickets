<?php
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\ItemList;
use PayPal\Api\Item;
use PayPal\Api\Transaction;
use PayPal\Api\Payee;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Api\PaymentExecution;
use PayPal\Api\FlowConfig;
use PayPal\Api\WebProfile;
use PayPal\Api\InputFields;
use PayPal\Api\Presentation;

require_once APP_ROOT . '/Modelo/Tool.php';

require_once APP_ROOT . '/Modelo/Evento.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';

require_once APP_ROOT . '/Modelo/Venta.php';
require_once APP_ROOT . '/Modelo/LineaVenta.php';
require_once APP_ROOT . '/lib/PayPal-PHP-SDK/autoload.php';


class CheckoutManager{

    public static function empezarPaypalPayment($venta){
        $apiContext=new ApiContext(
                new OAuthTokenCredential(PAYPAL_CLIENTID, PAYPAL_SECRET)
            );
        
        $pago=self::crearPaypalPayment($venta,$apiContext);
        
        try{
            $pago->create($apiContext);            
            header("Location:" . $pago->getApprovalLink());
        }
        catch(PayPalConnectionException $ex){
            echo $ex->getData();
        }
        
    }
    
    public static function ejecutarPaypalPayment($paymentId,$token,$payerID){
        $apiContext=new ApiContext(
            new OAuthTokenCredential(PAYPAL_CLIENTID, PAYPAL_SECRET)
            );
        
        $pago=Payment::get($paymentId,$apiContext);
        
        $paymentExecution=new PaymentExecution();
        $paymentExecution->setPayerId($payerID);
        $paymentExecution->setTransactions($pago->getTransactions());
        
        try {
            $pago->execute($paymentExecution,$apiContext);
            
            
            //echo "<br><br>JSON PAYPAL:<br>";
            //print_r($pago->toJSON());
            
            $venta=self::exportarPagoPaypal($pago);
            
            //echo "<br><br>VENTA:<br>";
            //print_r($venta);
                        
            $venta->crearVenta();
        } 
        catch(PayPalConnectionException $ex){
            echo $ex->getData();
        }
    }
    
    private static function crearPaypalPayment($venta,$apiContext){
        $pagador=new Payer();
        $pagador->setPaymentMethod("paypal");
        
        $vendedor=new Payee();
        $vendedor->setEmail(PAYPAL_EMAIL_VENDEDOR);
        $vendedor->setMerchantId(PAYPAL_ID_VENDEDOR);
        
        $importe= new Amount();
        $importe->setTotal($venta->importe);
        $importe->setCurrency("EUR");
        
        
        $items=new ItemList();
        
        
        foreach($venta->lineasVenta as $linea){
            $evento=Evento::getEvento($linea->idEvento);
            $tp=TipoEntrada::getTipoEntrada($linea->idEvento, $linea->idTipoEntrada);
            $precio=$linea->precio;
            $cantidad=$linea->cantidad;
            
            $item=new Item();
            $item->setName($tp->nombre);
            $item->setDescription($evento->nombre);
            $item->setQuantity($cantidad);
            $item->setPrice($precio);
            $item->setCurrency("EUR");
            
            $items->addItem($item);
        }
        
        $transaccion=new Transaction();
        $transaccion->setAmount($importe);
        $transaccion->setItemList($items);
        $transaccion->setCustom($_SESSION['usuario']['id']);
        $transaccion->setInvoiceNumber($venta->id);
        
        $redirectUrls=new RedirectUrls();
        $redirectUrls->setReturnUrl(Tool::getBaseURL() . "Controlador/temp.php");
        $redirectUrls->setCancelUrl(Tool::getBaseURL() . "Vista/verCarroCompra.php?msg=cancelado");
        
        
        $pago= new Payment();
        $pago->setIntent("sale")
            ->setPayer($pagador)
            //->setPayee($vendedor)
            ->setTransactions(array($transaccion))
            ->setRedirectUrls($redirectUrls)
            ->setExperienceProfileId(self::creaNuevaExperiencia($apiContext)->getId());
        ;
            
        
        return $pago;
    }
 
    private static function creaNuevaExperiencia($apiContext){
        $flowConfig=new FlowConfig();
        $flowConfig->setLandingPageType("Billing");
        $flowConfig->setReturnUriHttpMethod("GET");
        
        $presentation=new Presentation();
        $presentation->setBrandName("MYTickets Shop");
        
        $inputFields=new InputFields();
        $inputFields->setAllowNote(false)
            ->setNoShipping(1);
        
        $webProfile=new WebProfile();
        $webProfile->setName("MYTickets Test Shop".uniqid())
            ->setFlowConfig($flowConfig)
            ->setPresentation($presentation)
            ->setInputFields($inputFields)
            ->setTemporary(true);
        
        try {
            $perfilCreado=$webProfile->create($apiContext);
        } catch (PayPalConnectionException $ex) {
            print_r($ex->getData());
        }
        
        return $perfilCreado;
    }

    private static function exportarPagoPaypal($pagoPaypal){
        $v=new Venta();
        
        $transaccion=$pagoPaypal->getTransactions()[0];
        
        $paypalSale=$transaccion->getRelatedResources()[0]->getSale();
        $items=$transaccion->getItemList()->getItems();
        
        $v->id=$transaccion->getInvoiceNumber();
        $v->idUsuario=$transaccion->getCustom();
        $v->importe=$transaccion->getAmount()->getTotal();
        $v->fecha=$paypalSale->getUpdateTime();
        $v->estado=$paypalSale->getState();
        $v->paymentID=$pagoPaypal->getId();
        
        $lineas=array();
        $i=1;
        
        foreach($items as $item){
            //$lineas[]=self::exportarItemPaypal($item->getDescription(),$item->getName(),$v->id,$i,$item->getPrice(),$v->estado);
            $lineas[]=self::exportarItemPaypal(
                                            array(
                                                "nombreEvento"=>$item->getDescription(),
                                                "nombreTipoEntrada"=>$item->getName(),
                                                "idVenta"=>$v->id,
                                                "idLinea"=>$i,
                                                "precio"=>$item->getPrice(),
                                                "estado"=>$v->estado,
                                                "cantidad"=>$item->getQuantity()
                                                )
                                            );
            $i++;
        }
        
        $v->lineasVenta=$lineas;
        
        return $v;
    }
    
    private static function exportarItemPaypal($argsArray){
        $dbh=Tool::conectar();
        
        $sql="SELECT te.Id AS IdTipoEntrada, te.Precio AS Precio,e.Id AS IdEvento FROM tiposentrada AS te "
            ."INNER JOIN eventos AS e ON te.Id_Evento=e.Id "
            ."WHERE Precio=? AND te.Nombre=? AND e.Nombre=?"
                ;
        
        $precio=$argsArray['precio'];        
        //$precio=substr( $precio, 0, strlen($precio)-(strrpos($precio, ".00")-1) );
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1,$precio);
        $query->bindParam(2,$argsArray['nombreTipoEntrada']);
        $query->bindParam(3,$argsArray['nombreEvento']);
        $query->execute();
        
        $res=$query->fetch(PDO::FETCH_ASSOC);
        
        //echo "<br><br>DEBUG:<br>";
        //print_r($res);
        
        if(!empty($res)){
            $lv=new LineaVenta();
            
            $lv->idVenta=$argsArray['idVenta'];
            $lv->id=$argsArray['idLinea'];
            $lv->precio=$precio;
            $lv->estado=$argsArray['estado'];
            $lv->cantidad=$argsArray['cantidad'];
            
            $lv->idEvento=$res['IdEvento'];
            $lv->idTipoEntrada=$res['IdTipoEntrada'];
            
            return $lv;
        }
        else{
            throw new Exception("Item Paypal no corresponde a ningún tipo de entrada registrado");
        }
        
    }
}

?>