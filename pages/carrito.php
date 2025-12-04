<?php
// pages/carrito.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

// Redirigir si no está logeado
if (!AuthModel::isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

$productModel = new ProductModel();
$cart = $_SESSION['cart'] ?? [];
$subtotal = 0.00;
$igv_rate = 0.18; // 18% de IGV (ejemplo)
$error_message = '';
$success_message = '';

// --- Lógica para Eliminar del Carrito ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $id_producto_remove = (int)($_POST['id_producto'] ?? 0);
    if ($id_producto_remove > 0 && isset($_SESSION['cart'][$id_producto_remove])) {
        unset($_SESSION['cart'][$id_producto_remove]);
        // Recargar el carrito de la sesión después de eliminar
        $cart = $_SESSION['cart'] ?? [];
        $success_message = "Producto eliminado del carrito.";
    }
}

// --- Lógica de Checkout ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $direccion = trim($_POST['direccion'] ?? '');

    // Calcular totales nuevamente por seguridad
    $subtotal_checkout = 0.00;

    foreach ($cart as $item) {
        $subtotal_checkout += $item['precio'] * $item['cantidad'];
    }

    $igv_checkout = $subtotal_checkout * $igv_rate;
    $total_checkout = $subtotal_checkout + $igv_checkout;

    $id_cliente = $_SESSION['client_id'];

    if (empty($direccion)) {
        $error_message = "Por favor, ingresa una dirección de envío.";
    } elseif (empty($cart)) {
        $error_message = "Tu carrito está vacío. Agrega productos para comprar.";
    } else {
        $pedido_id = $productModel->checkout($id_cliente, $direccion, $subtotal_checkout, $total_checkout);

        if ($pedido_id) {
            // El checkout fue exitoso, el carrito de la sesión se limpió dentro del modelo.
            $cart = []; // Limpiar la variable local también
            $success_message = "¡Pedido #{$pedido_id} realizado con éxito! Un mensaje ha sido enviado a tu email. Redirigiendo...";

            // Redirigir después de un breve delay para mostrar el mensaje
            header("Refresh: 3; url=index.php?page=productos");
        } else {
            $error_message = "Hubo un error al procesar tu pedido. Verifica el stock e inténtalo de nuevo.";
        }
    }
}


// --- Cálculo de Totales para la Vista ---
foreach ($cart as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}

$igv = $subtotal * $igv_rate;
$total = $subtotal + $igv;

$user_name = $_SESSION['user_name'] ?? 'Cliente';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Carrito de Compras</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Archivos CSS Obligatorios -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/responsive.css">

    <!-- Incluir jQuery y archivos JS antes de </head> (Regla 3.1) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/forms.js"></script> <!-- Necesario para el campo de dirección -->
    <script src="assets/js/ui.js"></script>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <header class="gentlemen-bg shadow-lg">
        <div class="container-fluid container-lg py-3">
            <nav class="navbar navbar-expand-lg navbar-dark gentlemen-bg">
                <a class="navbar-brand text-white" href="index.php?page=productos">
                    <span class="gentlemen-text-primary fw-bold">GENTLEMEN</span> STORE
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item d-lg-none"><span class="nav-link text-secondary">¡Hola, <?php echo htmlspecialchars($user_name); ?>!</span></li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=productos">Tienda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php?page=carrito">Carrito</a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=logout" class="btn gentlemen-primary-bg btn-sm ms-lg-3 mt-2 mt-lg-0">Salir</a>
                        </li>
                    </ul>
                </div>
                <span class="text-secondary ms-4 d-none d-lg-block">¡Hola, <?php echo htmlspecialchars($user_name); ?>!</span>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container-lg my-4 p-4 p-md-5 flex-grow-1">
        <h2 class="fw-bold text-dark mb-4 border-bottom border-warning pb-2">Tu Carrito de Compras</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success p-3 rounded-3 mb-4" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger p-3 rounded-3 mb-4" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <div class="text-center bg-white p-5 rounded-4 shadow-sm">
                <p class="h5 text-secondary">Tu carrito está vacío. ¡Es hora de agregar unos productos!</p>
                <a href="index.php?page=productos" class="mt-3 btn gentlemen-primary-bg btn-lg">
                    Ir a la Tienda
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Carrito - Columna Productos -->
                <div class="col-lg-8">
                    <div class="list-group">
                        <?php foreach ($cart as $item): ?>
                            <div class="list-group-item d-flex align-items-center bg-white rounded-4 shadow-sm p-3 mb-3 card-gentlemen">
                                <!-- Info del Producto -->
                                <div class="flex-grow-1">
                                    <h3 class="h5 fw-bold text-dark"><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                    <p class="text-secondary mb-1">Precio Unitario: S/ <?php echo number_format($item['precio'], 2); ?></p>
                                    <p class="text-secondary mb-1">Cantidad: **<?php echo (int)$item['cantidad']; ?>**</p>
                                    <p class="h4 fw-bold gentlemen-text-primary mt-1">Subtotal Ítem: S/ <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></p>
                                </div>
                                <!-- Botón de Eliminar -->
                                <form method="POST" action="index.php?page=carrito" class="ms-4">
                                    <input type="hidden" name="id_producto" value="<?php echo (int)$item['id']; ?>">
                                    <button type="submit" name="remove_from_cart"
                                        class="btn btn-outline-danger border-0 rounded-circle p-2">
                                        <!-- Icono de Bote de Basura (SVG simple) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Resumen y Checkout - Columna 3 -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 shadow-lg p-4 sticky-lg card-gentlemen">
                        <h3 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Resumen del Pedido</h3>

                        <div class="list-group-flush text-secondary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>S/ <?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>IGV (<?php echo $igv_rate * 100; ?>%):</span>
                                <span>S/ <?php echo number_format($igv, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between pt-3 border-top fw-bold text-dark h4">
                                <span>Total a Pagar:</span>
                                <span>S/ <?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>

                        <form method="POST" action="index.php?page=carrito" class="mt-4" id="checkout-form">
                            <div class="mb-3">
                                <label for="direccion" class="form-label text-dark">Dirección de Envío</label>
                                <textarea name="direccion" id="direccion" rows="3" required
                                    placeholder="Ingresa tu dirección completa aquí..."
                                    class="form-control" style="resize: none;"></textarea>
                            </div>

                            <input type="hidden" name="checkout" value="1">
                            <button type="submit"
                                class="btn w-100 btn-checkout gentlemen-primary-bg shadow-sm">
                                Finalizar Pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <?php include __DIR__ . '/../components/footer.php'; // Incluir el footer
    ?>

    <!-- Bootstrap JS y Popper CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Incluir jQuery y archivos JS antes de </body> (Regla 3.2, ya incluidos en <head> pero se recomienda incluir aquí también para compatibilidad, aunque ya están en el head) -->
    <!-- En un proyecto real, se incluirían solo en <body> para optimización, pero seguiremos la guía de incluir al menos jQuery en <body> si es posible. -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/forms.js"></script>
    <script src="assets/js/ui.js"></script>
</body>

</html>