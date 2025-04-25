<?php
require_once 'db.php';

// Respuesta por defecto
$response = [
    'status' => 'error',
    'message' => 'Ha ocurrido un error desconocido',
    'task' => null
];

// Validar que se recibieron los datos necesarios
if (isset($_POST['task_name']) && isset($_POST['user_id'])) {
    $task_name = trim($_POST['task_name']);
    $user_id = (int)$_POST['user_id'];

    // Validar que los datos no estén vacíos
    if (empty($task_name)) {
        $response['message'] = 'El nombre de la tarea no puede estar vacío';
    } else {
        try {
            // Preparar la consulta SQL para insertar la tarea
            $stmt = $pdo->prepare("INSERT INTO tasks (task_name, user_id, created_at) VALUES (?, ?, NOW())");
            $result = $stmt->execute([$task_name, $user_id]);

            if ($result) {
                // Obtener el ID de la tarea recién insertada
                $taskId = $pdo->lastInsertId();

                // Obtener los datos completos de la tarea para devolverlos
                $stmt = $pdo->prepare("
                    SELECT t.id, t.task_name, t.created_at, u.username 
                    FROM tasks t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = ?
                ");
                $stmt->execute([$taskId]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($task) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Tarea agregada correctamente',
                        'task' => $task
                    ];
                } else {
                    $response['message'] = 'La tarea fue creada pero no se pudo recuperar su información';
                }
            } else {
                $response['message'] = 'No se pudo agregar la tarea';
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
