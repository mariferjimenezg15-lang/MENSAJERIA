<?php
/**
 * Configuración de conexión a la base de datos.
 * Ajusta estos valores a los de tu servidor MySQL.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'mensajeria_movil');
define('DB_USER', 'root');
define('DB_PASS', '');
define('COSTO_MENSAJE', 1.00);   // Costo por mensaje enviado
define('MAX_CARACTERES', 150);   // Máximo de caracteres por mensaje

session_start();

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
}
