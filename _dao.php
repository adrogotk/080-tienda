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


    private static function carritoCrearParaCliente(int $id): Carrito{
        self::ejecutarConsulta("INSERT INTO pedido (cliente_id, direccionEnvio, fechaConfirmacion) VALUES (?, NULL, NULL) ", [$id]);

    

    public static function carritoObtenerParaCliente(int $id): Carrito{


        $rs =self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);
        if(!$rs){

            self::carritoCrearParaCliente($id);
            $rs=self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);

        $rsComprobacion =self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);
        if(!$rsComprobacion){

            self::carritoCrearParaCliente($id);
            $rsSeleccionar=self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion=null ",[$id]);

        }

        $carrito= new Carrito (

        $rs[0]['id'],
        $rs[0]['cliente_id'],
        $rs[0]['direccionEnvio'],
         $rs[0]['fechaConfirmacion']
        );
        return $carrito;
    }
    private static function carritoObtenerUnidadesProducto($clienteId, $productoId, $pedidoId){
        self::ejecutarConsulta("SELECT unidades, pedido_id FROM linea, pedido WHERE pedido.id=linea.pedido_id AND cliente_id=? AND producto_id=? ", [$clienteId, $productoId]);
    }
    private static function carritoEstablecerUnidadesProducto($clienteId, $productoId, $nuevaCantidadUnidades, $pedidoId){
        $rs = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        // $rsPrecio= self::ejecutarConsulta("SELECT precio FROM producto WHERE id=? ", [$productoId]);
        // $precioProducto=$rsPrecio[0]['precio'];
        if (!$rs && $nuevaCantidadUnidades>1){
            self::ejecutarConsulta("INSERT INTO linea (pedidoId, producto_id, unidades, precioUnitario) VALUES (?, ?, ?, NULL )", [$pedidoId, $productoId, $nuevaCantidadUnidades]);
            // PrecioUnitario en vez de null-> $precioProducto*$nuevaCantidadUnidades
        }
        else if($rs && $nuevaCantidadUnidades>1){
            self::ejecutarConsulta("UPDATE linea SET unidades=? WHERE pedido_id=? AND producto_id=?", [$nuevaCantidadUnidades, $pedidoId, $productoId]);
            // Habria que añadir al set el PrecioUnitario ($precioProducto * $nuevaCantidadUnidades)
        }
        else if ($rs && $nuevaCantidadUnidades<=0) {
            self::ejecutarConsulta("DELETE FROM linea WHERE pedido_id=? AND producto_id=?", [$pedidoId, $productoId]);
        }
        /* else { // Quieren quitar unidades de un prodcuto que no existe, informar al usuario de ello.


         */
    }
    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades) {
        $rs = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $pedidoId=$rs[0]['pedido_id'];
        if (!$rs){
            $nuevaCantidadUnidades = $variacionUnidades;
        }
        else {
            $nuevaCantidadUnidades = $variacionUnidades + $rs[0]['unidades'];
        }
        self::carritoEstablecerUnidadesProducto($clienteId, $productoId, $nuevaCantidadUnidades, $pedidoId);
    }
}
