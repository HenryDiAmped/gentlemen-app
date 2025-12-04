<?php
// pages/nosotros.php
require_once __DIR__ . '/../models/AuthModel.php';
$user_name = AuthModel::isLoggedIn() ? ($_SESSION['user_name'] ?? 'Cliente') : 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Nosotros</title>
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
        <h1 class="text-center gentlemen-text-primary fw-bold mb-4">Sobre Nosotros</h1>
        <p class="text-center lead text-secondary mb-5">Experiencia, estilo y tradición.</p>

        <div class="row g-4 mb-5 text-center">
            <div class="col-md-6">
                <div class="p-4 rounded-3 shadow-sm gentlemen-bg text-white h-100 card-gentlemen">
                    <i class="fa-solid fa-bullseye fa-3x mb-3 gentlemen-text-primary"></i>
                    <h3 class="fw-bold mb-3">Misión</h3>
                    <p>Ofrecer servicios de barbería de clase mundial combinando técnicas tradicionales con tendencias modernas.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-3 shadow-sm bg-white h-100 card-gentlemen">
                    <i class="fa-solid fa-eye fa-3x mb-3 gentlemen-text-primary"></i>
                    <h3 class="fw-bold gentlemen-text-primary mb-3">Visión</h3>
                    <p class="text-dark">Ser el referente número uno en cuidado masculino en la región.</p>
                </div>
            </div>
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