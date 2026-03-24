<?php require_once __DIR__ . '/auth.php'; requireLogin(); setSecurityHeaders(); ?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Dev Environment</title>
  <link rel="stylesheet" href="/env/css/canvas-env.css">
  <link rel="stylesheet" href="/env/css/dark-theme.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <script>window.CSRF_TOKEN = '<?= generateCsrfToken() ?>'; window.USER_ROLE = '<?= currentRole() ?>';</script>

  <!-- ==================== CANVAS ENVIRONMENT ==================== -->
  <div id="canvas-app" class="view-home">

    <!-- COL 1: Barra de menú Canvas -->
    <nav id="global-nav" class="nav-open">
      <div class="nav-logo">
        <img src="/env/img/canvas-icon.svg" alt="Canvas">
      </div>
      <ul class="nav-items">
        <li class="nav-item active">
          <a href="#"><i class="fas fa-user"></i><span class="nav-label">Cuenta</span></a>
        </li>
        <li class="nav-item">
          <a href="#"><i class="fas fa-tachometer-alt"></i><span class="nav-label">Tablero</span></a>
        </li>
        <li class="nav-item">
          <a href="#"><i class="fas fa-book"></i><span class="nav-label">Cursos</span></a>
        </li>
        <li class="nav-item">
          <a href="#"><i class="fas fa-calendar"></i><span class="nav-label">Calendario</span></a>
        </li>
        <li class="nav-item">
          <a href="#"><i class="fas fa-inbox"></i><span class="nav-label">Bandeja</span></a>
        </li>
      </ul>
      <div class="nav-bottom-tools">
        <div class="nav-tools-group">
          <button id="btn-reload" class="nav-tool-btn" title="Recargar contenido (Ctrl+R)">
            <i class="fas fa-sync-alt"></i><span class="nav-label">Recargar</span>
          </button>
          <?php if (isAdmin()): ?>
          <button id="btn-compile" class="nav-tool-btn" title="Compilar CSS">
            <i class="fas fa-cogs"></i><span class="nav-label">Compilar</span>
          </button>
          <?php endif; ?>
          <button id="btn-mobile" class="nav-tool-btn" title="Vista móvil">
            <i class="fas fa-mobile-alt"></i><span class="nav-label">Móvil</span>
          </button>
          <button id="btn-editor" class="nav-tool-btn" title="Ver código (Ctrl+E)">
            <i class="fas fa-code"></i><span class="nav-label">Código</span>
          </button>
        </div>
        <div class="nav-tools-separator"></div>
        <div class="nav-tools-group">
          <?php if (isAdmin()): ?>
          <a href="/admin" class="nav-tool-btn" title="Administración">
            <i class="fas fa-cog"></i><span class="nav-label">Admin</span>
          </a>
          <?php endif; ?>
          <a href="/env/logout.php" class="nav-tool-btn" title="<?= htmlspecialchars($_SESSION['user']) ?>">
            <i class="fas fa-sign-out-alt"></i><span class="nav-label">Salir</span>
          </a>
        </div>
      </div>
      <button id="btn-toggle-nav" class="nav-toggle" title="Abrir/Cerrar menú">
        <i class="fas fa-chevron-left"></i>
      </button>
    </nav>

    <!-- Hamburger flotante para reabrir menú del curso -->
    <button id="btn-open-course" class="course-open-btn hidden" title="Abrir menú del curso">
      <i class="fas fa-bars"></i>
    </button>

    <!-- COL 2: Barra del curso (dinámica) -->
    <aside id="left-side" class="course-nav-open">
      <div class="course-nav-header">
        <span class="course-name" id="course-name-label">Selecciona un proyecto</span>
        <button id="btn-toggle-course" class="course-toggle" title="Cerrar menú del curso">
          <i class="fas fa-bars"></i>
        </button>
      </div>

      <!-- Vista: lista de proyectos -->
      <div id="projects-list-view">
        <div class="course-nav-section-title">Proyectos disponibles</div>
        <ul id="projects-list" class="course-items">
          <!-- Se llena dinámicamente -->
        </ul>
      </div>

      <!-- Vista: páginas del proyecto seleccionado -->
      <div id="project-pages-view" class="hidden">
        <button id="btn-back-projects" class="course-back-btn">
          <i class="fas fa-arrow-left"></i> Proyectos
        </button>
        <ul id="project-pages" class="course-items">
          <!-- Se llena dinámicamente -->
        </ul>
      </div>
    </aside>

    <!-- COL 3: Contenido principal -->
    <main id="content-area">
      <div class="content-breadcrumb">
        <a href="#" id="breadcrumb-project">Canvas Themes</a>
        <i class="fas fa-chevron-right"></i>
        <span id="breadcrumb-page">Inicio</span>
      </div>
      <div id="content-body">
        <div class="placeholder-content">
          <h2>Canvas Themes – Dev Environment</h2>
          <p>Selecciona un proyecto desde el menú <strong>Proyectos</strong> en la barra lateral para comenzar.</p>
        </div>
      </div>
    </main>

    <!-- COL 4: Estado del curso (solo en Inicio) -->
    <aside id="course-status">
      <div class="status-section">
        <h3>Estado del Curso</h3>
        <div class="status-item">
          <i class="fas fa-chart-line"></i>
          <div>
            <strong>Calificación actual</strong>
            <span>Sin calificar</span>
          </div>
        </div>
      </div>
      <div class="status-section">
        <h3>Por hacer</h3>
        <ul class="todo-list">
          <li>
            <i class="fas fa-edit"></i>
            <div>
              <a href="#">Tarea 1 – Introducción</a>
              <small>Vence: 25 mar 2026</small>
            </div>
          </li>
          <li>
            <i class="fas fa-comments"></i>
            <div>
              <a href="#">Foro – Presentación</a>
              <small>Vence: 27 mar 2026</small>
            </div>
          </li>
          <li>
            <i class="fas fa-question-circle"></i>
            <div>
              <a href="#">Quiz – Diagnóstico</a>
              <small>Vence: 30 mar 2026</small>
            </div>
          </li>
        </ul>
      </div>
      <div class="status-section">
        <h3>Próximos eventos</h3>
        <ul class="event-list">
          <li>
            <i class="fas fa-video"></i>
            <div>
              <a href="#">Clase en vivo</a>
              <small>22 mar 2026 – 10:00 AM</small>
            </div>
          </li>
        </ul>
      </div>
    </aside>

  </div>

  <!-- ==================== MOBILE FRAME ==================== -->
  <div id="mobile-frame" class="hidden">
    <div class="mobile-toolbar">
      <div class="mobile-toolbar-group">
        <button class="mobile-device-btn active" data-device="phone" title="Teléfono">
          <i class="fas fa-mobile-alt"></i>
        </button>
        <button class="mobile-device-btn" data-device="tablet" title="Tablet">
          <i class="fas fa-tablet-alt"></i>
        </button>
      </div>
      <div class="mobile-toolbar-separator"></div>
      <button id="btn-toggle-orientation" class="mobile-orient-btn" title="Cambiar orientación">
        <i class="fas fa-mobile-alt" id="orient-icon"></i>
      </button>
      <div class="mobile-toolbar-separator"></div>
      <span id="mobile-size-label" class="mobile-size-label">375 x 812</span>
      <div class="mobile-toolbar-separator"></div>
      <button id="btn-mobile-reload" class="mobile-orient-btn" title="Recargar contenido">
        <i class="fas fa-sync-alt"></i>
      </button>
      <div class="mobile-toolbar-separator"></div>
      <button id="btn-dark" class="mobile-orient-btn" title="Modo oscuro">
        <i class="fas fa-moon"></i>
      </button>
      <div class="mobile-toolbar-separator"></div>
      <button id="btn-exit-mobile" class="mobile-orient-btn exit" title="Salir">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div id="mobile-device" class="portrait phone">
      <div class="mobile-status-bar">
        <span>9:41</span>
        <span><i class="fas fa-signal"></i> <i class="fas fa-wifi"></i> <i class="fas fa-battery-full"></i></span>
      </div>
      <iframe id="mobile-content-area" frameborder="0" allow="autoplay" style="flex:1;width:100%;border:none;"></iframe>
      <nav class="mobile-bottom-nav">
        <a href="#" class="mobile-tab active"><i class="fas fa-tachometer-alt"></i><span>Tablero</span></a>
        <a href="#" class="mobile-tab"><i class="fas fa-book"></i><span>Cursos</span></a>
        <a href="#" class="mobile-tab"><i class="fas fa-calendar"></i><span>Calendario</span></a>
        <a href="#" class="mobile-tab"><i class="fas fa-inbox"></i><span>Bandeja</span></a>
        <a href="#" class="mobile-tab"><i class="fas fa-bell"></i><span>Alertas</span></a>
      </nav>
    </div>
  </div>

  <!-- ==================== CODE VIEWER MODAL ==================== -->
  <div id="code-viewer-overlay" class="hidden">
    <div id="code-viewer-panel">
      <div class="viewer-header">
        <h3><i class="fas fa-code"></i> Código del Proyecto</h3>
        <button id="btn-close-viewer" class="viewer-close-btn" title="Cerrar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="viewer-tabs">
        <button class="viewer-tab-btn active" data-tab="html">HTML</button>
        <button class="viewer-tab-btn" data-tab="cssmaster">CSS Master</button>
        <button class="viewer-tab-btn" data-tab="css">CSS Mobile</button>
        <button class="viewer-tab-btn" data-tab="cssdesktop">CSS Desktop</button>
        <button class="viewer-tab-btn" data-tab="js">JS</button>
      </div>
      <div class="viewer-body">
        <div class="viewer-pane active" id="viewer-html">
          <div class="viewer-copy-bar">
            <span class="viewer-filename" id="viewer-html-filename">index.html</span>
            <button class="copy-btn" data-target="viewer-html-code"><i class="fas fa-copy"></i> Copiar</button>
          </div>
          <pre><code id="viewer-html-code"></code></pre>
        </div>
        <div class="viewer-pane" id="viewer-cssmaster">
          <div class="viewer-copy-bar">
            <span class="viewer-filename" id="viewer-cssmaster-filename">css/*-master.css</span>
            <button class="copy-btn" data-target="viewer-cssmaster-code"><i class="fas fa-copy"></i> Copiar</button>
          </div>
          <pre><code id="viewer-cssmaster-code"></code></pre>
        </div>
        <div class="viewer-pane" id="viewer-css">
          <div class="viewer-copy-bar">
            <span class="viewer-filename" id="viewer-css-filename">css/*-mobile.css</span>
            <button class="copy-btn" data-target="viewer-css-code"><i class="fas fa-copy"></i> Copiar</button>
          </div>
          <pre><code id="viewer-css-code"></code></pre>
        </div>
        <div class="viewer-pane" id="viewer-cssdesktop">
          <div class="viewer-copy-bar">
            <span class="viewer-filename" id="viewer-cssdesktop-filename">css/*-desktop.css</span>
            <button class="copy-btn" data-target="viewer-cssdesktop-code"><i class="fas fa-copy"></i> Copiar</button>
          </div>
          <pre><code id="viewer-cssdesktop-code"></code></pre>
        </div>
        <div class="viewer-pane" id="viewer-js">
          <div class="viewer-copy-bar">
            <span class="viewer-filename">js/scripts.js</span>
            <button class="copy-btn" data-target="viewer-js-code"><i class="fas fa-copy"></i> Copiar</button>
          </div>
          <pre><code id="viewer-js-code"></code></pre>
        </div>
      </div>
    </div>
  </div>

  <script src="/env/js/canvas-env.js"></script>
</body>
</html>
