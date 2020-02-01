<?php

require_once "_comprobar-sesion.php";
require_once "_clases.php";
require_once "_dao.php";
$carrito=DAO::obtenerCarritoPorCliente($_SESSION["id"]); //O clienteId o id o idCliente

?>

<html>

<head>
    <meta charset="UTF-8">
    <title>carrito ver</title>
</head>

<body>

<?php require "_info-sesion.php"; ?>

<h1>Tu carrito</h1>


<table border="1">
    <?php

    if ($carrito){
        foreach ($productos as $fila) {
            $producto=DAO::obtenerProductoPorId($fila["producto_id"]);//obtenerProductoPorId
            ?>

            <tr>
                <td><a href='producto-detalle.php?id=<?=$producto["id"]?>'><?=$producto["nombre"]?></a></td>
                <td><?=$fila["unidades"] ?></td>
                <td><?=$producto["precio"] ?></td>
            </tr>
        <?php }} ?>

</table>

<?php require "_info-sesion.php"; ?>

</body>
</html>
