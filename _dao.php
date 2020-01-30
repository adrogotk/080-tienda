<?php

class DAO
{
    private static $pdo = null;

    private function obtenerPdoConexion()
    {
        $servidor = "localhost";
        $identificador = "root";
        $contrasenna = "";
        $bd = "tienda"; // Schema
        $opciones = [
            PDO::ATTR_EMULATE_PREPARES => false, // Modo emulación desactivado para prepared statements "reales"
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Que los errores salgan como excepciones.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // El modo de fetch que queremos por defecto.
        ];

        try {
            $pdo = new PDO("mysql:host=$servidor;dbname=$bd;charset=utf8", $identificador, $contrasenna, $opciones);
        } catch (Exception $e) {
            error_log("Error al conectar: " . $e->getMessage());
            exit("Error al conectar");
        }




        return $pdo;
    }

// Esta función redirige a otra página y deja de ejecutar el PHP que la llamó:
    function redireccionar($url)
    {
        header("Location: $url");
        exit();
    }


    private static function ejecutarConsulta(string $sql, array $parametros): array
    {
        if (!isset(self::$pdo)) {
            self::$pdo =self::obtenerPdoConexion();
            $select= self::$pdo->prepare($sql);
            $select->execute($parametros);
            return $select->fetchAll();

        }


    }

   /* private static function ejecutarActualizacion()
    {
        if (!isset($pdo)) {

        }
    }*/

    public static function productoObtenerTodos(): array
    {
        $datos = [];
        $rs = self::ejecutarConsulta("Select * from producto order by nombre", []);

        foreach ($rs as $fila) {
            $producto = new producto($fila["id"], $fila["nombre"], $fila["descripcion"], $fila["precio"]);
            array_push($datos, $producto);
        }
        return $datos;
    }
    public static function productoObtenerPorId(int $id){
    $rs=self::ejecutarConsulta("select * from producto where id=?",[$id]);
    $producto = new Producto($rs[0]["id"], $rs[0]["nombre"], $rs[0]["descripcion"], $rs[0]["precio"]);
    return $producto;
    }
    /*
    private static function carritoCrearParaCliente(int $id): Carrito{
        self::ejecutarConsulta("INSERT INTO pedido (cliente_id) VALUES (?) ", [$id]);
        $carrito= new Carrito ($rs[0]['id'], $rs[0]['cliente_id'], $rs[0]['direccionEnvio'], $rs[0]['fechaConfirmacion']);
    }

    public static function carritoObtenerParaCliente(int $id): Carrito{

        $rsComprobacion =self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);
        if(!$rsComprobacion){

            self::carritoCrearParaCliente($id);
            $rsSeleccionar=self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);

        }

        $carrito= new Carrito (
        $rsSeleccionar[0]['id'],
        $rsSeleccionar[0]['cliente_id'],
        $rsSeleccionar[0]['direccionEnvio'],
         $rsSeleccionar[0]['fechaConfirmacion']
        );
        return $carrito;
    }*/
}
