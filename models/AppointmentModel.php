<?php
// models/AppointmentModel.php
require_once __DIR__ . '/../config/db_connection.php';

class AppointmentModel
{

    public function createAppointment($id_cliente, $id_barbero, $id_servicio, $fecha, $hora)
    {
        $db = getDbConnection();

        $checkSql = "SELECT id_horario_atencion FROM horarios_atencion 
                     WHERE id_barbero = ? AND fecha = ? AND hora = ? AND estado = 'RESERVADO'";
        $stmtCheck = $db->prepare($checkSql);
        $stmtCheck->bind_param("iss", $id_barbero, $fecha, $hora);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            $stmtCheck->close();
            $db->close();
            return ['success' => false, 'message' => 'El barbero ya tiene una reserva en ese horario.'];
        }
        $stmtCheck->close();

        $db->begin_transaction();

        try {
            $estado_horario = 'RESERVADO';
            $stmtHorario = $db->prepare("INSERT INTO horarios_atencion (id_barbero, fecha, hora, estado) VALUES (?, ?, ?, ?)");
            $stmtHorario->bind_param("isss", $id_barbero, $fecha, $hora, $estado_horario);
            $stmtHorario->execute();
            $id_horario = $db->insert_id;
            $stmtHorario->close();

            $estado_cita = 'POR_ATENDER';
            $stmtCita = $db->prepare("INSERT INTO citas (id_cliente, id_horario_atencion, id_servicio, estado) VALUES (?, ?, ?, ?)");
            $stmtCita->bind_param("iiis", $id_cliente, $id_horario, $id_servicio, $estado_cita);
            $stmtCita->execute();
            $id_cita = $db->insert_id;
            $stmtCita->close();

            $db->commit();
            $db->close();
            return ['success' => true, 'message' => '¡Cita reservada con éxito! ID de cita: #' . $id_cita];
        } catch (Exception $e) {
            $db->rollback();
            $db->close();
            return ['success' => false, 'message' => 'Error al procesar la reserva: ' . $e->getMessage()];
        }
    }

    public function getAppointmentsByClient(int $id_cliente): array
    {
        $db = getDbConnection();
        $appointments = [];

        // AGREGADO: c.id_comentario en el SELECT para saber si ya tiene comentario
        $sql = "SELECT c.id_cita, c.estado, c.id_comentario,
                       h.fecha, h.hora, 
                       s.nombre as servicio, s.tarifa, 
                       CONCAT(u.nombres, ' ', u.apellidos) as barbero
                FROM citas c
                JOIN horarios_atencion h ON c.id_horario_atencion = h.id_horario_atencion
                JOIN servicios s ON c.id_servicio = s.id_servicio
                JOIN barberos b ON h.id_barbero = b.id_barbero
                JOIN usuarios u ON b.id_usuario = u.id_usuario
                WHERE c.id_cliente = ?
                ORDER BY h.fecha DESC, h.hora DESC";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        $stmt->close();
        $db->close();
        return $appointments;
    }

    // --- NUEVO MÉTODO: Agregar Comentario ---
    public function addComment(int $id_cita, string $contenido): bool
    {
        $db = getDbConnection();
        $db->begin_transaction();

        try {
            // 1. Insertar el comentario en la tabla 'comentarios'
            $stmt = $db->prepare("INSERT INTO comentarios (contenido) VALUES (?)");
            $stmt->bind_param("s", $contenido);
            $stmt->execute();
            $id_comentario = $db->insert_id;
            $stmt->close();

            // 2. Actualizar la cita para vincular el comentario
            $stmtUpdate = $db->prepare("UPDATE citas SET id_comentario = ? WHERE id_cita = ?");
            $stmtUpdate->bind_param("ii", $id_comentario, $id_cita);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            $db->commit();
            $db->close();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            $db->close();
            return false;
        }
    }
}
