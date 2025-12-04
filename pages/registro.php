<?php
// pages/registro.php
require_once __DIR__ . '/../models/AuthModel.php';

// Si ya está logeado, redirigir
if (AuthModel::isLoggedIn()) {
    header('Location: index.php?page=home');
    exit();
}

$authModel = new AuthModel();
$message = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $dni = trim($_POST['dni']);
    $celular = trim($_POST['celular']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validación básica
    if (empty($nombres) || empty($apellidos) || empty($dni) || empty($email) || empty($password)) {
        $message = "Por favor completa todos los campos obligatorios.";
        $msg_type = "warning";
    } else {
        $result = $authModel->register($nombres, $apellidos, $dni, $celular, $email, $password);
        if ($result['success']) {
            $message = $result['message'];
            $msg_type = "success";
            // Redirigir al login después de 2 segundos
            header("refresh:2;url=index.php?page=login");
        } else {
            $message = $result['message'];
            $msg_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TUS ESTILOS -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 gentlemen-bg py-5">

    <div class="container">
        <div class="login-card p-4 p-md-5 mx-auto rounded-4 shadow-lg bg-white" style="max-width: 550px;">
            <h2 class="text-center gentlemen-text-primary fw-bolder mb-2">Crear Cuenta</h2>
            <p class="text-center text-muted mb-4">Únete a Gentlemen Barber Shop</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=registro">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" required placeholder="Juan">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Apellidos *</label>
                        <input type="text" name="apellidos" class="form-control" required placeholder="Pérez">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">DNI *</label>
                        <input type="text" name="dni" class="form-control" required placeholder="8 dígitos" maxlength="8" pattern="[0-9]{8}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Celular *</label>
                        <input type="tel" name="celular" class="form-control" required placeholder="9 dígitos" maxlength="9" pattern="[0-9]{9}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Correo Electrónico *</label>
                    <input type="email" name="email" class="form-control" required placeholder="nombre@correo.com">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Contraseña *</label>
                    <input type="password" name="password" class="form-control" required placeholder="********" minlength="6">
                    <div class="form-text">Mínimo 6 caracteres.</div>
                </div>

                <button type="submit" class="btn gentlemen-primary-bg w-100 py-2 rounded-3 shadow-sm fw-bold fs-5">
                    Registrarse
                </button>
            </form>

            <div class="text-center mt-4 border-top pt-3">
                <p class="text-muted small mb-0">¿Ya tienes una cuenta?</p>
                <a href="index.php?page=login" class="text-decoration-none fw-bold gentlemen-text-primary">Iniciar Sesión</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>