<?php
// models/AuthModel.php
require_once __DIR__ . '/../config/db_connection.php';

class AuthModel
{

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['client_id']) && $_SESSION['client_id'] > 0;
    }

    public function login(string $email, string $password)
    {
        $db = getDbConnection();

        $stmt = $db->prepare("SELECT u.id_usuario, u.contrasena, u.nombres, c.id_cliente 
                              FROM usuarios u 
                              JOIN clientes c ON u.id_usuario = c.id_usuario 
                              WHERE u.email = ? AND u.tipo_usuario = 'CLIENTE'");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        if ($user && password_verify($password, $user['contrasena'])) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['client_id'] = $user['id_cliente'];
            $_SESSION['user_name'] = $user['nombres'];
            $_SESSION['user_role'] = 'CLIENTE';
            return $user['id_cliente'];
        }

        return false;
    }

    /**
     * Registra un nuevo cliente en la base de datos.
     */
    public function register($nombres, $apellidos, $dni, $celular, $email, $password)
    {
        $db = getDbConnection();

        // 1. Verificar si el email ya existe
        $stmtCheck = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            $stmtCheck->close();
            $db->close();
            return ['success' => false, 'message' => 'El correo electrónico ya está registrado.'];
        }
        $stmtCheck->close();

        // 2. Iniciar transacción
        $db->begin_transaction();

        try {
            // Insertar en usuarios
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $tipo = 'CLIENTE';

            $stmtUser = $db->prepare("INSERT INTO usuarios (nombres, apellidos, dni, celular, email, contrasena, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtUser->bind_param("sssssss", $nombres, $apellidos, $dni, $celular, $email, $hashed_password, $tipo);
            $stmtUser->execute();
            $id_usuario = $db->insert_id;
            $stmtUser->close();

            // Insertar en clientes
            $stmtClient = $db->prepare("INSERT INTO clientes (id_usuario) VALUES (?)");
            $stmtClient->bind_param("i", $id_usuario);
            $stmtClient->execute();
            $stmtClient->close();

            $db->commit();
            $db->close();
            return ['success' => true, 'message' => 'Registro exitoso. Ahora puedes iniciar sesión.'];
        } catch (Exception $e) {
            $db->rollback();
            $db->close();
            // Verificar si es error de duplicado (DNI o Celular)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['success' => false, 'message' => 'El DNI o Celular ya están registrados en el sistema.'];
            }
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }
}
