<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nif       = trim($_POST['nif'] ?? '');
$nombre    = trim($_POST['nombre'] ?? '');
$password  = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

function volverConError(string $msg): void
{
    $_SESSION['register_error'] = $msg;
    header('Location: register.php');
    exit;
}

if ($nif === '' || $nombre === '' || $password === '' || $password2 === '') {
    volverConError('Todos los campos son obligatorios.');
}
if (strlen($password) < 6) {
    volverConError('La contraseña debe tener al menos 6 caracteres.');
}
if ($password !== $password2) {
    volverConError('Las contraseñas no coinciden.');
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nif = ?");
$stmt->execute([$nif]);
if ($stmt->fetch()) {
    volverConError('Ya existe una cuenta registrada con ese NIF.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nif, nombre, password, rol) VALUES (?, ?, ?, 'cliente')");
$stmt->execute([$nif, $nombre, $hash]);

$_SESSION['usuario_id'] = $pdo->lastInsertId();
$_SESSION['nombre']     = $nombre;
$_SESSION['nif']        = $nif;
$_SESSION['rol']        = 'cliente';

header('Location: dashboard.php');
exit;
