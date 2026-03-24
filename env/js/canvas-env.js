/* ============================================
   Canvas Themes – Dev Environment
   Main JavaScript
   ============================================ */

(function () {
  'use strict';

  // ── DOM References ──
  const html = document.documentElement;
  const app = document.getElementById('canvas-app');

  // Global Nav
  const globalNav = document.getElementById('global-nav');
  const btnToggleNav = document.getElementById('btn-toggle-nav');
  // navProjects removed - projects load automatically in course nav

  // Course Nav
  const courseNav = document.getElementById('left-side');
  const btnToggleCourse = document.getElementById('btn-toggle-course');
  const btnOpenCourse = document.getElementById('btn-open-course');
  const courseNameLabel = document.getElementById('course-name-label');

  // Course Nav views
  const projectsListView = document.getElementById('projects-list-view');
  const projectsList = document.getElementById('projects-list');
  const projectPagesView = document.getElementById('project-pages-view');
  const projectPages = document.getElementById('project-pages');
  const btnBackProjects = document.getElementById('btn-back-projects');

  // Content
  const contentBody = document.getElementById('content-body');
  const breadcrumbProject = document.getElementById('breadcrumb-project');
  const breadcrumbPage = document.getElementById('breadcrumb-page');

  // Nav tools
  const btnReload = document.getElementById('btn-reload');
  const btnCompile = document.getElementById('btn-compile');
  const btnDark = document.getElementById('btn-dark');
  const btnMobile = document.getElementById('btn-mobile');
  const btnEditor = document.getElementById('btn-editor');

  // Mobile frame
  const mobileFrame = document.getElementById('mobile-frame');
  const mobileDevice = document.getElementById('mobile-device');
  const mobileContentArea = document.getElementById('mobile-content-area');
  const btnToggleOrientation = document.getElementById('btn-toggle-orientation');
  const orientIcon = document.getElementById('orient-icon');
  const mobileSizeLabel = document.getElementById('mobile-size-label');
  const deviceBtns = document.querySelectorAll('.mobile-device-btn');
  const btnExitMobile = document.getElementById('btn-exit-mobile');

  // Code viewer
  const codeViewerOverlay = document.getElementById('code-viewer-overlay');
  const btnCloseViewer = document.getElementById('btn-close-viewer');
  const viewerTabBtns = document.querySelectorAll('.viewer-tab-btn');
  const viewerPanes = document.querySelectorAll('.viewer-pane');
  const viewerHtmlFilename = document.getElementById('viewer-html-filename');

  // ── State ──
  let isDark = false;
  let currentProject = null;
  let currentPageSlug = null;
  let currentPageName = null;
  let projectStyleEl = null;
  let projectDesktopStyleEl = null;
  let projectScriptEl = null;
  let projectsData = [];

  // Mobile state
  var mobileCurrentDevice = 'phone';
  var mobileIsPortrait = true;
  var deviceSizes = {
    phone:  { pw: 375, ph: 812 },
    tablet: { pw: 768, ph: 1024 }
  };

  // ── State Management (clean URLs) ──
  function updateState() {
    if (currentProject && currentPageSlug) {
      var url = '/project/' + encodeURIComponent(currentProject.slug);
      if (currentPageSlug !== 'index') {
        url += '#' + encodeURIComponent(currentPageSlug);
      }
      history.replaceState(null, '', url);
    }
  }

  function readState() {
    // 1. Clean URL: /project/slug
    var pathMatch = location.pathname.match(/^\/project\/([a-z0-9\-]+)$/);
    if (pathMatch) {
      var page = location.hash ? decodeURIComponent(location.hash.replace('#', '')) : 'index';
      return { project: decodeURIComponent(pathMatch[1]), page: page };
    }
    // 2. Query param: ?project=slug
    var qp = new URLSearchParams(location.search);
    if (qp.get('project')) {
      return { project: qp.get('project'), page: qp.get('page') || 'index' };
    }
    // 3. Legacy hash: #project=slug&page=index
    var hash = location.hash.replace('#', '');
    if (hash && hash.indexOf('project=') !== -1) {
      var params = {};
      hash.split('&').forEach(function (part) {
        var kv = part.split('=');
        if (kv.length === 2) params[kv[0]] = decodeURIComponent(kv[1]);
      });
      return params;
    }
    return null;
  }

  // ── Init: Load projects then restore state ──
  loadProjects().then(function () {
    var params = readState();
    if (params && params.project) {
      var proj = projectsData.find(function (p) { return p.slug === params.project; });
      if (proj) {
        selectProject(proj, params.page || 'index');
        return;
      }
    }
  });

  async function loadProjects() {
    try {
      const resp = await fetch('/env/api/projects.php');
      const data = await resp.json();
      projectsData = data.projects || [];
      renderProjectsList();
    } catch (e) {
      console.error('Error cargando proyectos:', e);
    }
  }

  function renderProjectsList() {
    projectsList.innerHTML = '';
    if (projectsData.length === 0) {
      projectsList.innerHTML = '<li class="course-item-empty">No hay proyectos. Crea uno desde Admin.</li>';
      return;
    }
    projectsData.forEach(function (proj) {
      var li = document.createElement('li');
      li.className = 'course-item';
      var a = document.createElement('a');
      a.href = '#';
      a.innerHTML = '<i class="fas fa-folder"></i> ' + proj.name;
      a.addEventListener('click', function (e) {
        e.preventDefault();
        selectProject(proj);
      });
      li.appendChild(a);
      projectsList.appendChild(li);
    });
  }

  function selectProject(proj, initialPage) {
    currentProject = proj;
    var pageSlug = initialPage || 'index';

    // Switch to pages view
    projectsListView.classList.add('hidden');
    projectPagesView.classList.remove('hidden');
    courseNameLabel.textContent = proj.name;

    // Render pages
    projectPages.innerHTML = '';
    proj.pages.forEach(function (page) {
      // Snippets solo en desktop
      if (page.slug === 'snippets' && mobileFrame.classList.contains('active')) return;

      var li = document.createElement('li');
      li.className = 'course-item';
      if (page.slug === 'snippets') li.className += ' snippets-only-desktop';
      if (page.slug === pageSlug) li.classList.add('active');
      li.dataset.page = page.slug;

      var a = document.createElement('a');
      a.href = '#';
      a.textContent = page.name;
      a.addEventListener('click', function (e) {
        e.preventDefault();
        loadPage(proj.slug, page.slug, page.name);
        projectPages.querySelectorAll('.course-item').forEach(function (item) {
          item.classList.remove('active');
        });
        li.classList.add('active');
      });
      li.appendChild(a);
      projectPages.appendChild(li);
    });

    // Find page name
    var pageData = proj.pages.find(function (p) { return p.slug === pageSlug; });
    var pageName = pageData ? pageData.name : pageSlug;

    loadPage(proj.slug, pageSlug, pageName);
  }

  // ── Back to projects list ──
  btnBackProjects.addEventListener('click', function () {
    projectPagesView.classList.add('hidden');
    projectsListView.classList.remove('hidden');
    courseNameLabel.textContent = 'Selecciona un proyecto';
    currentProject = null;
    currentPageSlug = null;
    currentPageName = null;

    unloadProjectAssets();

    contentBody.innerHTML =
      '<div class="placeholder-content">' +
      '<h2>Canvas Themes</h2>' +
      '<p>Selecciona un proyecto desde el menú <strong>Proyectos</strong> para comenzar.</p>' +
      '</div>';
    breadcrumbProject.textContent = 'Canvas Themes';
    breadcrumbPage.textContent = 'Inicio';
    switchView('home');
    history.replaceState(null, '', location.pathname);
  });

  // ── Load a project page ──
  async function loadPage(projectSlug, pageSlug, pageName) {
    try {
      var resp = await fetch('/env/api/content.php?project=' + encodeURIComponent(projectSlug) + '&page=' + encodeURIComponent(pageSlug));
      var data = await resp.json();

      if (data.error) {
        contentBody.innerHTML = '<div class="placeholder-content"><h2>Error</h2><p>' + data.error + '</p></div>';
        return;
      }

      // Inject HTML first
      contentBody.innerHTML = data.html;

      // Swap assets if project changed or first load
      var needsAssets = !currentPageSlug || projectStyleEl === null;
      if (needsAssets) {
        unloadProjectAssets();

        if (data.cssPath) {
          projectStyleEl = document.createElement('link');
          projectStyleEl.rel = 'stylesheet';
          projectStyleEl.id = 'project-style';
          projectStyleEl.href = data.cssPath + '?t=' + Date.now();
          document.head.appendChild(projectStyleEl);
        }

        if (data.cssDesktopPath) {
          projectDesktopStyleEl = document.createElement('link');
          projectDesktopStyleEl.rel = 'stylesheet';
          projectDesktopStyleEl.id = 'project-style-desktop';
          projectDesktopStyleEl.href = data.cssDesktopPath + '?t=' + Date.now();
          document.head.appendChild(projectDesktopStyleEl);
        }

        if (data.jsPath) {
          projectScriptEl = document.createElement('script');
          projectScriptEl.id = 'project-script';
          projectScriptEl.src = data.jsPath + '?t=' + Date.now();
          projectScriptEl.onload = function () {
            document.dispatchEvent(new Event('contentLoaded'));
          };
          document.body.appendChild(projectScriptEl);
        }
      } else {
        // Script ya cargado, solo reinicializar
        document.dispatchEvent(new Event('contentLoaded'));
      }

      // Update state
      currentPageSlug = pageSlug;
      currentPageName = pageName;

      // Update breadcrumb
      breadcrumbProject.textContent = currentProject ? currentProject.name : projectSlug;
      breadcrumbPage.textContent = pageName || pageSlug;

      // Toggle home/content
      var view = (pageSlug === 'index') ? 'home' : 'content';
      switchView(view);

      // Update hash
      updateState();

      // Sync mobile
      if (!mobileFrame.classList.contains('hidden')) {
        syncMobileContent();
      }

    } catch (e) {
      console.error('Error cargando página:', e);
      contentBody.innerHTML = '<div class="placeholder-content"><h2>Error</h2><p>No se pudo cargar la página.</p></div>';
    }
  }

  // ── Reload: recarga contenido y CSS sin perder el estado ──
  btnReload.addEventListener('click', reloadContent);

  function reloadContent() {
    if (!currentProject || !currentPageSlug) return;

    // Force reload CSS by removing and re-adding
    unloadProjectAssets();
    projectStyleEl = null;

    var icon = btnReload.querySelector('i');
    icon.classList.add('fa-spin');

    loadPage(currentProject.slug, currentPageSlug, currentPageName).then(function () {
      setTimeout(function () { icon.classList.remove('fa-spin'); }, 500);
    });
  }

  // ── Compile CSS ──
  btnCompile.addEventListener('click', function () {
    if (!currentProject) {
      alert('Selecciona un proyecto primero.');
      return;
    }
    compileCSS(currentProject.slug);
  });

  async function compileCSS(slug) {
    var icon = btnCompile.querySelector('i');
    var originalClass = icon.className;
    icon.className = 'fas fa-spinner fa-spin';
    btnCompile.disabled = true;

    try {
      var resp = await fetch('/env/api/compile-css.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
        body: JSON.stringify({ project: slug })
      });
      var data = await resp.json();

      if (data.success) {
        icon.className = 'fas fa-check';
        btnCompile.classList.add('compile-success');

        // Reload CSS after compile
        reloadContent();

        setTimeout(function () {
          icon.className = originalClass;
          btnCompile.classList.remove('compile-success');
          btnCompile.disabled = false;
        }, 2000);
      } else {
        alert(data.error || 'Error al compilar.');
        icon.className = originalClass;
        btnCompile.disabled = false;
      }
    } catch (e) {
      alert('Error de conexión.');
      icon.className = originalClass;
      btnCompile.disabled = false;
    }
  }

  function unloadProjectAssets() {
    if (projectStyleEl) {
      projectStyleEl.remove();
      projectStyleEl = null;
    }
    if (projectDesktopStyleEl) {
      projectDesktopStyleEl.remove();
      projectDesktopStyleEl = null;
    }
    if (projectScriptEl) {
      if (window.__canvasProjectCleanup) {
        try { window.__canvasProjectCleanup(); } catch (e) { /* ignore */ }
        window.__canvasProjectCleanup = null;
      }
      projectScriptEl.remove();
      projectScriptEl = null;
    }
  }

  function switchView(view) {
    app.classList.remove('view-home', 'view-content');
    app.classList.add('view-' + view);
  }

  // Projects list loads automatically on init via loadProjects()

  // ── Dark Mode (solo en vista móvil) ──
  if (btnDark) {
    btnDark.addEventListener('click', function () {
      isDark = !isDark;
      html.setAttribute('data-theme', isDark ? 'dark' : 'light');
      btnDark.classList.toggle('active', isDark);
      var icon = btnDark.querySelector('i');
      icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
      syncMobileContent();
    });
  }

  function resetDarkMode() {
    isDark = false;
    html.setAttribute('data-theme', 'light');
    if (btnDark) {
      btnDark.classList.remove('active');
      var icon = btnDark.querySelector('i');
      icon.className = 'fas fa-moon';
    }
  }

  // ── Mobile Reload ──
  var btnMobileReload = document.getElementById('btn-mobile-reload');
  if (btnMobileReload) {
    btnMobileReload.addEventListener('click', function () {
      syncMobileContent();
    });
  }

  // ── Mobile Mode ──
  btnMobile.addEventListener('click', function () {
    mobileFrame.classList.remove('hidden');
    btnMobile.classList.add('active');
    updateMobileDevice();
    syncMobileContent();
  });

  btnExitMobile.addEventListener('click', function () {
    mobileFrame.classList.add('hidden');
    btnMobile.classList.remove('active');
    resetDarkMode();
  });

  deviceBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      mobileCurrentDevice = btn.dataset.device;
      deviceBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      updateMobileDevice();
    });
  });

  btnToggleOrientation.addEventListener('click', function () {
    mobileIsPortrait = !mobileIsPortrait;
    updateMobileDevice();
  });

  function updateMobileDevice() {
    var orientation = mobileIsPortrait ? 'portrait' : 'landscape';
    mobileDevice.className = mobileCurrentDevice + ' ' + orientation;

    if (mobileCurrentDevice === 'phone') {
      orientIcon.className = mobileIsPortrait
        ? 'fas fa-mobile-alt'
        : 'fas fa-mobile-alt fa-rotate-90';
    } else {
      orientIcon.className = mobileIsPortrait
        ? 'fas fa-tablet-alt'
        : 'fas fa-tablet-alt fa-rotate-90';
    }

    var sizes = deviceSizes[mobileCurrentDevice];
    var w = mobileIsPortrait ? sizes.pw : sizes.ph;
    var h = mobileIsPortrait ? sizes.ph : sizes.pw;
    mobileSizeLabel.textContent = w + ' x ' + h;
  }

  function syncMobileContent() {
    if (!currentProject || !currentPageSlug) return;
    var theme = isDark ? 'dark' : 'light';
    var url = '/env/api/preview.php?project=' + encodeURIComponent(currentProject.slug)
            + '&page=' + encodeURIComponent(currentPageSlug)
            + '&theme=' + theme
            + '&t=' + Date.now();
    mobileContentArea.src = url;
  }

  // ── Global Nav Toggle ──
  btnToggleNav.addEventListener('click', function () {
    globalNav.classList.toggle('nav-open');
    var icon = btnToggleNav.querySelector('i');
    icon.className = globalNav.classList.contains('nav-open')
      ? 'fas fa-chevron-left'
      : 'fas fa-chevron-right';
    updateCourseOpenBtnPosition();
  });

  // ── Course Nav Toggle ──
  // Canvas usa style="display:none" en #left-side para colapsar.
  // Las reglas CSS del proyecto usan body:has(#left-side:not([style*="display: none"]))
  btnToggleCourse.addEventListener('click', function () {
    courseNav.classList.remove('course-nav-open');
    courseNav.style.display = 'none';
    btnOpenCourse.classList.remove('hidden');
    updateCourseOpenBtnPosition();
  });

  btnOpenCourse.addEventListener('click', function () {
    courseNav.style.display = '';
    courseNav.classList.add('course-nav-open');
    btnOpenCourse.classList.add('hidden');
  });

  function updateCourseOpenBtnPosition() {
    var styles = getComputedStyle(document.documentElement);
    var navWidth = globalNav.classList.contains('nav-open')
      ? parseInt(styles.getPropertyValue('--nav-width-open'))
      : parseInt(styles.getPropertyValue('--nav-width-closed'));
    btnOpenCourse.style.left = (navWidth + 8) + 'px';
  }
  updateCourseOpenBtnPosition();

  // ── Code Viewer ──
  btnEditor.addEventListener('click', function () {
    if (!currentProject || !currentPageSlug) {
      alert('Selecciona un proyecto primero.');
      return;
    }
    openCodeViewer();
  });

  async function openCodeViewer() {
    codeViewerOverlay.classList.remove('hidden');

    var projSlug = currentProject.slug;

    if (currentPageSlug === 'index') {
      viewerHtmlFilename.textContent = 'index.html';
    } else {
      viewerHtmlFilename.textContent = 'pages/' + currentPageSlug + '.html';
    }
    document.getElementById('viewer-cssmaster-filename').textContent = 'css/' + projSlug + '-master.css';
    document.getElementById('viewer-css-filename').textContent = 'css/' + projSlug + '-mobile.css';
    document.getElementById('viewer-cssdesktop-filename').textContent = 'css/' + projSlug + '-desktop.css';

    try {
      var resp = await fetch('/env/api/source.php?project=' + encodeURIComponent(projSlug) + '&page=' + encodeURIComponent(currentPageSlug));
      var data = await resp.json();

      document.getElementById('viewer-html-code').textContent = data.html || '(vacío)';
      document.getElementById('viewer-cssmaster-code').textContent = data.cssMaster || '(sin master)';
      document.getElementById('viewer-css-code').textContent = cleanCssForCanvas(data.css) || '(sin estilos)';
      document.getElementById('viewer-cssdesktop-code').textContent = cleanCssForCanvas(data.cssDesktop) || '(sin estilos desktop)';
      document.getElementById('viewer-js-code').textContent = data.js || '(sin scripts)';
    } catch (e) {
      console.error('Error cargando código fuente:', e);
    }
  }

  function cleanCssForCanvas(css) {
    if (!css) return css;
    return css
      .replace(/\/\*[\s\S]*?Ambiente de pruebas[\s\S]*?\*\/\s*/g, '')
      .replace(/html\[data-theme="dark"\]\s*\{[^}]*\}/g, '')
      .replace(/\n{3,}/g, '\n\n')
      .trim();
  }

  btnCloseViewer.addEventListener('click', function () {
    codeViewerOverlay.classList.add('hidden');
  });

  codeViewerOverlay.addEventListener('click', function (e) {
    if (e.target === codeViewerOverlay) {
      codeViewerOverlay.classList.add('hidden');
    }
  });

  viewerTabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tab = btn.dataset.tab;
      viewerTabBtns.forEach(function (b) { b.classList.remove('active'); });
      viewerPanes.forEach(function (p) { p.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('viewer-' + tab).classList.add('active');
    });
  });

  document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.dataset.target;
      var code = document.getElementById(targetId).textContent;

      if (code === '(vacío)' || code === '(sin estilos)' || code === '(sin scripts)' || code === '(sin estilos desktop)') {
        return;
      }

      navigator.clipboard.writeText(code).then(function () {
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado';
        btn.classList.add('copied');
        setTimeout(function () {
          btn.innerHTML = originalHtml;
          btn.classList.remove('copied');
        }, 2000);
      });
    });
  });

  // ── Keyboard shortcuts ──
  document.addEventListener('keydown', function (e) {
    // Ctrl+E → toggle code viewer
    if (e.ctrlKey && e.key === 'e') {
      e.preventDefault();
      if (codeViewerOverlay.classList.contains('hidden')) {
        if (currentProject && currentPageSlug) openCodeViewer();
      } else {
        codeViewerOverlay.classList.add('hidden');
      }
    }
    // Ctrl+R → reload content (prevent browser refresh)
    if (e.ctrlKey && e.key === 'r') {
      e.preventDefault();
      reloadContent();
    }
    // Escape → close modals
    if (e.key === 'Escape') {
      if (!codeViewerOverlay.classList.contains('hidden')) {
        codeViewerOverlay.classList.add('hidden');
      } else if (!mobileFrame.classList.contains('hidden')) {
        mobileFrame.classList.add('hidden');
        btnMobile.classList.remove('active');
        resetDarkMode();
      }
    }
  });

})();
