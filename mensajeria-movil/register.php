<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (estaLogueado()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - Mensajería Móvil</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-card">
  <h1 class="auth-title">Crear Cuenta</h1>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form action="register_process.php" method="post">
    <div class="mb-3">
      <label class="form-label">NIF</label>
      <input type="text" name="nif" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nombre completo</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" minlength="6" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirmar contraseña</label>
      <input type="password" name="password2" class="form-control" minlength="6" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">Registrarme</button>
  </form>

  <p class="text-center mt-3 mb-0">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>
</body>
</html>
