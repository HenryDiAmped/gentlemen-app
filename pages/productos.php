<?php
// pages/productos.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

// RESTRICCIÓN: Login requerido para comprar
if (!AuthModel::isLoggedIn()) {
    header('Location: index.php?page=login&required=products');
    exit();
}

$productModel = new ProductModel();
$products = $productModel->getProductsWithCategories();
$success_message = '';
$error_message = '';

// Agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id_producto = (int)($_POST['id_producto'] ?? 0);
    // Recibimos la cantidad del formulario, por defecto 1 si falla
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_producto > 0 && $cantidad > 0) {
        if ($productModel->addToCart($id_producto, $cantidad)) {
            $success_message = "Se agregaron $cantidad unidad(es) al carrito.";
        } else {
            $error_message = "Stock insuficiente o error al agregar.";
        }
    }
}

$user_name = $_SESSION['user_name'] ?? 'Cliente';
$is_logged_in = AuthModel::isLoggedIn();

// Agrupar productos
$grouped_products = [];
foreach ($products as $product) {
    $grouped_products[$product['categoria']][] = $product;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- TUS ESTILOS -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <main class="container-lg my-5 flex-grow-1">
        <h1 class="text-center gentlemen-text-primary mb-4 fw-bold">Nuestros Productos</h1>
        <p class="text-center text-secondary mb-5">Descubre nuestra línea exclusiva.</p>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($grouped_products)): ?>
            <?php foreach ($grouped_products as $category => $category_products): ?>
                <h2 class="h4 gentlemen-text-secondary border-bottom border-2 pb-2 mb-4 mt-5 fw-bold"><?php echo htmlspecialchars($category); ?></h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php foreach ($category_products as $product): ?>
                        <div class="col">
                            <div class="card h-100 card-gentlemen border-0 p-3">
                                <!-- Placeholder visual usando CSS -->
                                <div class="card-product-img mb-3 rounded-3">
                                    <?php echo substr(htmlspecialchars($product['nombre']), 0, 3); ?>
                                </div>
                                <div class="card-body d-flex flex-column p-0">
                                    <h5 class="card-title fw-bold gentlemen-text-primary mb-1"><?php echo htmlspecialchars($product['nombre']); ?></h5>
                                    <p class="card-text text-muted small mb-3 flex-grow-1"><?php echo htmlspecialchars(substr($product['descripcion'], 0, 60)); ?>...</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-4 fw-bolder text-dark">S/ <?php echo number_format($product['precio'], 2); ?></span>
                                        <span class="badge <?php echo $product['stock'] > 5 ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            Stock: <?php echo $product['stock']; ?>
                                        </span>
                                    </div>

                                    <form method="POST" action="index.php?page=productos" class="mt-auto">
                                        <input type="hidden" name="id_producto" value="<?php echo $product['id_producto']; ?>">

                                        <!-- SECCIÓN NUEVA: Input de Cantidad -->
                                        <?php if ($product['stock'] > 0): ?>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <label for="cant-<?php echo $product['id_producto']; ?>" class="small fw-bold text-secondary me-2">Cant:</label>
                                                <input type="number"
                                                    id="cant-<?php echo $product['id_producto']; ?>"
                                                    name="cantidad"
                                                    value="1"
                                                    min="1"
                                                    max="<?php echo $product['stock']; ?>"
                                                    class="form-control form-control-sm text-center"
                                                    style="width: 80px;">
                                            </div>
                                        <?php endif; ?>
                                        <!-- FIN SECCIÓN NUEVA -->

                                        <button type="submit" name="add_to_cart" class="btn w-100 gentlemen-primary-bg fw-semibold shadow-sm"
                                            <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                            <?php echo $product['stock'] > 0 ? 'Añadir al Carrito' : 'Agotado'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">No hay productos disponibles.</div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ui.js"></script>
</body>

</html>