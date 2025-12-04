<?php
// pages/perfil.php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';

// Verificar sesión
if (!AuthModel::isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

$id_cliente = $_SESSION['client_id'];
$user_name = $_SESSION['user_name'];
$productModel = new ProductModel();
$appointmentModel = new AppointmentModel();
$msg = '';

// --- Lógica para Acciones de Pedidos (Pagar / Cancelar) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Acciones de Pedidos
    if (isset($_POST['accion_pedido'])) {
        $id_pedido_accion = (int)$_POST['id_pedido'];
        $accion = $_POST['accion_pedido'];

        if ($accion === 'pagar') {
            if ($productModel->payOrder($id_pedido_accion)) {
                $msg = '<div class="alert alert-success">¡Pedido pagado correctamente! Stock descontado.</div>';
            } else {
                $msg = '<div class="alert alert-danger">Error al procesar el pago. Puede que no haya stock suficiente.</div>';
            }
        } elseif ($accion === 'cancelar') {
            if ($productModel->deleteOrder($id_pedido_accion)) {
                $msg = '<div class="alert alert-warning">El pedido ha sido eliminado.</div>';
            } else {
                $msg = '<div class="alert alert-danger">Error al cancelar el pedido.</div>';
            }
        }
    }

    // NUEVA: Acción de Comentarios en Citas
    if (isset($_POST['accion_cita']) && $_POST['accion_cita'] === 'comentar') {
        $id_cita_comentar = (int)$_POST['id_cita'];
        $contenido = trim($_POST['comentario']);

        if (!empty($contenido)) {
            if ($appointmentModel->addComment($id_cita_comentar, $contenido)) {
                $msg = '<div class="alert alert-success">¡Gracias por tu comentario!</div>';
            } else {
                $msg = '<div class="alert alert-danger">Error al guardar el comentario.</div>';
            }
        } else {
            $msg = '<div class="alert alert-warning">El comentario no puede estar vacío.</div>';
        }
    }
}

// Obtener datos actualizados
$mis_pedidos = $productModel->getOrdersByClient($id_cliente);
$mis_citas = $appointmentModel->getAppointmentsByClient($id_cliente);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gentlemen App - Mi Perfil</title>
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
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 rounded-4 shadow-sm border-start border-5 border-warning">
                    <h1 class="h3 fw-bold gentlemen-text-primary mb-1">Hola, <?php echo htmlspecialchars($user_name); ?></h1>
                    <p class="text-secondary mb-0">Bienvenido a tu panel personal.</p>
                </div>
                <?php if ($msg) echo "<div class='mt-3'>$msg</div>"; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sección Citas -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg h-100 rounded-4 overflow-hidden">
                    <div class="card-header gentlemen-bg text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa-regular fa-calendar-check me-2"></i>Mis Citas</h5>
                        <a href="index.php?page=citas" class="btn btn-sm btn-light fw-bold text-dark">Nueva Cita</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($mis_citas)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-3x mb-3 opacity-25"></i>
                                <p>No tienes citas registradas aún.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($mis_citas as $cita): ?>
                                    <div class="list-group-item p-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($cita['servicio']); ?></h6>
                                                <p class="small text-secondary mb-1">
                                                    <i class="fa-solid fa-user-tie me-1"></i> Barbero: <?php echo htmlspecialchars($cita['barbero']); ?>
                                                </p>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fa-regular fa-calendar me-1"></i> <?php echo date('d/m/Y', strtotime($cita['fecha'])); ?>
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fa-regular fa-clock me-1"></i> <?php echo date('h:i A', strtotime($cita['hora'])); ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="text-end ms-2">
                                                <span class="badge mb-2 d-block <?php echo $cita['estado'] == 'ATENDIDO' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                    <?php echo $cita['estado']; ?>
                                                </span>

                                                <!-- BOTÓN DE COMENTARIO -->
                                                <?php if ($cita['estado'] == 'ATENDIDO'): ?>
                                                    <?php if (empty($cita['id_comentario'])): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#commentModal"
                                                            data-id="<?php echo $cita['id_cita']; ?>"
                                                            title="Dejar una opinión">
                                                            <i class="fa-regular fa-comment-dots"></i> Opinar
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge bg-info text-dark" title="Ya has comentado esta cita">
                                                            <i class="fa-solid fa-check"></i> Opinado
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sección Pedidos -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg h-100 rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold gentlemen-text-primary"><i class="fa-solid fa-bag-shopping me-2"></i>Mis Pedidos</h5>
                        <a href="index.php?page=productos" class="btn btn-sm gentlemen-primary-bg text-white fw-bold">Ir a Tienda</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($mis_pedidos)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fa-solid fa-basket-shopping fa-3x mb-3 opacity-25"></i>
                                <p>No has realizado compras todavía.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light text-secondary small">
                                        <tr>
                                            <th class="ps-4">#Pedido</th>
                                            <th>Fecha / Total</th>
                                            <th>Dirección</th>
                                            <th>Estado</th>
                                            <th class="text-end pe-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mis_pedidos as $pedido): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-muted">#<?php echo str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT); ?></td>
                                                <td class="small" style="min-width: 100px;">
                                                    <div class="text-secondary"><?php echo date('d/m/y', strtotime($pedido['fecha_pedido'])); ?></div>
                                                    <div class="fw-bold text-dark">S/ <?php echo number_format($pedido['total'], 2); ?></div>
                                                </td>
                                                <td class="small">
                                                    <div class="text-truncate" style="max-width: 140px;"
                                                        title="<?php echo htmlspecialchars($pedido['direccion']); ?>">
                                                        <i class="fa-solid fa-location-dot text-secondary me-1"></i>
                                                        <?php echo htmlspecialchars($pedido['direccion']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill <?php echo $pedido['estado'] == 'PAGADO' ? 'bg-success' : 'bg-secondary'; ?>" style="font-size: 0.75rem;">
                                                        <?php echo $pedido['estado']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <?php if ($pedido['estado'] == 'PENDIENTE_PAGO'): ?>
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <form method="POST" action="index.php?page=perfil">
                                                                <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                                <input type="hidden" name="accion_pedido" value="pagar">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Pagar">
                                                                    <i class="fa-solid fa-money-bill-wave"></i>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="index.php?page=perfil" onsubmit="return confirm('¿Seguro que deseas cancelar este pedido?');">
                                                                <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                                <input type="hidden" name="accion_pedido" value="cancelar">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-check-circle text-success"></i>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Comentarios -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gentlemen-bg text-white">
                    <h5 class="modal-title fw-bold">Tu Opinión es Importante</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=perfil">
                    <div class="modal-body">
                        <input type="hidden" name="accion_cita" value="comentar">
                        <input type="hidden" name="id_cita" id="modal_id_cita">

                        <div class="mb-3">
                            <label for="comentario" class="form-label fw-bold">¿Qué te pareció el servicio?</label>
                            <textarea class="form-control" name="comentario" id="comentario" rows="4" required placeholder="Escribe tu experiencia aquí..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn gentlemen-primary-bg fw-bold">Enviar Comentario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ui.js"></script>

    <!-- Script para pasar el ID de la cita al Modal -->
    <script>
        const commentModal = document.getElementById('commentModal');
        if (commentModal) {
            commentModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const idCita = button.getAttribute('data-id');
                const modalInput = commentModal.querySelector('#modal_id_cita');
                modalInput.value = idCita;
            });
        }
    </script>
</body>

</html>