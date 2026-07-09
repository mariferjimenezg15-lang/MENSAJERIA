<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u  = usuarioActual();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM telefonos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $u['id']]);
$telefono = $stmt->fetch();

if (!$telefono) {
    $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Teléfono no encontrado.'];
    header('Location: mis_telefonos.php');
    exit;
}

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'toggle_estado') {
        $nuevoEstado = $telefono['estado'] === 'conectado' ? 'desconectado' : 'conectado';
        $stmt = $pdo->prepare("UPDATE telefonos SET estado = ? WHERE id = ?");
        $stmt->execute([$nuevoEstado, $telefono['id']]);
        flash('success', 'Estado actualizado a "' . $nuevoEstado . '".');
    }

    if ($accion === 'recargar') {
        $monto = (float)($_POST['monto'] ?? 0);
        if ($monto <= 0) {
            flash('danger', 'Ingresa un monto válido para recargar.');
        } else {
            $stmt = $pdo->prepare("UPDATE telefonos SET saldo = saldo + ? WHERE id = ?");
            $stmt->execute([$monto, $telefono['id']]);
            flash('success', 'Recarga de $' . number_format($monto, 2) . ' aplicada.');
        }
    }

    if ($accion === 'desvio') {
        $destino = trim($_POST['destino'] ?? '');
        if (!preg_match('/^\d{10}$/', $destino)) {
            flash('danger', 'El número de desvío debe tener 10 dígitos.');
        } elseif ($destino === $telefono['numero']) {
            flash('danger', 'No puedes desviar un teléfono hacia sí mismo.');
        } else {
            $stmt = $pdo->prepare("UPDATE telefonos SET desvio_numero = ? WHERE id = ?");
            $stmt->execute([$destino, $telefono['id']]);
            flash('success', 'Desvío activado hacia ' . $destino . '.');
        }
    }

    if ($accion === 'quitar_desvio') {
        $stmt = $pdo->prepare("UPDATE telefonos SET desvio_numero = NULL WHERE id = ?");
        $stmt->execute([$telefono['id']]);
        flash('success', 'Desvío desactivado.');
    }

    header('Location: telefono.php?id=' . $telefono['id']);
    exit;
}

$tituloPagina = 'Teléfono ' . $telefono['numero'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Teléfono <?= htmlspecialchars($telefono['numero']) ?></h2>
  <p class="text-muted">
    Estado:
    <span class="badge <?= $telefono['estado'] === 'conectado' ? 'badge-estado-conectado' : 'badge-estado-desconectado' ?>">
      <?= ucfirst($telefono['estado']) ?>
    </span>
    &nbsp;|&nbsp; Saldo: <strong>$<?= number_format($telefono['saldo'], 2) ?></strong>
    &nbsp;|&nbsp; Desvío: <strong><?= $telefono['desvio_numero'] ? htmlspecialchars($telefono['desvio_numero']) : 'Ninguno' ?></strong>
  </p>

  <div class="row g-4 mt-2">
    <div class="col-md-6">
      <h6 style="color:#5674CC;">Estado del Teléfono</h6>
      <form method="post">
        <input type="hidden" name="accion" value="toggle_estado">
        <button type="submit" class="btn <?= $telefono['estado'] === 'conectado' ? 'btn-secondary' : 'btn-success' ?>">
          <?= $telefono['estado'] === 'conectado' ? 'Apagar Teléfono' : 'Encender Teléfono' ?>
        </button>
      </form>
    </div>

    <div class="col-md-6">
      <h6 style="color:#5674CC;">Recargar Saldo</h6>
      <form method="post" class="d-flex gap-2">
        <input type="hidden" name="accion" value="recargar">
        <input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="Monto" required>
        <button type="submit" class="btn btn-primary text-nowrap">Recargar</button>
      </form>
    </div>
  </div>

  <hr class="my-4">

  <h6 style="color:#5674CC;">Desvío de Llamadas/Mensajes</h6>
  <form method="post" class="d-flex gap-2">
    <input type="hidden" name="accion" value="desvio">
    <input type="text" name="destino" class="form-control" placeholder="Número destino" pattern="\d{10}" maxlength="10" value="<?= htmlspecialchars($telefono['desvio_numero'] ?? '') ?>">
    <button type="submit" class="btn btn-primary text-nowrap">Activar Desvío</button>
  </form>
  <?php if ($telefono['desvio_numero']): ?>
    <form method="post" class="mt-2">
      <input type="hidden" name="accion" value="quitar_desvio">
      <button type="submit" class="btn btn-outline-danger btn-sm">Quitar desvío</button>
    </form>
  <?php endif; ?>

  <div class="mt-4 d-flex gap-2">
    <a href="enviar_mensaje.php?desde=<?= $telefono['id'] ?>" class="btn btn-outline-primary">Enviar Mensaje desde este Teléfono</a>
    <a href="historial.php" class="btn btn-outline-secondary">Ver Historial</a>
    <a href="mis_telefonos.php" class="btn btn-secondary">Volver</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
