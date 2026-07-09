<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (estaLogueado()) {
    header('Location: ' . ($_SESSION['rol'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'));
} else {
    header('Location: login.php');
}
exit;
