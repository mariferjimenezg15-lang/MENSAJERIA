<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();

// Cantidad de teléfonos y saldo total
$stmt = $pdo->prepare("SELECT COUNT(*) AS total, COALESCE(SUM(saldo),0) AS saldo_total FROM telefonos WHERE usuario_id = ?");
$stmt->execute([$u['id']]);
$resumenTel = $stmt->fetch();

// Mensajes enviados por el usuario (desde sus teléfonos)
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM mensajes m
    JOIN telefonos t ON t.id = m.telefono_origen_id
    WHERE t.usuario_id = ?
");
$stmt->execute([$u['id']]);
$mensajesEnviados = $stmt->fetch()['total'];

// Mensajes pendientes por recibir en los números del usuario
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM mensajes m
    JOIN telefonos t ON t.numero = m.numero_destino
    WHERE t.usuario_id = ? AND m.estado = 'pendiente'
");
$stmt->execute([$u['id']]);
$pendientes = $stmt->fetch()['total'];

$tituloPagina = 'Inicio';
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Bienvenido, <?= htmlspecialchars($u['nombre']) ?></h2>
  <p class="text-muted">Resumen de tu cuenta y teléfonos.</p>

  <h5 class="text-purple mt-4 mb-3" style="color:#6a3fd4;">Resumen</h5>
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Mis teléfonos</div>
        <div class="valor"><?= (int)$resumenTel['total'] ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Saldo total</div>
        <div class="valor">$<?= number_format($resumenTel['saldo_total'], 2) ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Mensajes enviados</div>
        <div class="valor"><?= (int)$mensajesEnviados ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-resumen text-center">
        <div class="text-muted small">Pendientes por recibir</div>
        <div class="valor"><?= (int)$pendientes ?></div>
      </div>
    </div>
  </div>

  <div class="mt-4 d-flex gap-2 flex-wrap">
    <a href="mis_telefonos.php" class="btn btn-primary">Mis Teléfonos</a>
    <a href="enviar_mensaje.php" class="btn btn-outline-primary">Enviar Mensaje</a>
    <a href="historial.php" class="btn btn-outline-secondary">Ver Historial</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
