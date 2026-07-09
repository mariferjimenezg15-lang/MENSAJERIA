<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
requiereRol('admin');

$mensajes = $pdo->query("SELECT * FROM mensajes ORDER BY fecha DESC LIMIT 500")->fetchAll();

$tituloPagina = 'Mensajes';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Todos los Mensajes</h2>

  <div class="table-responsive mt-3">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Remitente</th>
          <th>Destino</th>
          <th>Entregado a</th>
          <th>Mensaje</th>
          <th>Costo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($mensajes)): ?>
          <tr><td colspan="7" class="text-center text-muted">No hay mensajes registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($mensajes as $m): ?>
          <tr>
            <td><?= date('d/m/Y H:i', strtotime($m['fecha'])) ?></td>
            <td><?= htmlspecialchars($m['numero_origen']) ?></td>
            <td><?= htmlspecialchars($m['numero_destino']) ?></td>
            <td><?= htmlspecialchars($m['entregado_a'] ?? '—') ?></td>
            <td><?= htmlspecialchars($m['mensaje']) ?></td>
            <td>$<?= number_format($m['costo'], 2) ?></td>
            <td>
              <?php
                $badge = match ($m['estado']) {
                    'entregado' => 'success',
                    'pendiente' => 'warning text-dark',
                    'fallido'   => 'danger',
                    default     => 'secondary',
                };
              ?>
              <span class="badge bg-<?= $badge ?>"><?= ucfirst($m['estado']) ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>
