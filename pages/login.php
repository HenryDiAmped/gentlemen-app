<?php
// pages/login.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../config/db_connection.php';

$authModel = new AuthModel();
$error = '';
$success = '';
$warning = '';

if (AuthModel::isLoggedIn()) {
    header('Location: index.php?page=home');
    exit();
}

if (isset($_GET['required'])) {
    $action = htmlspecialchars($_GET['required']);
    if ($action == 'products' || $action == 'checkout') {
        $warning = "Debes iniciar sesión para poder comprar productos o finalizar tu pedido.";
    } elseif ($action == 'citas') {
        $warning = "Debes iniciar sesión para poder reservar una cita.";
    }
}

// Lógica Usuario DEMO
if (isset($_GET['demo_register'])) {
    $db = getDbConnection();
    $email_demo = 'cliente@gentlemen.com';
    $password_plain = 'Cliente123';

    $stmt = $db->prepare("SELECT u.id_usuario FROM usuarios u WHERE u.email = ? AND u.tipo_usuario = 'CLIENTE'");
    $stmt->bind_param("s", $email_demo);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $pass_hash = password_hash($password_plain, PASSWORD_DEFAULT);
        $db->query("INSERT INTO usuarios (celular, dni, apellidos, nombres, email, contrasena, tipo_usuario) VALUES ('999000111','00000001','Demo','Cliente','$email_demo','$pass_hash','CLIENTE')");
        $uid = $db->insert_id;
        $db->query("INSERT INTO clientes (id_usuario) VALUES ($uid)");
        $success = "Usuario Demo creado. ¡Inicia sesión!";
    } else {
        $success = "Usuario Demo listo. Ingresa con los datos provistos.";
    }
    $db->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($authModel->login($email, $password)) {
        header('Location: index.php?page=home');
        exit();
    } else {
        $error = "Credenciales inválidas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 gentlemen-bg">

    <div class="container">
        <div class="login-card p-4 p-md-5 mx-auto rounded-4 shadow-lg bg-white" style="max-width: 450px;">
            <h1 class="text-center gentlemen-text-primary fw-bolder mb-2">Bienvenido</h1>
            <p class="text-center text-muted mb-4">Ingresa a Gentlemen Barber Shop</p>

            <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if ($warning) echo "<div class='alert alert-warning'>$warning</div>"; ?>

            <form method="POST" action="index.php?page=login" id="login-form">
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" name="email" id="email" required class="form-control form-control-lg"
                        placeholder="email@ejemplo.com"
                        value="<?php echo isset($_GET['demo_register']) ? 'cliente@gentlemen.com' : ''; ?>">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" id="password" required class="form-control form-control-lg"
                        placeholder="********"
                        value="<?php echo isset($_GET['demo_register']) ? 'Cliente123' : ''; ?>">
                </div>
                <input type="hidden" name="login" value="1">
                <button type="submit" class="btn gentlemen-primary-bg w-100 py-2 rounded-3 shadow-sm fw-bold fs-5">Iniciar Sesión</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="mb-2 small text-muted">¿Eres nuevo por aquí?</p>
                <!-- ENLACE AL REGISTRO REAL -->
                <a href="index.php?page=registro" class="btn btn-outline-dark w-100 mb-2 rounded-3">Crear Cuenta Nueva</a>

                <a href="index.php?page=login&demo_register=1" class="text-decoration-none text-secondary small">Usar Demo Rápida</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/forms.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>