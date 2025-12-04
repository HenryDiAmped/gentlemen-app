<?php
// models/ProductModel.php
require_once __DIR__ . '/../config/db_connection.php';

class ProductModel
{

    public function getProductsWithCategories(): array
    {
        $db = getDbConnection();
        $products = [];

        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, c.nombre AS categoria 
                FROM productos p 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                ORDER BY c.nombre, p.nombre";

        $result = $db->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free();
        }

        $db->close();
        return $products;
    }

    public function getProductById(int $id_producto): ?array
    {
        $db = getDbConnection();
        $product = null;

        $stmt = $db->prepare("SELECT id_producto, nombre, descripcion, precio, stock FROM productos WHERE id_producto = ?");
        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $product = $row;
        }

        $stmt->close();
        return $product;
    }

    public function addToCart(int $id_producto, int $cantidad): bool
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $product = $this->getProductById($id_producto);
        $db = getDbConnection();

        if (!$product || (int)$product['stock'] < $cantidad) {
            $db->close();
            return false;
        }

        if (isset($_SESSION['cart'][$id_producto])) {
            $current_qty = $_SESSION['cart'][$id_producto]['cantidad'];
            $new_qty = $current_qty + $cantidad;
            $stock_disponible = (int)$product['stock'];

            if ($stock_disponible < $new_qty) {
                $db->close();
                return false;
            }
            $_SESSION['cart'][$id_producto]['cantidad'] = $new_qty;
        } else {
            $_SESSION['cart'][$id_producto] = [
                'id' => $id_producto,
                'nombre' => $product['nombre'],
                'precio' => (float)$product['precio'],
                'cantidad' => $cantidad
            ];
        }

        $db->close();
        return true;
    }

    /**
     * Finaliza la compra (Paso 1): Crea el pedido en estado PENDIENTE sin descontar stock.
     */
    public function checkout(int $id_cliente, string $direccion, float $subtotal, float $total)
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return false;
        }

        $db = getDbConnection();
        $db->begin_transaction();
        $pedido_id = false;

        try {
            $estado = 'PENDIENTE_PAGO';
            $fecha_pedido = date('Y-m-d');

            // 1. Crear el Pedido
            $stmt_pedido = $db->prepare("INSERT INTO pedidos (fecha_pedido, subtotal, total, direccion, estado) VALUES (?, ?, ?, ?, ?)");
            $stmt_pedido->bind_param("sddss", $fecha_pedido, $subtotal, $total, $direccion, $estado);
            $stmt_pedido->execute();
            $pedido_id = $db->insert_id;
            $stmt_pedido->close();

            // 2. Guardar detalles en 'carritos' (Items del pedido)
            $estado_carrito = 'APROBADO';
            $fecha_creacion = date('Y-m-d');
            $stmt_carrito = $db->prepare("INSERT INTO carritos (cantidad, fecha_creacion, id_cliente, id_pedido, id_producto, estado) VALUES (?, ?, ?, ?, ?, ?)");

            // ** CAMBIO: Eliminamos la lógica de descuento de stock de aquí **

            foreach ($_SESSION['cart'] as $item) {
                $cantidad = $item['cantidad'];
                $id_producto = $item['id'];

                $stmt_carrito->bind_param("isiiis", $cantidad, $fecha_creacion, $id_cliente, $pedido_id, $id_producto, $estado_carrito);
                $stmt_carrito->execute();
            }
            $stmt_carrito->close();

            $db->commit();
            unset($_SESSION['cart']);
        } catch (Exception $e) {
            $db->rollback();
            $pedido_id = false;
        }

        $db->close();
        return $pedido_id;
    }

    public function getOrdersByClient(int $id_cliente): array
    {
        $db = getDbConnection();
        $orders = [];

        $sql = "SELECT DISTINCT p.id_pedido, p.fecha_pedido, p.total, p.estado, p.direccion 
                FROM pedidos p
                INNER JOIN carritos c ON p.id_pedido = c.id_pedido
                WHERE c.id_cliente = ?
                ORDER BY p.fecha_pedido DESC";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        $stmt->close();
        $db->close();
        return $orders;
    }

    /**
     * Actualiza el estado de un pedido a PAGADO y DESCUENTA EL STOCK.
     * Si no hay stock suficiente, la transacción falla y no se paga.
     */
    public function payOrder(int $id_pedido): bool
    {
        $db = getDbConnection();
        $db->begin_transaction();

        try {
            // 1. Obtener los productos de este pedido
            $sql_items = "SELECT id_producto, cantidad FROM carritos WHERE id_pedido = ?";
            $stmt_items = $db->prepare($sql_items);
            $stmt_items->bind_param("i", $id_pedido);
            $stmt_items->execute();
            $result = $stmt_items->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);
            $stmt_items->close();

            // 2. Intentar descontar stock para cada producto
            $stmt_stock = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?");

            foreach ($items as $item) {
                $cantidad = $item['cantidad'];
                $id_producto = $item['id_producto'];

                $stmt_stock->bind_param("iii", $cantidad, $id_producto, $cantidad);
                $stmt_stock->execute();

                if ($db->affected_rows === 0) {
                    // Si no se actualizó ninguna fila, significa que (stock < cantidad)
                    throw new Exception("Stock insuficiente para el producto ID: " . $id_producto);
                }
            }
            $stmt_stock->close();

            // 3. Si todo el stock se descontó correctamente, marcamos como PAGADO
            $stmt_update = $db->prepare("UPDATE pedidos SET estado = 'PAGADO' WHERE id_pedido = ?");
            $stmt_update->bind_param("i", $id_pedido);
            $success = $stmt_update->execute();
            $stmt_update->close();

            if (!$success) {
                throw new Exception("Error al actualizar el estado del pedido.");
            }

            $db->commit();
            $db->close();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            $db->close();
            // Puedes loguear el error aquí si deseas: error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pedido PENDIENTE.
     * Como el stock no se descontó al crearlo, no es necesario devolver stock.
     */
    public function deleteOrder(int $id_pedido): bool
    {
        $db = getDbConnection();
        $stmt = $db->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
        if (!$stmt) return false;

        $stmt->bind_param("i", $id_pedido);
        $success = $stmt->execute();
        $stmt->close();
        $db->close();
        return $success;
    }
}
