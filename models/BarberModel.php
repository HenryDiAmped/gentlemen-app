<?php
// models/BarberModel.php
require_once __DIR__ . '/../config/db_connection.php';

class BarberModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDBConnection();
        if (!$this->db) {
            error_log("Error: No se pudo conectar a la base de datos en BarberModel.");
            return;
        }
    }

    public function getAllBarbers()
    {
        $barbers = [];
        if (!$this->db) return $barbers;

        // AGREGADO: b.id_barbero en el SELECT
        $sql = "SELECT b.id_barbero, u.nombres, u.apellidos
                FROM usuarios u
                INNER JOIN barberos b ON u.id_usuario = b.id_usuario
                WHERE u.tipo_usuario = 'BARBERO'
                ORDER BY u.apellidos ASC";

        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);

            // Datos simulados para visualización
            $specialties = ['Fade', 'Clásico', 'Barba', 'Tinte'];
            $experiences = ['Experto', 'Master', 'Senior', 'Pro'];

            foreach ($data as $index => $row) {
                $barbers[] = [
                    'id_barbero' => $row['id_barbero'], // NECESARIO PARA EL FORMULARIO
                    'nombre' => trim($row['nombres'] . ' ' . $row['apellidos']),
                    'especialidad' => $specialties[$index % count($specialties)],
                    'experiencia' => $experiences[$index % count($experiences)],
                    'image_url' => 'https://placehold.co/150?text=' . substr($row['nombres'], 0, 1),
                ];
            }
        }
        return $barbers;
    }
}
