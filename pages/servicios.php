<?php
// pages/servicios.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';

$user_name = AuthModel::isLoggedIn() ? ($_SESSION['user_name'] ?? 'Cliente') : 'Invitado';
$serviceModel = new ServiceModel();
$services = $serviceModel->getAllServicesGroupedByType();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Servicios</title>
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
        <h1 class="text-center gentlemen-text-primary mb-4 fw-bold">Nuestros Servicios</h1>
        <p class="text-center text-secondary mb-5">Encuentra el ritual perfecto para ti.</p>

        <?php if (!empty($services)): ?>
            <?php foreach ($services as $tipo => $service_list): ?>
                <div class="mb-5">
                    <h2 class="h4 gentlemen-text-secondary border-bottom border-2 pb-2 mb-4 fw-bold"><?php echo htmlspecialchars($tipo); ?></h2>
                    <div class="row g-4">
                        <?php foreach ($service_list as $service): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm border-0 service-card p-3 card-gentlemen">
                                    <div class="card-body p-0 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title fw-bold gentlemen-text-primary mb-1"><?php echo htmlspecialchars($service['nombre']); ?></h5>
                                            <span class="badge bg-light text-dark border">30 min</span>
                                        </div>
                                        <p class="card-text text-secondary small mt-2 mb-3 flex-grow-1"><?php echo htmlspecialchars($service['descripcion']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                            <span class="fs-4 fw-bolder text-dark">S/ <?php echo number_format($service['precio'], 2); ?></span>
                                            <!-- BOTÓN ACTUALIZADO: Ahora es un enlace a Citas -->
                                            <a href="index.php?page=citas" class="btn btn-sm gentlemen-primary-bg fw-semibold shadow-sm text-decoration-none">
                                                Reservar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">No hay servicios disponibles.</div>
        <?php endif; ?>

        <div class="alert alert-info p-3 text-center rounded-3 mt-5">
            <p class="mb-0 fw-semibold">¿Listo para tu cambio de look?</p>
            <a href="index.php?page=citas" class="btn btn-sm gentlemen-primary-bg mt-2 fw-bold">Agendar Cita Ahora</a>
        </div>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ui.js"></script>
</body>

</html>