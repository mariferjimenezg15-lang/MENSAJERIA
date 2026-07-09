<?php
/**
 * Funciones de ayuda para autenticación y control de acceso.
 * Debe incluirse DESPUÉS de config.php (necesita la sesión ya iniciada).
 */

function estaLogueado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function requiereLogin(): void
{
    if (!estaLogueado()) {
        header('Location: login.php');
        exit;
    }
}

function requiereRol(string $rol): void
{
    requiereLogin();
    if ($_SESSION['rol'] !== $rol) {
        header('Location: ' . ($_SESSION['rol'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'));
        exit;
    }
}

function usuarioActual(): array
{
    return [
        'id'     => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? '',
        'nif'    => $_SESSION['nif'] ?? '',
        'rol'    => $_SESSION['rol'] ?? '',
    ];
}
