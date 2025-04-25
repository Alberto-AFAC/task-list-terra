<?php
require_once 'db.php';

// Respuesta por defecto
$response = [
    'status' => 'error',
    'message' => 'Ha ocurrido un error desconocido',
    'task' => null
];

// Validar que se recibieron los datos necesarios
if (isset($_POST['task_id']) && isset($_POST['task_name']) && isset($_POST['user_id'])) {
    $task_id = (int)$_POST['task_id'];
    $task_name = trim($_POST['task_name']);
    $user_id = (int)$_POST['user_id'];

    // Validar que los datos no estén vacíos
    if (empty($task_name)) {
        $response['message'] = 'El nombre de la tarea no puede estar vacío';
    } else {
        try {
            // Preparar la consulta SQL para actualizar la tarea
            $stmt = $pdo->prepare("UPDATE tasks SET task_name = ?, user_id = ? WHERE id = ?");
            $result = $stmt->execute([$task_name, $user_id, $task_id]);

            if ($result) {
                // Obtener los datos actualizados de la tarea para devolverlos
                $stmt = $pdo->prepare("
                    SELECT t.id, t.user_id, t.task_name, t.created_at, u.username 
                    FROM tasks t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = ?
                ");
                $stmt->execute([$task_id]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($task) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Tarea actualizada correctamente',
                        'task' => $task
                    ];
                } else {
                    $response['message'] = 'La tarea fue actualizada pero no se pudo recuperar su información';
                }
            } else {
                $response['message'] = 'No se pudo actualizar la tarea';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Faltan datos requeridos';
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
