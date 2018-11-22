<?php
defined("APP_ROOT")?null:define("APP_ROOT",__DIR__);

require_once APP_ROOT . '/credenciales.php';

defined("ID_VENTA_LENGTH")?null:define("ID_VENTA_LENGHT",12);
defined("TICKET_CODE_LENGTH")?null:define("TICKET_CODE_LENGTH",15);

session_start();
?>
