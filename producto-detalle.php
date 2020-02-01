
<?php

require_once "_clases.php";
require_once "_dao.php";

$id = $_REQUEST["id"];

$producto = DAO::productoObtenerPorId($id);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Nombre producto<?=$producto->getNombre()?></p>
    <p>Descripción<?=$producto->getDescripcion()?></p>

    <a href="productos-listado.php">Volver listado</a>

    <form action="carrito-gestionar-producto.php?productoId=<?=$producto->getId()?>&varacionUnidades=1" method="post">
        <input type="submit" name="Añadir carrito" value="annadir">
    </form>
</body>
</html>
