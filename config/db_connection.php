<?php
// config/db_connection.php

// Iniciar sesión global para manejar la autenticación del usuario y el carrito
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Establece y devuelve la conexión a la base de datos.
 * ATENCIÓN: DEBES REEMPLAZAR LAS SIGUIENTES CREDENCIALES con tus datos reales.
 */
function getDbConnection()
{
    $host = 'localhost'; // O la IP/Nombre de host de tu servidor de BD
    $user = 'root';      // Tu usuario de MySQL
    $pass = ''; // Tu contraseña de MySQL
    $db = 'barberia_db';  // El nombre de tu base de datos (barberia_db)

    // Crear conexión
    $conn = new mysqli($host, $user, $pass, $db);

    // Verificar conexión
    if ($conn->connect_error) {
        // En un entorno de producción, solo registraríamos el error y mostraríamos un mensaje genérico.
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Configurar charset a UTF8 para evitar problemas de codificación
    $conn->set_charset("utf8");

    return $conn;
}
