<?php
require_once '../constantes.php';
require_once APP_ROOT .'/Modelo/CarroCompra.php';
require_once APP_ROOT .'/Modelo/CheckoutManager.php';
require_once APP_ROOT .'/Modelo/Venta.php';
require_once APP_ROOT .'/Modelo/LineaVenta.php';

session_start();

$carro=CarroCompra::getJSON();

$venta=Venta::importarJSONCarro($carro);

CheckoutManager::empezarPaypalPayment($venta);

?>