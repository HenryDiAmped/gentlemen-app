<?php
// components/navbar.php

$current_page = $_GET['page'] ?? 'home';
$is_logged_in = isset($_SESSION['client_id']);
$navbar_user_name = $_SESSION['user_name'] ?? 'Invitado';
?>
<!-- Header / Navbar -->
<header class="gentlemen-bg shadow-lg">
    <div class="container-fluid container-lg py-3">
        <nav class="navbar navbar-expand-lg navbar-dark gentlemen-bg">
            <a class="navbar-brand text-white" href="index.php?page=home">
                <span class="gentlemen-text-primary fw-bold">GENTLEMEN</span> STORE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">

                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'home' ? 'active' : ''; ?>" href="index.php?page=home">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'nosotros' ? 'active' : ''; ?>" href="index.php?page=nosotros">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'servicios' ? 'active' : ''; ?>" href="index.php?page=servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'barberos' ? 'active' : ''; ?>" href="index.php?page=barberos">Barberos</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'citas' ? 'active' : ''; ?>" href="index.php?page=citas">Reservar</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'productos' ? 'active' : ''; ?>" href="index.php?page=productos">Tienda</a></li>

                    <?php if ($is_logged_in): ?>
                        <li class="nav-item d-lg-none mt-2 border-top border-secondary pt-2">
                            <a class="nav-link <?php echo $current_page == 'perfil' ? 'active' : ''; ?>" href="index.php?page=perfil">
                                <i class="fa-solid fa-user me-2"></i>Mi Perfil
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="index.php?page=carrito" class="nav-link position-relative me-3 <?php echo $current_page == 'carrito' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <?php if (!empty($_SESSION['cart'])): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo count($_SESSION['cart']); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- Auth -->
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a href="index.php?page=logout" class="btn gentlemen-primary-bg btn-sm ms-lg-3 mt-2 mt-lg-0 px-3 fw-bold shadow-sm">Salir</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a href="index.php?page=login" class="btn btn-outline-light btn-sm ms-lg-3 px-3">Entrar</a></li>
                        <!-- BOTÓN ACTUALIZADO -->
                        <li class="nav-item"><a href="index.php?page=registro" class="btn gentlemen-primary-bg btn-sm ms-2 px-3 fw-bold">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if ($is_logged_in): ?>
                <a href="index.php?page=perfil" class="d-none d-lg-flex align-items-center ms-4 border-start border-secondary ps-3 text-decoration-none">
                    <div class="text-end lh-1">
                        <small class="text-secondary d-block" style="font-size: 0.7rem;">Mi Cuenta</small>
                        <span class="text-white fw-semibold hover-highlight" style="font-size: 0.9rem;">
                            <?php echo htmlspecialchars($navbar_user_name); ?>
                        </span>
                    </div>
                </a>
            <?php endif; ?>

        </nav>
    </div>
</header>