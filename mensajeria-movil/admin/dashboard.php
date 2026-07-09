<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
requiereRol('admin');

$totalUsuarios  = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'")->fetchColumn();
$totalTelefonos = $pdo->query("SELECT COUNT(*) FROM telefonos")->fetchColumn();
$totalMensajes  = $pdo->query("SELECT COUNT(*) FROM mensajes")->fetchColumn();
$saldoGlobal    = $pdo->query("SELECT COALESCE(SUM(saldo),0) FROM telefonos")->fetchColumn();

$tituloPagina = 'Panel de Administración';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Panel de Administración</h2>
  <p class="text-muted">Resumen general del sistema.</p>

  <div class="row g-3 mt-2">
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Clientes</div>
        <div class="valor"><?= (int)$totalUsuarios ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Teléfonos</div>
        <div class="valor"><?= (int)$totalTelefonos ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Mensajes</div>
        <div class="valor"><?= (int)$totalMensajes ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Saldo global</div>
        <div class="valor">$<?= number_format($saldoGlobal, 2) ?></div>
      </div>
    </div>
  </div>

  <div class="mt-4 d-flex gap-2 flex-wrap">
    <a href="usuarios.php" class="btn btn-primary">Ver Usuarios</a>
    <a href="telefonos.php" class="btn btn-outline-primary">Ver Teléfonos</a>
    <a href="mensajes.php" class="btn btn-outline-secondary">Ver Mensajes</a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>
