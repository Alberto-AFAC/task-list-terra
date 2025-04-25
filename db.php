<?php

/**
 * Archivo de configuración de conexión a la base de datos
 * Utilizando PDO para conectar a MySQL
 */

// Credenciales de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'task-list-terra');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Opciones PDO para manejo de errores
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Conexión PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        $options
    );
} catch (PDOException $e) {
    // En caso de error en la conexión
    die("Error de conexión: " . $e->getMessage());
}
