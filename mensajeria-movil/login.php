<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (estaLogueado()) {
    header('Location: ' . ($_SESSION['rol'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'));
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - Mensajería Móvil</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --blue-1: #A8D2F0;
    --blue-2: #C7B9F0;
    --blue-deep: #6C8FE3;
    --blue-dark: #5674CC;
    --card: #FFFFFF;
    --ink: #3A3F5C;
    --muted: #8E93B0;
    --pink: #F5A9C4;
    --error: #E0577A;
    --error-bg: #FDEBF1;
  }

  * { box-sizing: border-box; }

  body{
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(160deg, var(--blue-1) 0%, var(--blue-2) 55%, #E4D6F3 100%);
    font-family: 'Nunito', sans-serif;
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    position: relative;
    overflow: hidden;
  }

  /* decorative soft blobs */
  body::before, body::after{
    content: "";
    position: fixed;
    border-radius: 50%;
    filter: blur(0px);
    z-index: 0;
  }
  body::before{
    width: 260px; height: 260px;
    background: radial-gradient(circle at 30% 30%, #FFFFFF80, transparent 70%);
    top: -60px; left: -60px;
  }
  body::after{
    width: 320px; height: 320px;
    background: radial-gradient(circle at 70% 70%, #FFD6E8AA, transparent 70%);
    bottom: -80px; right: -80px;
  }

  .sparkle{
    position: fixed;
    color: #FFFFFFCC;
    z-index: 0;
    pointer-events: none;
  }
  .sparkle svg{ width: 100%; height: 100%; }

  .wrap{
    width: 100%;
    max-width: 360px;
    position: relative;
    z-index: 1;
  }

  .bubble{
    width: 60px;
    height: 60px;
    margin: 0 auto 14px;
    background: linear-gradient(135deg, var(--blue-deep), var(--pink));
    border-radius: 20px 20px 20px 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 18px -6px rgba(86, 116, 204, 0.55);
  }
  .bubble svg{ width: 26px; height: 26px; }

  h1{
    text-align: center;
    font-family: 'Baloo 2', sans-serif;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--blue-dark);
  }
  .sub{
    text-align: center;
    font-size: 13px;
    color: #6C7396;
    margin: 0 0 22px;
  }

  .card{
    background: var(--card);
    border-radius: 24px;
    padding: 26px 22px;
    box-shadow: 0 20px 40px -18px rgba(86, 116, 204, 0.45);
  }

  .rol-toggle{
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #EEF2FC;
    border-radius: 14px;
    padding: 4px;
    margin-bottom: 20px;
  }
  .rol-toggle button{
    appearance: none;
    border: none;
    background: transparent;
    font-family: 'Nunito', sans-serif;
    font-weight: 700;
    font-size: 13px;
    padding: 9px 6px;
    border-radius: 11px;
    cursor: pointer;
    color: var(--blue-dark);
  }
  .rol-toggle button.active{
    background: linear-gradient(135deg, var(--blue-deep), #8AA6EA);
    color: #fff;
  }
  .rol-toggle button:focus-visible{
    outline: 2px solid var(--blue-dark);
    outline-offset: 2px;
  }

  .field{ margin-bottom: 16px; }
  .field label{
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--muted);
    margin-bottom: 5px;
  }
  .field input{
    width: 100%;
    background: #F6F8FE;
    border: 2px solid transparent;
    border-radius: 14px;
    padding: 11px 13px;
    font-family: 'Nunito', sans-serif;
    font-size: 14px;
    color: var(--ink);
  }
  .field input:focus-visible{
    outline: none;
    border-color: var(--blue-deep);
  }

  .alert{
    background: var(--error-bg);
    color: var(--error);
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 12.5px;
    font-weight: 600;
    margin-bottom: 16px;
  }

  button.submit{
    width: 100%;
    appearance: none;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--blue-deep), var(--pink));
    color: #fff;
    font-family: 'Baloo 2', sans-serif;
    font-weight: 600;
    font-size: 15px;
    padding: 13px;
    cursor: pointer;
    transition: transform 0.12s ease, box-shadow 0.15s ease;
    box-shadow: 0 10px 20px -10px rgba(86, 116, 204, 0.7);
  }
  button.submit:hover{ transform: translateY(-1px); }
  button.submit:active{ transform: translateY(0px); }
  button.submit:focus-visible{
    outline: 2px solid var(--blue-dark);
    outline-offset: 2px;
  }

  .register{
    text-align: center;
    font-size: 13px;
    color: var(--muted);
    margin: 16px 0 0;
  }
  .register a{
    color: var(--blue-dark);
    font-weight: 700;
    text-decoration: none;
  }
  .register a:hover{ text-decoration: underline; }

  @media (prefers-reduced-motion: reduce){
    button.submit{ transition: none; }
  }
</style>
</head>
<body>
  <div class="sparkle" style="top:14%; left:12%; width:18px; height:18px;">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2 8 8 2-8 2-2 8-2-8-8-2 8-2z"/></svg>
  </div>
  <div class="sparkle" style="top:70%; left:8%; width:12px; height:12px;">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2 8 8 2-8 2-2 8-2-8-8-2 8-2z"/></svg>
  </div>
  <div class="sparkle" style="top:20%; right:10%; width:14px; height:14px;">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2 8 8 2-8 2-2 8-2-8-8-2 8-2z"/></svg>
  </div>

<div class="wrap">
  <div class="bubble" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M4 4h16v12H8l-4 4V4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
    </svg>
  </div>
  <h1>Mensajería Móvil</h1>
  <p class="sub">Inicia sesión para continuar</p>

  <div class="card">
    <div class="rol-toggle" role="tablist" aria-label="Tipo de cuenta">
      <button type="button" id="tab-cliente-btn" class="active" data-rol="cliente" onclick="setRol('cliente')" role="tab" aria-selected="true">Cliente</button>
      <button type="button" id="tab-admin-btn" data-rol="admin" onclick="setRol('admin')" role="tab" aria-selected="false">Administrador</button>
    </div>

    <?php if ($error): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login_process.php" method="post" novalidate>
      <input type="hidden" name="rol" id="rolInput" value="cliente">

      <div class="field">
        <label for="nif">NIF</label>
        <input type="text" id="nif" name="nif" required autofocus autocomplete="username">
      </div>

      <div class="field">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>

      <button type="submit" class="submit">Iniciar sesión</button>
    </form>

    <p class="register" id="registroLink">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
  </div>
</div>

<script>
function setRol(rol) {
    document.getElementById('rolInput').value = rol;
    const cliente = document.getElementById('tab-cliente-btn');
    const admin = document.getElementById('tab-admin-btn');
    cliente.classList.toggle('active', rol === 'cliente');
    admin.classList.toggle('active', rol === 'admin');
    cliente.setAttribute('aria-selected', rol === 'cliente');
    admin.setAttribute('aria-selected', rol === 'admin');
    document.getElementById('registroLink').style.display = rol === 'cliente' ? 'block' : 'none';
}
</script>
</body>
</html>
