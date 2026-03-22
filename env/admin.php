<?php require_once __DIR__ . '/auth.php'; requireAdmin(); setSecurityHeaders(); ?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Administración</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>

  <header class="admin-header">
    <div class="admin-header-left">
      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
      <h1><i class="fas fa-cog"></i> Administración</h1>
    </div>
    <div class="admin-tabs">
      <button class="admin-tab active" data-tab="projects"><i class="fas fa-folder-open"></i> Proyectos</button>
      <button class="admin-tab" data-tab="users"><i class="fas fa-users"></i> Usuarios</button>
      <a href="docs.php" class="admin-tab"><i class="fas fa-book-open"></i> Docs</a>
    </div>
  </header>

  <main class="admin-main">

    <!-- ══════════════════════════════════════════
         PESTAÑA: PROYECTOS
         ══════════════════════════════════════════ -->
    <div class="admin-panel active" id="panel-projects">

      <div class="panel-header">
        <h2><i class="fas fa-folder-open"></i> Proyectos</h2>
        <button id="btn-new-project" class="btn-primary">
          <i class="fas fa-plus"></i> Nuevo Proyecto
        </button>
      </div>

      <!-- Formulario nuevo proyecto -->
      <div id="new-project-form" class="card form-card hidden">
        <h2>Crear Proyecto</h2>
        <div class="form-group">
          <label for="project-name">Nombre del proyecto</label>
          <input type="text" id="project-name" placeholder="Ej: Mi Curso de Historia" autocomplete="off">
          <small class="form-hint">Carpeta: <code id="slug-preview">---</code></small>
        </div>

        <div class="form-group">
          <label>Organización del contenido</label>
          <div class="form-row">
            <select id="org-type" class="form-select">
              <option value="none">Sin organización</option>
              <option value="semanas">Semanas</option>
              <option value="modulos">Módulos</option>
              <option value="unidades">Unidades</option>
            </select>
            <input type="number" id="org-count" class="form-input-sm" min="1" max="30" value="4" disabled placeholder="Cantidad">
          </div>
        </div>

        <div class="form-group">
          <label>Colores del proyecto</label>
          <div class="color-inputs">
            <div class="color-field">
              <input type="color" id="color-primary-picker" value="#0374B5">
              <input type="text" id="color-primary" class="color-hex" value="#0374B5" maxlength="7" placeholder="#000000">
              <span>Primario</span>
            </div>
            <div class="color-field">
              <input type="color" id="color-secondary-picker" value="#2D3B45">
              <input type="text" id="color-secondary" class="color-hex" value="#2D3B45" maxlength="7" placeholder="#000000">
              <span>Secundario</span>
            </div>
            <div class="color-field">
              <input type="color" id="color-accent1-picker" value="#E63946">
              <input type="text" id="color-accent1" class="color-hex" value="#E63946" maxlength="7" placeholder="#000000">
              <span>Acento 1</span>
            </div>
            <div class="color-field">
              <input type="color" id="color-accent2-picker" value="#457B9D">
              <input type="text" id="color-accent2" class="color-hex" value="#457B9D" maxlength="7" placeholder="#000000">
              <span>Acento 2</span>
            </div>
            <div class="color-field">
              <input type="color" id="color-accent3-picker" value="#2B9348">
              <input type="text" id="color-accent3" class="color-hex" value="#2B9348" maxlength="7" placeholder="#000000">
              <span>Acento 3</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Librerías externas (@import en CSS)</label>
          <div class="cdn-options">
            <label class="cdn-check">
              <input type="checkbox" id="cdn-bootstrap" value="bootstrap">
              <span class="cdn-check-label">
                <i class="fas fa-cube"></i> Bootstrap 5 CSS
                <small>Grid, utilidades, componentes</small>
              </span>
            </label>
            <label class="cdn-check">
              <input type="checkbox" id="cdn-bootstrap-icons" value="bootstrap-icons">
              <span class="cdn-check-label">
                <i class="fas fa-icons"></i> Bootstrap Icons
                <small>2,000+ íconos SVG</small>
              </span>
            </label>
            <label class="cdn-check">
              <input type="checkbox" id="cdn-fontawesome" value="fontawesome">
              <span class="cdn-check-label">
                <i class="fas fa-font-awesome"></i> Font Awesome 6
                <small>Íconos vectoriales</small>
              </span>
            </label>
            <label class="cdn-check">
              <input type="checkbox" id="cdn-animate" value="animate">
              <span class="cdn-check-label">
                <i class="fas fa-magic"></i> Animate.css
                <small>Animaciones CSS predefinidas</small>
              </span>
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button id="btn-create" class="btn-primary"><i class="fas fa-check"></i> Crear</button>
          <button id="btn-cancel" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</button>
        </div>
        <div id="form-message" class="form-message hidden"></div>
      </div>

      <!-- Lista de proyectos -->
      <div id="projects-grid" class="projects-grid"></div>

      <div id="empty-state" class="empty-state hidden">
        <i class="fas fa-folder-plus"></i>
        <p>No hay proyectos creados.<br>Haz clic en <strong>Nuevo Proyecto</strong> para comenzar.</p>
      </div>

    </div>

    <!-- ══════════════════════════════════════════
         PESTAÑA: USUARIOS
         ══════════════════════════════════════════ -->
    <div class="admin-panel" id="panel-users">

      <div class="panel-header">
        <h2><i class="fas fa-users"></i> Usuarios</h2>
        <?php
          $config = loadConfig();
          $users = $config['users'] ?? [];
          $hasGuest = false;
          foreach ($users as $u) { if ($u['role'] === 'guest') { $hasGuest = true; break; } }
        ?>
        <?php if (!$hasGuest): ?>
        <button id="btn-add-guest" class="btn-primary"><i class="fas fa-user-plus"></i> Agregar Invitado</button>
        <?php endif; ?>
      </div>

      <!-- Formulario nuevo invitado -->
      <div id="guest-form" class="card form-card hidden" style="margin-bottom: 24px;">
        <h2>Crear Usuario Invitado</h2>
        <div class="form-group">
          <label for="guest-username">Usuario</label>
          <input type="text" id="guest-username" placeholder="ej: invitado" autocomplete="off">
          <small class="form-hint">Solo letras, números y guion bajo</small>
        </div>
        <div class="form-group">
          <label for="guest-password">Contraseña</label>
          <input type="password" id="guest-password" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
        </div>
        <div class="form-actions">
          <button id="btn-create-guest" class="btn-primary"><i class="fas fa-check"></i> Crear</button>
          <button id="btn-cancel-guest" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</button>
        </div>
        <div id="guest-message" class="form-message hidden"></div>
      </div>

      <?php
        $recoveryEmail = $config['recovery_email'] ?? '';
        $permLabels = [
            'admin' => 'Crear, editar, eliminar, compilar, ver código',
            'guest' => 'Ver proyectos, ver código, documentación'
        ];
      ?>

      <div class="users-grid">
        <?php foreach ($users as $username => $data): ?>
        <div class="user-card">
          <div class="user-card-header">
            <div class="user-avatar <?= $data['role'] ?>">
              <i class="fas <?= $data['role'] === 'admin' ? 'fa-user-shield' : 'fa-user' ?>"></i>
            </div>
            <div>
              <h3><?= $data['role'] === 'admin' ? 'Administrador' : 'Invitado' ?></h3>
              <span class="user-role-badge <?= $data['role'] ?>"><?= $data['role'] === 'admin' ? 'Admin' : 'Solo lectura' ?></span>
            </div>
          </div>
          <div class="user-card-info">
            <div class="user-info-row">
              <span class="user-info-label">Usuario</span>
              <code><?= htmlspecialchars($username) ?></code>
            </div>
            <div class="user-info-row">
              <span class="user-info-label">Permisos</span>
              <span><?= $permLabels[$data['role']] ?? '' ?></span>
            </div>
          </div>
          <div class="user-card-pw">
            <label>Cambiar contraseña</label>
            <div class="pw-row">
              <input type="password" id="pw-<?= htmlspecialchars($username) ?>" placeholder="Nueva contraseña" autocomplete="new-password">
              <button class="btn-change-pw" data-user="<?= htmlspecialchars($username) ?>"><i class="fas fa-key"></i></button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div id="pw-message" class="form-message hidden" style="margin-top: 16px;"></div>

      <!-- Correo de recuperación -->
      <div class="card" style="margin-top: 24px;">
        <div class="recovery-section">
          <div class="recovery-info">
            <h3><i class="fas fa-envelope"></i> Correo de recuperación</h3>
            <p>Se usa para resetear contraseñas si las olvidas.</p>
          </div>
          <div class="recovery-form">
            <input type="email" id="recovery-email" value="<?= htmlspecialchars($recoveryEmail) ?>" placeholder="tu@correo.com">
            <button id="btn-save-email" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
          </div>
          <div id="email-message" class="form-message hidden" style="margin-top: 8px;"></div>
          <?php if (!$recoveryEmail): ?>
          <div class="form-message error" style="margin-top: 8px;">
            <i class="fas fa-exclamation-triangle"></i> Sin correo configurado. No podrás recuperar acceso si olvidas las contraseñas.
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Link de recuperación -->
      <div class="recovery-link-info" style="margin-top: 16px;">
        <small><i class="fas fa-link"></i> URL de recuperación: <code>reset-password.php</code></small>
      </div>

    </div>

  </main>

  <!-- Modal editar proyecto -->
  <div id="edit-overlay" class="overlay hidden">
    <div class="modal modal-edit">
      <h3><i class="fas fa-edit"></i> Editar Proyecto</h3>
      <div class="form-group">
        <label for="edit-project-name">Nombre del proyecto</label>
        <input type="text" id="edit-project-name" autocomplete="off">
      </div>
      <div class="form-group">
        <label>Organización del contenido</label>
        <div class="form-row">
          <select id="edit-org-type" class="form-select">
            <option value="none">Sin organización</option>
            <option value="semanas">Semanas</option>
            <option value="modulos">Módulos</option>
            <option value="unidades">Unidades</option>
          </select>
          <input type="number" id="edit-org-count" class="form-input-sm" min="1" max="30" value="4" placeholder="Cantidad">
        </div>
        <small class="form-hint">Solo agrega nuevas páginas de organización. No elimina las existentes.</small>
      </div>
      <div id="edit-message" class="form-message hidden"></div>
      <div class="modal-actions">
        <button id="btn-save-edit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
        <button id="btn-cancel-edit" class="btn-secondary">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Modal confirmar eliminación -->
  <div id="delete-overlay" class="overlay hidden">
    <div class="modal">
      <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
      <h3>Eliminar proyecto</h3>
      <p>¿Estás seguro de eliminar <strong id="delete-project-name"></strong>? Esta acción no se puede deshacer.</p>
      <div class="modal-actions">
        <button id="btn-confirm-delete" class="btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
        <button id="btn-cancel-delete" class="btn-secondary">Cancelar</button>
      </div>
    </div>
  </div>

  <script>window.CSRF_TOKEN = '<?= generateCsrfToken() ?>';</script>
  <script src="js/admin.js"></script>
</body>
</html>
