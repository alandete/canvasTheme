<?php
require_once __DIR__ . '/auth.php';
setSecurityHeaders();

// Si ya está configurado, redirigir al login
if (isConfigured()) {
    header('Location: login.php');
    exit;
}

$error = '';
$step = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminUser     = strtolower(trim($_POST['admin_user'] ?? ''));
    $adminPassword = $_POST['admin_password'] ?? '';
    $adminConfirm  = $_POST['admin_confirm'] ?? '';
    $createGuest = isset($_POST['create_guest']);
    $email       = strtolower(trim($_POST['recovery_email'] ?? ''));

    // Validaciones
    if (!$adminUser || !$adminPassword) {
        $error = 'El usuario y contraseña del administrador son obligatorios.';
    } elseif (mb_strlen($adminUser) < 3 || mb_strlen($adminUser) > 30) {
        $error = 'El usuario admin debe tener entre 3 y 30 caracteres.';
    } elseif (!preg_match('/^[a-z0-9_]+$/', $adminUser)) {
        $error = 'El usuario admin solo puede contener letras, números y guion bajo.';
    } elseif (mb_strlen($adminPassword) < 6) {
        $error = 'La contraseña del admin debe tener al menos 6 caracteres.';
    } elseif ($adminPassword !== $adminConfirm) {
        $error = 'Las contraseñas del admin no coinciden.';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        $config = [
            'created_at'     => date('Y-m-d H:i:s'),
            'recovery_email' => $email,
            'users'          => [
                $adminUser => [
                    'password' => password_hash($adminPassword, PASSWORD_DEFAULT),
                    'role'     => 'admin'
                ]
            ]
        ];

        if ($createGuest) {
            $config['users']['invitado'] = [
                'password' => password_hash('invitado2026', PASSWORD_DEFAULT),
                'role'     => 'guest'
            ];
        }

        saveConfig($config);
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Configuración Inicial</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Lato', sans-serif;
      background: #1A1A2E;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .setup-card {
      background: #FFF;
      border-radius: 12px;
      padding: 40px;
      width: 480px;
      max-width: 100%;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .setup-logo {
      text-align: center;
      margin-bottom: 28px;
    }

    .setup-logo img { width: 48px; height: auto; }
    .setup-logo h1 { font-size: 20px; color: #2D3B45; margin-top: 8px; }
    .setup-logo p { font-size: 13px; color: #8B969E; margin-top: 4px; }

    .setup-section {
      margin-bottom: 24px;
      padding-bottom: 24px;
      border-bottom: 1px solid #F0F0F0;
    }

    .setup-section:last-of-type { border-bottom: none; margin-bottom: 16px; }

    .setup-section h2 {
      font-size: 14px;
      color: #2D3B45;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .setup-section h2 i { color: #0374B5; font-size: 14px; }

    .badge-required {
      font-size: 9px;
      background: #E63946;
      color: #FFF;
      padding: 2px 6px;
      border-radius: 10px;
      font-weight: 700;
    }

    .badge-optional {
      font-size: 9px;
      background: #E0E0E0;
      color: #757575;
      padding: 2px 6px;
      border-radius: 10px;
      font-weight: 700;
    }

    .form-group { margin-bottom: 12px; }

    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #6B7780;
      margin-bottom: 4px;
    }

    .form-group input {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid #E0E0E0;
      border-radius: 6px;
      font-size: 13px;
      color: #2D3B45;
    }

    .form-group input:focus {
      outline: none;
      border-color: #0374B5;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .form-hint {
      font-size: 11px;
      color: #8B969E;
      margin-top: 3px;
    }

    .setup-btn {
      width: 100%;
      padding: 11px;
      background: #0374B5;
      color: #FFF;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.15s;
    }

    .setup-btn:hover { background: #0587D4; }

    .error-msg {
      background: #FFEBEE;
      color: #C62828;
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 13px;
      margin-bottom: 16px;
    }

    @media (max-width: 520px) {
      .setup-card { padding: 24px; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="setup-card">
    <div class="setup-logo">
      <img src="/env/img/canvas-lms.png" alt="Canvas LMS" />
      <h1>Canvas Themes</h1>
      <p>Configuración inicial del ambiente</p>
    </div>

    <?php if ($error): ?>
      <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">

      <div class="setup-section">
        <h2><i class="fas fa-user-shield"></i> Administrador <span class="badge-required">Obligatorio</span></h2>
        <div class="form-group">
          <label for="admin_user">Usuario</label>
          <input type="text" id="admin_user" name="admin_user" placeholder="ej: admin" value="<?= htmlspecialchars($_POST['admin_user'] ?? 'admin') ?>" required>
          <p class="form-hint">Solo letras, números y guion bajo</p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="admin_password">Contraseña</label>
            <input type="password" id="admin_password" name="admin_password" placeholder="Mínimo 6 caracteres" required>
          </div>
          <div class="form-group">
            <label for="admin_confirm">Confirmar</label>
            <input type="password" id="admin_confirm" name="admin_confirm" placeholder="Repetir contraseña" required>
          </div>
        </div>
      </div>

      <div class="setup-section">
        <h2><i class="fas fa-user"></i> Invitado <span class="badge-optional">Opcional</span></h2>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="create_guest" value="1" style="width:16px;height:16px;">
          <span style="font-size:13px;color:#2D3B45;">Crear usuario invitado con acceso de solo lectura</span>
        </label>
        <p class="form-hint" style="margin-top:6px;">Usuario: <code>invitado</code> / Contraseña: <code>invitado2026</code>. Puedes cambiarlos después en Admin → Usuarios.</p>
      </div>

      <div class="setup-section">
        <h2><i class="fas fa-envelope"></i> Recuperación <span class="badge-optional">Opcional</span></h2>
        <div class="form-group">
          <label for="recovery_email">Correo electrónico</label>
          <input type="email" id="recovery_email" name="recovery_email" placeholder="tu@correo.com" value="<?= htmlspecialchars($_POST['recovery_email'] ?? '') ?>">
        </div>
        <p class="form-hint">Se usa para resetear contraseñas si las olvidas.</p>
      </div>

      <button type="submit" class="setup-btn"><i class="fas fa-rocket"></i> Iniciar Canvas Themes</button>

    </form>
  </div>
</body>
</html>
