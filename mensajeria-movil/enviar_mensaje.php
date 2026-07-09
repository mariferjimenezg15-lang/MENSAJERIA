<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();

$stmt = $pdo->prepare("SELECT * FROM telefonos WHERE usuario_id = ? ORDER BY numero");
$stmt->execute([$u['id']]);
$telefonos = $stmt->fetchAll();

$desdeSeleccionado = (int)($_GET['desde'] ?? 0);

$tituloPagina = 'Enviar Mensaje';
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Enviar Mensaje</h2>
  <p class="text-muted">Costo por mensaje: <strong>$<?= number_format(COSTO_MENSAJE, 2) ?></strong> · Máximo <?= MAX_CARACTERES ?> caracteres.</p>

  <form action="enviar_mensaje_process.php" method="post" id="formMensaje">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Desde (tu teléfono)</label>
        <select name="telefono_origen_id" class="form-select" required>
          <option value="">Selecciona...</option>
          <?php foreach ($telefonos as $t): ?>
            <option value="<?= $t['id'] ?>" <?= $t['id'] == $desdeSeleccionado ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['numero']) ?>
              (<?= $t['estado'] ?>, saldo $<?= number_format($t['saldo'], 2) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($telefonos)): ?>
          <div class="form-text text-danger">No tienes teléfonos registrados. <a href="mis_telefonos.php">Agrega uno</a>.</div>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label">Número destino</label>
        <input type="text" name="numero_destino" class="form-control" placeholder="10 dígitos" pattern="\d{10}" maxlength="10" required>
      </div>
    </div>

    <div class="mt-3">
      <label class="form-label">Mensaje</label>
      <textarea name="mensaje" id="mensajeTexto" class="form-control" rows="4" maxlength="<?= MAX_CARACTERES ?>" required></textarea>
      <div class="form-text"><span id="contador">0</span>/<?= MAX_CARACTERES ?> caracteres</div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
      <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
    </div>
  </form>
</div>

<script>
const textarea = document.getElementById('mensajeTexto');
const contador  = document.getElementById('contador');
textarea.addEventListener('input', () => {
    contador.textContent = textarea.value.length;
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
