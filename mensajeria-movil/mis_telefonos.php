<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

// Agregar nuevo teléfono
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $numero = trim($_POST['numero'] ?? '');

    if (!preg_match('/^\d{10}$/', $numero)) {
        flash('danger', 'El número debe tener exactamente 10 dígitos.');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM telefonos WHERE numero = ?");
        $stmt->execute([$numero]);
        if ($stmt->fetch()) {
            flash('danger', 'Ese número ya está registrado en el sistema.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO telefonos (usuario_id, numero, saldo, estado, fecha_alta) VALUES (?, ?, 0, 'desconectado', CURDATE())");
            $stmt->execute([$u['id'], $numero]);
            flash('success', 'Teléfono agregado correctamente.');
        }
    }
    header('Location: mis_telefonos.php');
    exit;
}

// Dar de baja
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'baja') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM telefonos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $u['id']]);
    flash('success', 'Teléfono dado de baja.');
    header('Location: mis_telefonos.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM telefonos WHERE usuario_id = ? ORDER BY fecha_alta DESC");
$stmt->execute([$u['id']]);
$telefonos = $stmt->fetchAll();

$tituloPagina = 'Mis Teléfonos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Mis Teléfonos</h2>
  <p class="text-muted">Administra tus números: estado, saldo y desvío.</p>

  <h5 class="mt-4 mb-3" style="color:#5674CC;">Agregar Nuevo Teléfono</h5>
  <p class="text-muted small mb-2">Ya tienes una cuenta en el sistema, solo necesitas indicar el nuevo número.</p>
  <form method="post" class="row g-2 align-items-center mb-4">
    <input type="hidden" name="accion" value="agregar">
    <div class="col-auto">
      <input type="text" name="numero" class="form-control" placeholder="10 dígitos" pattern="\d{10}" maxlength="10" required>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Agregar</button>
    </div>
  </form>

  <h5 class="mb-3" style="color:#5674CC;">Mis Números</h5>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Número</th>
          <th>Saldo</th>
          <th>Estado</th>
          <th>Desvío</th>
          <th>Alta</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($telefonos)): ?>
          <tr><td colspan="6" class="text-center text-muted">No tienes teléfonos registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($telefonos as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t['numero']) ?></td>
            <td>$<?= number_format($t['saldo'], 2) ?></td>
            <td>
              <span class="badge <?= $t['estado'] === 'conectado' ? 'badge-estado-conectado' : 'badge-estado-desconectado' ?>">
                <?= ucfirst($t['estado']) ?>
              </span>
            </td>
            <td><?= $t['desvio_numero'] ? htmlspecialchars($t['desvio_numero']) : '—' ?></td>
            <td><?= date('d/m/Y', strtotime($t['fecha_alta'])) ?></td>
            <td>
              <a href="telefono.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Administrar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Dar de baja este teléfono? Esta acción no se puede deshacer.');">
                <input type="hidden" name="accion" value="baja">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Dar de baja</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>