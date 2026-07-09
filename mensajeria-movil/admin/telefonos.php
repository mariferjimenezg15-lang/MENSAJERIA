<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($accion === 'toggle_estado') {
        $stmt = $pdo->prepare("SELECT estado FROM telefonos WHERE id = ?");
        $stmt->execute([$id]);
        $actual = $stmt->fetchColumn();
        if ($actual !== false) {
            $nuevo = $actual === 'conectado' ? 'desconectado' : 'conectado';
            $stmt = $pdo->prepare("UPDATE telefonos SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo, $id]);
            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Estado actualizado.'];
        }
    }

    if ($accion === 'eliminar') {
        $stmt = $pdo->prepare("DELETE FROM telefonos WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Teléfono eliminado.'];
    }

    header('Location: telefonos.php');
    exit;
}

$telefonos = $pdo->query("
    SELECT t.*, u.nombre AS nombre_cliente, u.nif
    FROM telefonos t
    JOIN usuarios u ON u.id = t.usuario_id
    ORDER BY t.fecha_alta DESC
")->fetchAll();

$tituloPagina = 'Teléfonos';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Todos los Teléfonos</h2>

  <div class="table-responsive mt-3">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Número</th>
          <th>Cliente</th>
          <th>Saldo</th>
          <th>Estado</th>
          <th>Desvío</th>
          <th>Alta</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($telefonos)): ?>
          <tr><td colspan="7" class="text-center text-muted">No hay teléfonos registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($telefonos as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t['numero']) ?></td>
            <td><?= htmlspecialchars($t['nombre_cliente']) ?> (<?= htmlspecialchars($t['nif']) ?>)</td>
            <td>$<?= number_format($t['saldo'], 2) ?></td>
            <td>
              <span class="badge <?= $t['estado'] === 'conectado' ? 'badge-estado-conectado' : 'badge-estado-desconectado' ?>">
                <?= ucfirst($t['estado']) ?>
              </span>
            </td>
            <td><?= $t['desvio_numero'] ? htmlspecialchars($t['desvio_numero']) : '—' ?></td>
            <td><?= date('d/m/Y', strtotime($t['fecha_alta'])) ?></td>
            <td class="d-flex gap-1">
              <form method="post">
                <input type="hidden" name="accion" value="toggle_estado">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                  <?= $t['estado'] === 'conectado' ? 'Apagar' : 'Encender' ?>
                </button>
              </form>
              <form method="post" onsubmit="return confirm('¿Eliminar este teléfono?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>
