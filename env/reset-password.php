<?php
/**
 * RECUPERACIÓN DE CONTRASEÑAS
 *
 * Opciones:
 * 1. Ingresar el correo de recuperación → resetea contraseñas del admin
 * 2. Resetear todo → elimina la configuración y redirige al setup inicial
 */
require_once __DIR__ . '/auth.php';
setSecurityHeaders();

if (!isConfigured()) {
    header('Location: setup.php');
    exit;
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

$message = '';
$success = false;
$config = loadConfig();
$hasEmail = !empty($config['recovery_email']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'reset_by_email') {
        $email = strtolower(trim($_POST['recovery_email'] ?? ''));

        if (!$hasEmail) {
            $message = 'No hay correo de recuperación configurado.';
        } elseif ($email === strtolower($config['recovery_email'])) {
            // Resetear contraseña del admin al valor por defecto
            $adminUser = null;
            foreach ($config['users'] as $user => $data) {
                if ($data['role'] === 'admin') {
                    $adminUser = $user;
                    break;
                }
            }
            if ($adminUser) {
                $config['users'][$adminUser]['password'] = password_hash('admin2026', PASSWORD_DEFAULT);
                saveConfig($config);
                $success = true;
                $message = 'Contraseña del admin reseteada. Usuario: ' . $adminUser . ' / Contraseña: admin2026';
            }
        } else {
            $message = 'El correo no coincide con el registrado.';
        }
    } elseif ($action === 'reset_users') {
        // Eliminar solo usuarios → vuelve al setup
        if (file_exists(CONFIG_PATH)) {
            unlink(CONFIG_PATH);
        }
        logout();
        header('Location: setup.php');
        exit;
    } elseif ($action === 'reset_factory') {
        // Eliminar usuarios + todos los proyectos
        if (file_exists(CONFIG_PATH)) {
            unlink(CONFIG_PATH);
        }
        $projectsDir = realpath(__DIR__ . '/../projects');
        if ($projectsDir && is_dir($projectsDir)) {
            $dirs = array_filter(scandir($projectsDir), function ($d) use ($projectsDir) {
                return $d !== '.' && $d !== '..' && is_dir($projectsDir . '/' . $d);
            });
            foreach ($dirs as $dir) {
                deleteDirectory($projectsDir . '/' . $dir);
            }
        }
        // Crear demo si se solicitó
        $createDemo = isset($_POST['create_demo']);
        if ($createDemo) {
            $templatesDir = realpath(__DIR__ . '/templates');
            $demoPath = dirname($projectsDir ?: __DIR__ . '/../projects') . '/projects/proyecto-demo';
            if (!$projectsDir) {
                $demoPath = realpath(__DIR__ . '/..') . '/projects/proyecto-demo';
            }
            $dirs = [$demoPath, $demoPath . '/pages', $demoPath . '/css', $demoPath . '/js'];
            foreach ($dirs as $d) {
                if (!is_dir($d)) mkdir($d, 0755, true);
            }
            // Generar desde plantillas
            $tplVars = ['IMPORTS' => '', 'PROJECT_NAME' => 'Proyecto Demo'];
            $tplFiles = [
                'index.html' => $demoPath . '/index.html',
                'css/master.css' => $demoPath . '/css/proyecto-demo-master.css',
                'js/scripts.js' => $demoPath . '/js/scripts.js',
            ];
            foreach ($tplFiles as $tpl => $dest) {
                $content = file_get_contents($templatesDir . '/' . $tpl);
                foreach ($tplVars as $k => $v) {
                    $content = str_replace('{{' . $k . '}}', $v, $content);
                }
                file_put_contents($dest, $content);
            }
            // Semanas de organización
            $orgTpl = file_get_contents($templatesDir . '/pages/organization.html');
            for ($i = 1; $i <= 5; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $content = str_replace('{{ORG_LABEL}}', 'Semana ' . $num, $orgTpl);
                file_put_contents($demoPath . '/pages/semana-' . $num . '.html', $content);
            }
            // Actividades
            $actTpls = ['tarea', 'quiz', 'foros'];
            foreach ($actTpls as $act) {
                $content = file_get_contents($templatesDir . '/pages/' . $act . '.html');
                file_put_contents($demoPath . '/pages/' . $act . '.html', $content);
            }
            // Compilar
            include __DIR__ . '/api/compile-css-fn.php';
            compileCssFromMaster($demoPath, 'proyecto-demo');
        }
        logout();
        header('Location: setup.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Recuperar Acceso</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #1A1A2E;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .card {
      background: #FFF;
      border-radius: 12px;
      padding: 40px;
      width: 420px;
      max-width: 100%;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    h1 { font-size: 18px; color: #2D3B45; margin-bottom: 8px; }
    .subtitle { font-size: 13px; color: #8B969E; margin-bottom: 24px; }
    label { display: block; font-size: 12px; font-weight: 600; color: #6B7780; margin-bottom: 4px; }
    input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #E0E0E0;
      border-radius: 6px;
      font-size: 14px;
      margin-bottom: 16px;
    }
    input:focus { outline: none; border-color: #0374B5; }
    .btn {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-bottom: 8px;
    }
    .btn-primary { background: #0374B5; color: #FFF; }
    .btn-primary:hover { background: #0587D4; }
    .btn-danger { background: #E63946; color: #FFF; }
    .btn-danger:hover { background: #C62828; }
    .msg {
      padding: 10px;
      border-radius: 6px;
      font-size: 13px;
      margin-bottom: 16px;
      text-align: center;
    }
    .msg.ok { background: #E8F5E9; color: #2E7D32; }
    .msg.err { background: #FFEBEE; color: #C62828; }
    .separator {
      text-align: center;
      color: #BDBDBD;
      font-size: 12px;
      margin: 20px 0;
      position: relative;
    }
    .separator::before, .separator::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 40%;
      height: 1px;
      background: #E0E0E0;
    }
    .separator::before { left: 0; }
    .separator::after { right: 0; }
    .danger-box {
      background: #FFF5F5;
      border: 1px solid #FFCDD2;
      border-radius: 8px;
      padding: 16px;
    }
    .danger-box h3 {
      font-size: 13px;
      color: #C62828;
      margin-bottom: 8px;
    }
    .danger-box p {
      font-size: 12px;
      color: #757575;
      margin-bottom: 12px;
      line-height: 1.5;
    }
    .back { display: block; text-align: center; margin-top: 16px; color: #0374B5; font-size: 13px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Recuperar Acceso</h1>

    <?php if ($message): ?>
      <div class="msg <?= $success ? 'ok' : 'err' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <a href="/env/login.php" class="back">Ir al login</a>
    <?php else: ?>

      <?php if ($hasEmail): ?>
        <p class="subtitle">Ingresa tu correo de recuperación para resetear la contraseña del admin.</p>
        <form method="POST">
          <input type="hidden" name="action" value="reset_by_email">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="recovery_email" placeholder="tu@correo.com" required autofocus>
          <button type="submit" class="btn btn-primary">Resetear contraseña del admin</button>
        </form>
      <?php else: ?>
        <p class="subtitle">No hay correo de recuperación configurado.</p>
      <?php endif; ?>

      <div class="separator">o</div>

      <div class="danger-box">
        <h3><i class="fas fa-users"></i> Reiniciar usuarios</h3>
        <p>Elimina los usuarios y vuelve al asistente inicial. <strong>Los proyectos se conservan.</strong></p>
        <form method="POST" onsubmit="return confirm('Se eliminarán los usuarios configurados. Los proyectos no se tocan. ¿Continuar?');">
          <input type="hidden" name="action" value="reset_users">
          <button type="submit" class="btn btn-danger">Reiniciar usuarios</button>
        </form>
      </div>

      <div class="danger-box" style="margin-top: 12px;">
        <h3><i class="fas fa-bomb"></i> Reinicio de fábrica</h3>
        <p>Elimina usuarios <strong>y todos los proyectos</strong>. Ideal para transferir el ambiente limpio a otra persona.</p>
        <form method="POST" onsubmit="return confirm('ATENCIÓN: Se eliminarán los usuarios Y todos los proyectos. Esta acción NO se puede deshacer. ¿Continuar?');">
          <input type="hidden" name="action" value="reset_factory">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:12px;font-size:12px;color:#757575;">
            <input type="checkbox" name="create_demo" value="1" style="width:15px;height:15px;" checked>
            Crear proyecto demo de referencia
          </label>
          <button type="submit" class="btn btn-danger">Reinicio de fábrica</button>
        </form>
      </div>

      <a href="/env/login.php" class="back">Volver al login</a>
    <?php endif; ?>
  </div>
</body>
</html>
