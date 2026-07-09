<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();

$stmt = $pdo->prepare("
    SELECT DISTINCT m.*
    FROM mensajes m
    LEFT JOIN telefonos t_origen    ON t_origen.id = m.telefono_origen_id
    LEFT JOIN telefonos t_destino   ON t_destino.numero = m.numero_destino
    LEFT JOIN telefonos t_entregado ON t_entregado.numero = m.entregado_a
    WHERE t_origen.usuario_id = ? OR t_destino.usuario_id = ? OR t_entregado.usuario_id = ?
    ORDER BY m.fecha DESC
");
$stmt->execute([$u['id'], $u['id'], $u['id']]);
$mensajes = $stmt->fetchAll();

$tituloPagina = 'Historial';
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Historial de Mensajes</h2>
  <p class="text-muted">Mensajes enviados y recibidos de tus teléfonos.</p>

  <div class="table-responsive">
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
