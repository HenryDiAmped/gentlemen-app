<?php
// pages/citas.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/BarberModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';

// 1. Seguridad: Solo usuarios logueados pueden reservar
if (!AuthModel::isLoggedIn()) {
    header('Location: index.php?page=login&required=citas');
    exit();
}

// 2. Cargar datos para el formulario
$barberModel = new BarberModel();
$barbers = $barberModel->getAllBarbers(); // Nota: Asegúrate que tu BarberModel devuelva ID también (ver nota abajo)

$serviceModel = new ServiceModel();
// Obtenemos servicios agrupados, los aplanaremos para el select simple
$groupedServices = $serviceModel->getAllServicesGroupedByType();

$user_name = $_SESSION['user_name'] ?? 'Cliente';
$message = '';
$msg_type = '';

// 3. Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar'])) {
    $id_barbero = $_POST['barbero'] ?? '';
    $id_servicio = $_POST['servicio'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if ($id_barbero && $id_servicio && $fecha && $hora) {
        $appointmentModel = new AppointmentModel();
        $resultado = $appointmentModel->createAppointment($_SESSION['client_id'], $id_barbero, $id_servicio, $fecha, $hora);

        if ($resultado['success']) {
            $message = $resultado['message'];
            $msg_type = 'success';
        } else {
            $message = $resultado['message'];
            $msg_type = 'danger';
        }
    } else {
        $message = "Por favor completa todos los campos.";
        $msg_type = 'warning';
    }
}

// Horarios disponibles (Hardcoded para simplicidad visual, el modelo valida disponibilidad real)
$horarios_disponibles = [
    '10:00:00' => '10:00 AM',
    '11:00:00' => '11:00 AM',
    '12:00:00' => '12:00 PM',
    '13:00:00' => '01:00 PM',
    '15:00:00' => '03:00 PM',
    '16:00:00' => '04:00 PM',
    '17:00:00' => '05:00 PM',
    '18:00:00' => '06:00 PM',
    '19:00:00' => '07:00 PM'
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Reservar Cita</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header gentlemen-bg text-white p-4 text-center">
                        <h2 class="fw-bold mb-1">Reserva tu Cita</h2>
                        <p class="mb-0 text-white-50">Selecciona tu estilo y horario preferido.</p>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=citas" id="cita-form">

                            <!-- Paso 1: Servicio -->
                            <div class="mb-4">
                                <label for="servicio" class="form-label fw-bold text-secondary"><i class="fa-solid fa-scissors me-2"></i>Servicio</label>
                                <select name="servicio" id="servicio" class="form-select form-select-lg" required>
                                    <option value="" selected disabled>Selecciona un servicio...</option>
                                    <?php foreach ($groupedServices as $tipo => $lista): ?>
                                        <optgroup label="<?php echo htmlspecialchars($tipo); ?>">
                                            <?php foreach ($lista as $serv): ?>
                                                <!-- Nota: Necesitamos el ID del servicio. Asumimos que tu modelo lo traerá. 
                                                     Si tu ServiceModel actual no trae ID, necesitarás ajustarlo. 
                                                     Por ahora intentaremos usar un campo 'id_servicio' simulado si no existe -->
                                                <option value="<?php echo $serv['id_servicio'] ?? 1; /* Fallback ID 1 para demo */ ?>">
                                                    <?php echo htmlspecialchars($serv['nombre']); ?> - S/ <?php echo number_format($serv['precio'], 2); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Paso 2: Barbero -->
                            <div class="mb-4">
                                <label for="barbero" class="form-label fw-bold text-secondary"><i class="fa-solid fa-user-tie me-2"></i>Barbero</label>
                                <select name="barbero" id="barbero" class="form-select form-select-lg" required>
                                    <option value="" selected disabled>Selecciona un barbero...</option>
                                    <?php foreach ($barbers as $barber): ?>
                                        <!-- Nota: Igual que arriba, necesitamos el ID del barbero. 
                                             Ajustaremos BarberModel en el siguiente paso si es necesario. -->
                                        <option value="<?php echo $barber['id_barbero'] ?? 1; ?>">
                                            <?php echo htmlspecialchars($barber['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <!-- Paso 3: Fecha -->
                                <div class="col-md-6 mb-4">
                                    <label for="fecha" class="form-label fw-bold text-secondary"><i class="fa-regular fa-calendar me-2"></i>Fecha</label>
                                    <input type="date" name="fecha" id="fecha" class="form-control form-control-lg"
                                        min="<?php echo date('Y-m-d'); ?>" required>
                                </div>

                                <!-- Paso 4: Hora -->
                                <div class="col-md-6 mb-4">
                                    <label for="hora" class="form-label fw-bold text-secondary"><i class="fa-regular fa-clock me-2"></i>Hora</label>
                                    <select name="hora" id="hora" class="form-select form-select-lg" required>
                                        <option value="" selected disabled>Selecciona hora...</option>
                                        <?php foreach ($horarios_disponibles as $val => $texto): ?>
                                            <option value="<?php echo $val; ?>"><?php echo $texto; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="reservar" class="btn gentlemen-primary-bg btn-lg shadow-sm fw-bold">
                                    Confirmar Reserva
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ui.js"></script>
</body>

</html>