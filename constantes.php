<?php
session_start();

defined("APP_ROOT")?null:define("APP_ROOT",__DIR__);
defined("APP_URL")?null:define("APP_URL","http://localhost/mytickets_dev");
defined("LOG_FILE")?null:define("LOG_FILE",APP_ROOT . "/ipn.log");

require_once APP_ROOT . '/credenciales.php';

defined("ID_VENTA_LENGTH")?null:define("ID_VENTA_LENGHT",12);
defined("TICKET_CODE_LENGTH")?null:define("TICKET_CODE_LENGTH",15);


?>
