<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();

function volver(string $tipo, string $msg): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $msg];
    header('Location: enviar_mensaje.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: enviar_mensaje.php');
    exit;
}

$telefonoOrigenId = (int)($_POST['telefono_origen_id'] ?? 0);
$numeroDestino     = trim($_POST['numero_destino'] ?? '');
$mensaje           = trim($_POST['mensaje'] ?? '');

if (!preg_match('/^\d{10}$/', $numeroDestino)) {
    volver('danger', 'El número destino debe tener 10 dígitos.');
}
if ($mensaje === '' || mb_strlen($mensaje) > MAX_CARACTERES) {
    volver('danger', 'El mensaje no puede estar vacío ni superar ' . MAX_CARACTERES . ' caracteres.');
}

// Verificar que el teléfono de origen pertenece al usuario
$stmt = $pdo->prepare("SELECT * FROM telefonos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$telefonoOrigenId, $u['id']]);
$origen = $stmt->fetch();

if (!$origen) {
    volver('danger', 'Selecciona un teléfono de origen válido.');
}
if ($origen['estado'] !== 'conectado') {
    volver('danger', 'El teléfono de origen debe estar encendido para enviar mensajes.');
}
if ($origen['saldo'] < COSTO_MENSAJE) {
    volver('danger', 'Saldo insuficiente en el teléfono de origen. Recarga saldo para continuar.');
}

// --- Resolver la cadena de desvíos a partir del número destino ---
$actual   = $numeroDestino;
$visitados = [];
$telefonoDestino = null;

while (true) {
    if (in_array($actual, $visitados, true)) {
        // Bucle de desvíos detectado, se detiene en el último válido
        break;
    }
    $visitados[] = $actual;

    $stmt = $pdo->prepare("SELECT * FROM telefonos WHERE numero = ?");
    $stmt->execute([$actual]);
    $tel = $stmt->fetch();

    if (!$tel) {
        // El número no está registrado en el sistema; termina aquí
        $telefonoDestino = null;
        break;
    }

    if (!empty($tel['desvio_numero'])) {
        $actual = $tel['desvio_numero'];
        continue;
    }

    $telefonoDestino = $tel;
    break;
}

$entregadoA = $actual;
$estado = 'pendiente';
if ($telefonoDestino && $telefonoDestino['estado'] === 'conectado') {
    $estado = 'entregado';
} elseif ($telefonoDestino && $telefonoDestino['estado'] === 'desconectado') {
    $estado = 'pendiente';
} else {
    // Número no registrado en el sistema: se considera entregado a la red externa
    $estado = 'entregado';
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE telefonos SET saldo = saldo - ? WHERE id = ? AND saldo >= ?");
    $stmt->execute([COSTO_MENSAJE, $origen['id'], COSTO_MENSAJE]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Saldo insuficiente en el momento de confirmar el envío.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO mensajes (telefono_origen_id, numero_origen, numero_destino, entregado_a, mensaje, costo, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $origen['id'],
        $origen['numero'],
        $numeroDestino,
        $entregadoA,
        $mensaje,
        COSTO_MENSAJE,
        $estado,
    ]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    volver('danger', 'No se pudo enviar el mensaje: ' . $e->getMessage());
}

volver('success', 'Mensaje enviado correctamente. Estado: ' . $estado . '.');
