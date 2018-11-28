<?php
require_once '../constantes.php';
require_once APP_ROOT .'/Modelo/Tool.php';
require_once APP_ROOT .'/Modelo/CarroCompra.php';
require_once APP_ROOT .'/Modelo/CheckoutManager.php';
require_once APP_ROOT .'/Modelo/Venta.php';
require_once APP_ROOT .'/Modelo/Usuario.php';

session_start();


if(isset($_SESSION['usuario'])){
    
    $carro=CarroCompra::getJSON();
    
    $venta=Venta::importarJSONCarro($carro);
    
    CheckoutManager::empezarPaypalPayment($venta);

}
else{
    
    $_SESSION['urlRetorno']="/mytickets_dev/Vista/verCarroCompra.php";
    header("Location:/mytickets_dev/index.php");
}

?>