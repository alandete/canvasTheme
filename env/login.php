<?php
require_once __DIR__ . '/auth.php';
setSecurityHeaders();

if (!isConfigured()) {
    header('Location: setup.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header('Location: index.php');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos';
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Iniciar Sesión</title>
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
    }

    .login-card {
      background: #FFF;
      border-radius: 12px;
      padding: 40px;
      width: 360px;
      max-width: 90%;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .login-logo {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-logo i {
      font-size: 36px;
      color: #0374B5;
    }

    .login-logo h1 {
      font-size: 18px;
      color: #2D3B45;
      margin-top: 8px;
    }

    .login-logo small {
      font-size: 12px;
      color: #8B969E;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #6B7780;
      margin-bottom: 4px;
    }

    .form-group input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #E0E0E0;
      border-radius: 6px;
      font-size: 14px;
      color: #2D3B45;
      transition: border-color 0.15s;
    }

    .form-group input:focus {
      outline: none;
      border-color: #0374B5;
    }

    .login-btn {
      width: 100%;
      padding: 10px;
      background: #0374B5;
      color: #FFF;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.15s;
    }

    .login-btn:hover {
      background: #0587D4;
    }

    .error-msg {
      background: #FFEBEE;
      color: #C62828;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 13px;
      margin-bottom: 16px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo">
      <i class="fas fa-palette"></i>
      <h1>Canvas Themes</h1>
      <small>Ambiente de desarrollo</small>
    </div>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Usuario</label>
        <input type="text" id="username" name="username" autocomplete="username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="login-btn">Ingresar</button>
    </form>
    <a href="/admin/reset-password" style="display:block;text-align:center;margin-top:16px;font-size:12px;color:#8B969E;">¿Olvidaste tu contraseña?</a>
  </div>
</body>
</html>
