<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND rol = 'cliente'");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Usuario eliminado.'];
    header('Location: usuarios.php');
    exit;
}

$usuarios = $pdo->query("
    SELECT u.*, COUNT(t.id) AS num_telefonos
    FROM usuarios u
    LEFT JOIN telefonos t ON t.usuario_id = u.id
    WHERE u.rol = 'cliente'
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

$tituloPagina = 'Usuarios';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="bg-white rounded-3 shadow-sm p-4">
  <h2 class="text-primary fw-bold">Usuarios (Clientes)</h2>

  <div class="table-responsive mt-3">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>NIF</th>
          <th>Nombre</th>
          <th>Teléfonos</th>
          <th>Registrado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($usuarios)): ?>
          <tr><td colspan="5" class="text-center text-muted">No hay clientes registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($usuarios as $us): ?>
          <tr>
            <td><?= htmlspecialchars($us['nif']) ?></td>
            <td><?= htmlspecialchars($us['nombre']) ?></td>
            <td><?= (int)$us['num_telefonos'] ?></td>
            <td><?= date('d/m/Y', strtotime($us['created_at'])) ?></td>
            <td>
              <form method="post" onsubmit="return confirm('¿Eliminar este cliente y todos sus teléfonos?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $us['id'] ?>">
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
