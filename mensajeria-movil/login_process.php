<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$nif      = trim($_POST['nif'] ?? '');
$password = $_POST['password'] ?? '';
$rol      = $_POST['rol'] === 'admin' ? 'admin' : 'cliente';

if ($nif === '' || $password === '') {
    $_SESSION['login_error'] = 'Debes completar el NIF y la contraseña.';
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nif = ? AND rol = ?");
$stmt->execute([$nif, $rol]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    $_SESSION['login_error'] = 'NIF, contraseña o tipo de cuenta incorrectos.';
    header('Location: login.php');
    exit;
}

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nombre']     = $usuario['nombre'];
$_SESSION['nif']        = $usuario['nif'];
$_SESSION['rol']        = $usuario['rol'];

header('Location: ' . ($usuario['rol'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'));
exit;
