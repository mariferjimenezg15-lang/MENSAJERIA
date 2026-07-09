<?php
/**
 * EJECUTA ESTE ARCHIVO UNA SOLA VEZ (desde el navegador o con php crear_admin.php)
 * para crear el primer usuario administrador. Después BÓRRALO del servidor
 * por seguridad.
 */
require_once __DIR__ . '/config.php';

$nif      = 'admin';
$nombre   = 'Administrador del Sistema';
$password = 'admin123'; // <-- cámbiala antes de ejecutar el script

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nif = ?");
$stmt->execute([$nif]);

if ($stmt->fetch()) {
    echo "Ya existe un usuario con NIF '$nif'. No se creó ninguno nuevo.";
} else {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nif, nombre, password, rol) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$nif, $nombre, $hash]);
    echo "Usuario administrador creado correctamente.<br>";
    echo "NIF: $nif<br>Contraseña: $password<br><br>";
    echo "<strong>Importante: borra este archivo (crear_admin.php) del servidor ahora mismo.</strong>";
}
