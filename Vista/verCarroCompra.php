<?php
require_once '../constantes.php';
require_once APP_ROOT . '/Vista/Html.php';
require_once APP_ROOT . '/Modelo/CarroCompra.php';
require_once APP_ROOT . '/Modelo/TipoEntrada.php';
require_once APP_ROOT . '/Modelo/Evento.php';


echo Html::cabeceraHtml() . Html::actionBar();

$json=CarroCompra::getJSON();

$numLineas=0;
$precio=0;
$lineas=array();

if(!empty($json)){
    $json=json_decode($json);
    $numLineas=$json->numeroLineas;
    $precio=$json->totalPrecio;
    $lineas=$json->lineas;
}

echo "<h2>Carro Compra ( " . $numLineas . " items )</h2>";

echo "<table>
        <tr>
            <th>Evento</th>
            <th>Ticket</th>            
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>";

foreach($lineas as $linea){            
    echo "<tr>";    

    echo "<td>" . $linea->evento->nombre . "</td>" .
        "<td>" . $linea->tipoentrada->nombre . "</td>" .
        "<td>" . $linea->tipoentrada->precio . "</td>" .
        "<td>" . $linea->cantidad . "</td>" .
        "<td>" . $linea->tipoentrada->precio*$linea->cantidad . "</td>";
    
    echo "</tr>";
}

echo "<tr><td colspan=4>Total Pedido</td><td>" . $precio . " e</td></tr>";

echo "</table>";
?>