<?php
// pages/barberos.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/BarberModel.php';

$user_name = AuthModel::isLoggedIn() ? ($_SESSION['user_name'] ?? 'Cliente') : 'Invitado';
$barberModel = new BarberModel();
$barbers = $barberModel->getAllBarbers();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Barberos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- TUS ESTILOS -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <main class="container-lg my-5 flex-grow-1">
        <h1 class="text-center gentlemen-text-primary fw-bold mb-4">Nuestro Equipo</h1>
        <p class="text-center text-secondary mb-5">Profesionales dedicados a tu estilo.</p>

        <?php if (!empty($barbers)): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                <?php foreach ($barbers as $barber): ?>
                    <div class="col">
                        <div class="card h-100 shadow-lg border-0 text-center p-3 card-gentlemen">
                            <img src="<?php echo htmlspecialchars($barber['image_url']); ?>" class="card-img-top rounded-circle mx-auto mb-3 shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #f39c12;">
                            <div class="card-body p-0">
                                <h5 class="card-title fw-bolder gentlemen-text-primary mb-1"><?php echo htmlspecialchars($barber['nombre']); ?></h5>
                                <p class="text-dark fw-bold small mb-2"><?php echo htmlspecialchars($barber['especialidad']); ?></p>
                                <p class="card-text text-secondary small"><?php echo htmlspecialchars($barber['experiencia']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No hay barberos disponibles.</div>
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