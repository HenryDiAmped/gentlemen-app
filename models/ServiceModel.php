<?php
// models/ServiceModel.php
require_once __DIR__ . '/../config/db_connection.php';

class ServiceModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDBConnection();
        if ($this->db->connect_error) die("Connection failed.");
    }

    public function getAllServicesGroupedByType()
    {
        $services = [];

        // AGREGADO: s.id_servicio en el SELECT
        $sql = "SELECT s.id_servicio, s.nombre AS servicio_nombre, s.tarifa AS precio, 
                       s.detalle AS descripcion, t.nombre AS tipo_servicio_nombre
                FROM Servicios s
                JOIN Tipo_Servicios t ON s.id_tipo_servicio = t.id_tipo_servicio
                ORDER BY t.nombre, s.nombre";

        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tipo = $row['tipo_servicio_nombre'];
                if (!isset($services[$tipo])) $services[$tipo] = [];

                $services[$tipo][] = [
                    'id_servicio' => $row['id_servicio'], // NECESARIO PARA EL FORMULARIO
                    'nombre' => $row['servicio_nombre'],
                    'precio' => (float)$row['precio'],
                    'duracion' => '30 min',
                    'descripcion' => $row['descripcion'],
                ];
            }
        }
        return $services;
    }

    public function __destruct()
    {
        if ($this->db) $this->db->close();
    }
}
