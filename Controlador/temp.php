<?php
require_once '../constantes.php';

require_once APP_ROOT . '/Modelo/CheckoutManager.php';

session_start();

//print_r($_GET);

try {
    CheckoutManager::ejecutarPaypalPayment($_GET['paymentId'], $_GET['token'], $_GET['PayerID']);
    header("Location:../home.php");
} catch (Exception $e) {
    echo $e->getMessage();
}


?>
