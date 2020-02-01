
<?php

$clienteId = $_SESSION["id"];
$productoId = $_REQUEST["id"];
$variacionUnidades = $_REQUEST["varacionUnidades"];

$carrito = DAO::carritoObtenerParaCliente($clienteId);


?>