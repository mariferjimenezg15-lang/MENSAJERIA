<?php
$u = usuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' - ' : '' ?>Mensajería Móvil (Admin)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link href="../assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">💬 Mensajería Móvil · Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="telefonos.php">Teléfonos</a></li>
        <li class="nav-item"><a class="nav-link" href="mensajes.php">Mensajes</a></li>
        <li class="nav-item dropdown ms-lg-2">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            <?= htmlspecialchars($u['nombre']) ?>
            <span class="badge bg-light text-dark rounded-pill">Admin</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../logout.php">Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<main class="container my-4">
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['tipo']) ?>" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['mensaje']) ?>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
