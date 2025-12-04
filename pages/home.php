<?php
// pages/home.php
require_once __DIR__ . '/../models/AuthModel.php';

$user_name = AuthModel::isLoggedIn() ? ($_SESSION['user_name'] ?? 'Cliente') : 'Invitado';

// Datos simulados para testimonios (puedes mover esto a un modelo después)
$testimonials = [
    ['quote' => 'El mejor servicio de corte y barba que he recibido en Lima.', 'author' => 'Ricardo L.', 'rating' => 5],
    ['quote' => 'Excelente atención y profesionalismo. Mi nuevo lugar favorito.', 'author' => 'Javier M.', 'rating' => 5],
    ['quote' => 'Los productos son de calidad superior. ¡Recomendado!', 'author' => 'Andrés T.', 'rating' => 4],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Inicio</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- TUS ESTILOS CSS -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <main class="container-lg my-5 flex-grow-1">
        <header class="text-center mb-5">
            <h1 class="gentlemen-text-primary fw-bolder display-4">Bienvenido, <?php echo htmlspecialchars($user_name); ?>.</h1>
            <p class="lead text-secondary mt-3">Tu estilo, nuestra pasión. Descubre la experiencia Gentleman.</p>
        </header>

        <!-- Sección de Servicios Destacados -->
        <section class="mb-5 p-4 rounded-3 shadow-sm gentlemen-bg text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="h3 fw-bold mb-3">Tu Corte Perfecto te Espera.</h2>
                    <p class="lead">Explora nuestros servicios exclusivos de corte, barba y rituales. Agenda tu próxima cita en línea.</p>
                </div>
                <div class="col-md-4 text-center">
                    <a href="index.php?page=servicios" class="btn btn-lg gentlemen-secondary-bg btn-light fw-bold rounded-pill shadow-lg mt-3 mt-md-0">
                        <span class="gentlemen-text-primary">Ver Servicios</span> <i class="fa-solid fa-scissors ms-2 gentlemen-text-primary"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Sección de Productos Destacados (Estática por ahora) -->
        <section class="mb-5">
            <h2 class="text-center gentlemen-text-primary fw-bold mb-4">Lo Más Vendido</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 text-center p-3 card-gentlemen">
                        <div class="card-product-img mb-3 rounded-3">CREMA</div>
                        <div class="card-body p-0">
                            <h5 class="card-title fw-bold">MOISTURIZING SHAVE CREAM</h5>
                            <p class="card-text text-muted small">Hidrata y calma la piel.</p>
                            <span class="fs-5 fw-bolder gentlemen-text-primary">S/ 80.00</span>
                            <a href="index.php?page=productos" class="btn gentlemen-primary-bg w-100 mt-3 fw-semibold">Ver en Tienda</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 text-center p-3 card-gentlemen">
                        <div class="card-product-img mb-3 rounded-3">SHAMPOO</div>
                        <div class="card-body p-0">
                            <h5 class="card-title fw-bold">SHAMPOO 3-EN-1 TEA TREE</h5>
                            <p class="card-text text-muted small">Todo en uno para el hombre práctico.</p>
                            <span class="fs-5 fw-bolder gentlemen-text-primary">S/ 75.00</span>
                            <a href="index.php?page=productos" class="btn gentlemen-primary-bg w-100 mt-3 fw-semibold">Ver en Tienda</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 text-center p-3 card-gentlemen">
                        <div class="card-product-img mb-3 rounded-3">SERUM</div>
                        <div class="card-body p-0">
                            <h5 class="card-title fw-bold">BEARD SERUM 50 ML</h5>
                            <p class="card-text text-muted small">Cuidado intensivo para barba.</p>
                            <span class="fs-5 fw-bolder gentlemen-text-primary">S/ 85.00</span>
                            <a href="index.php?page=productos" class="btn gentlemen-primary-bg w-100 mt-3 fw-semibold">Ver en Tienda</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonios -->
        <h2 class="text-center gentlemen-text-primary fw-bold mb-4">Opiniones de Clientes</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 p-3">
                        <div class="card-body">
                            <p class="card-text fst-italic text-dark mb-2">"<?php echo htmlspecialchars($testimonial['quote']); ?>"</p>
                            <div class="gentlemen-text-primary">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): echo '<i class="fa-solid fa-star small"></i>';
                                endfor; ?>
                            </div>
                            <footer class="blockquote-footer mt-2"><?php echo htmlspecialchars($testimonial['author']); ?></footer>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- SCRIPTS NECESARIOS -->
    <!-- 1. jQuery (Requerido por tus archivos JS) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- 2. Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- 3. TUS SCRIPTS (Ahora sí se cargarán) -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ui.js"></script>
</body>

</html>