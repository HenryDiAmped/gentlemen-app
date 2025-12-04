<?php
// index.php - Controlador principal
require_once __DIR__ . '/config/db_connection.php';

// Rutas permitidas
$allowed_pages = [
    'login',
    'registro', // <--- AGREGADO
    'productos',
    'carrito',
    'logout',
    'home',
    'nosotros',
    'servicios',
    'barberos',
    'citas',
    'perfil'
];

$page = $_GET['page'] ?? 'home';

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

$file_path = __DIR__ . '/pages/' . $page . '.php';

if (file_exists($file_path)) {
    include $file_path;
} else {
    include __DIR__ . '/pages/login.php';
}
