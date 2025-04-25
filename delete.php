<?php
require_once 'db.php';

// Respuesta por defecto
$response = [
    'status' => 'error',
    'message' => 'Ha ocurrido un error desconocido'
];

// Validar que se recibió el ID de la tarea
if (isset($_POST['task_id'])) {
    $task_id = (int)$_POST['task_id'];
    
    try {
        // Preparar la consulta SQL para eliminar la tarea
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $result = $stmt->execute([$task_id]);
        
        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Tarea eliminada correctamente'
            ];
        } else {
            $response['message'] = 'No se pudo eliminar la tarea';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Falta el ID de la tarea';
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);