/* ============================================
   Canvas Themes – Admin / Proyectos
   ============================================ */

(function () {
  'use strict';

  // ── Tabs ──
  var activeTab = document.querySelector('.admin-tab.active[data-tab]');
  if (activeTab) {
    document.querySelectorAll('.admin-panel').forEach(function (p) { p.classList.remove('active'); });
    var panel = document.getElementById('panel-' + activeTab.dataset.tab);
    if (panel) panel.classList.add('active');
  }

  document.querySelectorAll('.admin-tab[data-tab]').forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelectorAll('.admin-tab').forEach(function (t) { t.classList.remove('active'); });
      document.querySelectorAll('.admin-panel').forEach(function (p) { p.classList.remove('active'); });
      tab.classList.add('active');
      document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
      history.replaceState(null, '', tab.href || '/admin');
    });
  });

  // ── Crear invitado ──
  var btnAddGuest = document.getElementById('btn-add-guest');
  var guestForm = document.getElementById('guest-form');
  var btnCreateGuest = document.getElementById('btn-create-guest');
  var btnCancelGuest = document.getElementById('btn-cancel-guest');
  var guestMessage = document.getElementById('guest-message');

  if (btnAddGuest) {
    btnAddGuest.addEventListener('click', function () {
      guestForm.classList.remove('hidden');
      document.getElementById('guest-username').focus();
    });
  }

  if (btnCancelGuest) {
    btnCancelGuest.addEventListener('click', function () {
      guestForm.classList.add('hidden');
    });
  }

  if (btnCreateGuest) {
    btnCreateGuest.addEventListener('click', async function () {
      var username = document.getElementById('guest-username').value.trim();
      var password = document.getElementById('guest-password').value;

      if (!username || !password) {
        guestMessage.textContent = 'Usuario y contraseña son obligatorios.';
        guestMessage.className = 'form-message error';
        guestMessage.classList.remove('hidden');
        return;
      }

      if (password.length < 6) {
        guestMessage.textContent = 'La contraseña debe tener al menos 6 caracteres.';
        guestMessage.className = 'form-message error';
        guestMessage.classList.remove('hidden');
        return;
      }

      btnCreateGuest.disabled = true;
      btnCreateGuest.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';

      try {
        var resp = await fetch('/env/api/create-guest.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
          body: JSON.stringify({ username: username, password: password })
        });
        var data = await resp.json();

        if (data.success) {
          guestMessage.textContent = data.message;
          guestMessage.className = 'form-message success';
          guestMessage.classList.remove('hidden');
          setTimeout(function () { location.reload(); }, 1500);
        } else {
          guestMessage.textContent = data.error || 'Error al crear.';
          guestMessage.className = 'form-message error';
          guestMessage.classList.remove('hidden');
        }
      } catch (e) {
        guestMessage.textContent = 'Error de conexión.';
        guestMessage.className = 'form-message error';
        guestMessage.classList.remove('hidden');
      }

      btnCreateGuest.disabled = false;
      btnCreateGuest.innerHTML = '<i class="fas fa-check"></i> Crear';
    });
  }

  var btnNewProject = document.getElementById('btn-new-project');
  var formCard = document.getElementById('new-project-form');
  var inputName = document.getElementById('project-name');
  var slugPreview = document.getElementById('slug-preview');
  var orgType = document.getElementById('org-type');
  var orgCount = document.getElementById('org-count');
  var btnCreate = document.getElementById('btn-create');
  var btnCancel = document.getElementById('btn-cancel');
  var formMessage = document.getElementById('form-message');
  var projectsGrid = document.getElementById('projects-grid');
  var emptyState = document.getElementById('empty-state');

  // Toggle org count input
  orgType.addEventListener('change', function () {
    orgCount.disabled = orgType.value === 'none';
    if (orgType.value === 'none') orgCount.value = '';
    else if (!orgCount.value) orgCount.value = 4;
  });

  // ── Sincronizar picker ↔ hex (crear + editar) ──
  function syncColorFields(prefix) {
    ['primary', 'secondary'].forEach(function (id) {
      var picker = document.getElementById(prefix + 'color-' + id + '-picker');
      var hex = document.getElementById(prefix + 'color-' + id);
      if (!picker || !hex) return;

      picker.addEventListener('input', function () {
        hex.value = picker.value.toUpperCase();
      });

      hex.addEventListener('input', function () {
        var val = hex.value.trim();
        if (!val.startsWith('#')) val = '#' + val;
        hex.value = val.toUpperCase();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
          picker.value = val;
        }
      });
    });
  }
  syncColorFields('');       // Formulario crear
  syncColorFields('edit-');  // Modal editar

  // Delete modal
  var deleteOverlay = document.getElementById('delete-overlay');
  var deleteProjectName = document.getElementById('delete-project-name');
  var btnConfirmDelete = document.getElementById('btn-confirm-delete');
  var btnCancelDelete = document.getElementById('btn-cancel-delete');
  var pendingDeleteSlug = null;

  // Edit modal
  var editOverlay = document.getElementById('edit-overlay');
  var editProjectName = document.getElementById('edit-project-name');
  var editOrgType = document.getElementById('edit-org-type');
  var editOrgCount = document.getElementById('edit-org-count');
  var editOrgHint = document.getElementById('edit-org-hint');
  var editMessage = document.getElementById('edit-message');
  var btnSaveEdit = document.getElementById('btn-save-edit');
  var btnCancelEdit = document.getElementById('btn-cancel-edit');
  var editColorPrimaryPicker = document.getElementById('edit-color-primary-picker');
  var editColorPrimary = document.getElementById('edit-color-primary');
  var editColorSecondaryPicker = document.getElementById('edit-color-secondary-picker');
  var editColorSecondary = document.getElementById('edit-color-secondary');
  var pendingEditSlug = null;
  var projectsCache = [];

  // ── Load projects ──
  loadProjects();

  async function loadProjects() {
    try {
      var resp = await fetch('/env/api/projects.php');
      var data = await resp.json();
      projectsCache = data.projects || [];
      renderProjects(projectsCache);
    } catch (e) {
      console.error('Error cargando proyectos:', e);
    }
  }

  function renderProjects(projects) {
    projectsGrid.innerHTML = '';

    if (projects.length === 0) {
      emptyState.classList.remove('hidden');
      projectsGrid.classList.add('hidden');
      return;
    }

    emptyState.classList.add('hidden');
    projectsGrid.classList.remove('hidden');

    projects.forEach(function (proj) {
      var card = document.createElement('div');
      card.className = 'project-card';

      var pagesCount = proj.pages.length;
      var pagesText = pagesCount === 1 ? '1 página' : pagesCount + ' páginas';

      var deleteBtn = proj.protected
        ? ''
        : '<button class="btn-delete" data-slug="' + escapeHtml(proj.slug) + '" data-name="' + escapeHtml(proj.name) + '">' +
            '<i class="fas fa-trash"></i>' +
          '</button>';

      var protectedBadge = proj.protected ? ' <span class="badge-protected">Demo</span>' : '';
      var inactiveBadge = !proj.active ? ' <span class="badge-inactive">Inactivo</span>' : '';
      var toggleIcon = proj.active ? 'fa-toggle-on' : 'fa-toggle-off';
      var toggleTitle = proj.active ? 'Desactivar proyecto' : 'Activar proyecto';

      if (!proj.active) card.className += ' project-card-inactive';

      card.innerHTML =
        '<div class="project-card-header">' +
          '<h3><i class="fas fa-folder"></i>' + escapeHtml(proj.name) + protectedBadge + inactiveBadge + '</h3>' +
        '</div>' +
        '<div class="project-card-meta">' +
          '<code>projects/' + escapeHtml(proj.slug) + '/</code>' +
        '</div>' +
        '<div class="project-card-pages">' +
          '<i class="fas fa-file-alt"></i> ' + pagesText +
          (proj.hasCss ? ' &nbsp;<i class="fas fa-palette"></i> CSS' : '') +
          (proj.hasJs ? ' &nbsp;<i class="fas fa-code"></i> JS' : '') +
        '</div>' +
        '<div class="project-card-actions">' +
          '<button class="btn-toggle" data-slug="' + escapeHtml(proj.slug) + '" data-active="' + (proj.active ? '1' : '0') + '" title="' + toggleTitle + '">' +
            '<i class="fas ' + toggleIcon + '"></i>' +
          '</button>' +
          '<a href="index.php#project=' + encodeURIComponent(proj.slug) + '" class="btn-open" title="Abrir">' +
            '<i class="fas fa-eye"></i>' +
          '</a>' +
          '<button class="btn-edit" data-slug="' + escapeHtml(proj.slug) + '" data-name="' + escapeHtml(proj.name) + '" title="Editar">' +
            '<i class="fas fa-pen"></i>' +
          '</button>' +
          '<button class="btn-compile" data-slug="' + escapeHtml(proj.slug) + '" title="Compilar CSS (master → mobile + desktop)">' +
            '<i class="fas fa-cogs"></i>' +
          '</button>' +
          deleteBtn +
        '</div>';

      projectsGrid.appendChild(card);
    });

    // Bind delete buttons
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
      btn.addEventListener('click', function () {
        pendingDeleteSlug = btn.dataset.slug;
        deleteProjectName.textContent = btn.dataset.name;
        deleteOverlay.classList.remove('hidden');
      });
    });

    // Bind edit buttons
    document.querySelectorAll('.btn-edit').forEach(function (btn) {
      btn.addEventListener('click', function () {
        pendingEditSlug = btn.dataset.slug;
        var proj = projectsCache.find(function (p) { return p.slug === btn.dataset.slug; });

        editProjectName.value = btn.dataset.name;

        // Pre-cargar colores
        var pc = (proj && proj.colors) ? proj.colors.primary : '#0374B5';
        var sc = (proj && proj.colors) ? proj.colors.secondary : '#2D3B45';
        editColorPrimary.value = pc.toUpperCase();
        editColorPrimaryPicker.value = pc;
        editColorSecondary.value = sc.toUpperCase();
        editColorSecondaryPicker.value = sc;

        // Pre-cargar organización
        if (proj && proj.orgType !== 'none') {
          editOrgType.value = proj.orgType;
          editOrgCount.value = proj.orgCount;
          editOrgCount.disabled = false;
          editOrgHint.textContent = 'Actualmente: ' + proj.orgCount + ' página(s). Solo agrega nuevas.';
        } else {
          editOrgType.value = 'none';
          editOrgCount.value = '';
          editOrgCount.disabled = true;
          editOrgHint.textContent = 'Solo agrega nuevas páginas. No elimina las existentes.';
        }

        editMessage.classList.add('hidden');
        editOverlay.classList.remove('hidden');
        editProjectName.focus();
      });
    });

    // Bind compile buttons
    document.querySelectorAll('.btn-compile').forEach(function (btn) {
      btn.addEventListener('click', function () {
        compileProject(btn.dataset.slug, btn);
      });
    });

    // Bind toggle buttons
    document.querySelectorAll('.btn-toggle').forEach(function (btn) {
      btn.addEventListener('click', async function () {
        var slug = btn.dataset.slug;
        var currentlyActive = btn.dataset.active === '1';
        btn.disabled = true;
        try {
          var resp = await fetch('/env/api/toggle-project.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
            body: JSON.stringify({ slug: slug, active: !currentlyActive })
          });
          var data = await resp.json();
          if (data.success) loadProjects();
        } catch (e) {
          console.error('Error toggling project:', e);
        }
        btn.disabled = false;
      });
    });
  }

  // ── Compile CSS ──
  async function compileProject(slug, btn) {
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
      var resp = await fetch('/env/api/compile-css.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
        body: JSON.stringify({ project: slug })
      });
      var data = await resp.json();

      if (data.success) {
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.add('compile-success');
        setTimeout(function () {
          btn.innerHTML = originalHtml;
          btn.classList.remove('compile-success');
          btn.disabled = false;
        }, 2000);
      } else {
        alert(data.error || 'Error al compilar.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
      }
    } catch (e) {
      alert('Error de conexión.');
      btn.innerHTML = originalHtml;
      btn.disabled = false;
    }
  }

  // ── Edit project ──
  editOrgType.addEventListener('change', function () {
    editOrgCount.disabled = editOrgType.value === 'none';
    if (editOrgType.value === 'none') editOrgCount.value = '';
    else if (!editOrgCount.value) editOrgCount.value = 4;
  });

  btnCancelEdit.addEventListener('click', function () {
    editOverlay.classList.add('hidden');
    pendingEditSlug = null;
  });

  editOverlay.addEventListener('click', function (e) {
    if (e.target === editOverlay) {
      editOverlay.classList.add('hidden');
      pendingEditSlug = null;
    }
  });

  btnSaveEdit.addEventListener('click', async function () {
    if (!pendingEditSlug) return;

    var name = editProjectName.value.trim();
    var org = editOrgType.value;
    var count = (org !== 'none' && editOrgCount.value) ? parseInt(editOrgCount.value) : 0;

    btnSaveEdit.disabled = true;
    btnSaveEdit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
      var resp = await fetch('/env/api/edit-project.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
        body: JSON.stringify({
          slug: pendingEditSlug,
          name: name,
          colors: {
            primary: editColorPrimary.value,
            secondary: editColorSecondary.value
          },
          orgType: org,
          orgCount: count
        })
      });
      var data = await resp.json();

      if (data.success) {
        editMessage.textContent = data.message;
        editMessage.className = 'form-message success';
        editMessage.classList.remove('hidden');
        loadProjects();
        setTimeout(function () {
          editOverlay.classList.add('hidden');
          pendingEditSlug = null;
        }, 1500);
      } else {
        editMessage.textContent = data.error || 'Error al guardar.';
        editMessage.className = 'form-message error';
        editMessage.classList.remove('hidden');
      }
    } catch (e) {
      editMessage.textContent = 'Error de conexión.';
      editMessage.className = 'form-message error';
      editMessage.classList.remove('hidden');
    }

    btnSaveEdit.disabled = false;
    btnSaveEdit.innerHTML = '<i class="fas fa-save"></i> Guardar';
  });

  // ── Show/hide create form ──
  btnNewProject.addEventListener('click', function () {
    formCard.classList.remove('hidden');
    inputName.value = '';
    slugPreview.textContent = '---';
    formMessage.classList.add('hidden');
    inputName.focus();
  });

  btnCancel.addEventListener('click', function () {
    formCard.classList.add('hidden');
  });

  // ── Slug preview ──
  inputName.addEventListener('input', function () {
    var slug = toSlug(inputName.value);
    slugPreview.textContent = slug || '---';
  });

  function toSlug(text) {
    return text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }

  // ── Create project ──
  btnCreate.addEventListener('click', async function () {
    var name = inputName.value.trim();
    if (!name) {
      showMessage('Ingresa un nombre para el proyecto.', 'error');
      return;
    }

    var slug = toSlug(name);
    if (!slug) {
      showMessage('El nombre no genera un identificador válido.', 'error');
      return;
    }

    var cdns = [];
    document.querySelectorAll('.cdn-check input:checked').forEach(function (cb) {
      cdns.push(cb.value);
    });

    var colors = {
      primary:   document.getElementById('color-primary').value,
      secondary: document.getElementById('color-secondary').value
    };

    var organization = orgType.value;
    var orgCountVal = (organization !== 'none' && orgCount.value) ? parseInt(orgCount.value) : 0;

    btnCreate.disabled = true;
    btnCreate.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';

    try {
      var resp = await fetch('/env/api/create-project.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
        body: JSON.stringify({
          name: name,
          slug: slug,
          cdns: cdns,
          colors: colors,
          orgType: organization,
          orgCount: orgCountVal
        })
      });
      var data = await resp.json();

      if (data.success) {
        showMessage('Proyecto "' + name + '" creado correctamente.', 'success');
        inputName.value = '';
        slugPreview.textContent = '---';
        orgType.value = 'none';
        orgCount.value = '';
        orgCount.disabled = true;
        document.querySelectorAll('.cdn-check input').forEach(function (cb) { cb.checked = false; });
        loadProjects();
        setTimeout(function () { formCard.classList.add('hidden'); }, 1500);
      } else {
        showMessage(data.error || 'Error al crear el proyecto.', 'error');
      }
    } catch (e) {
      showMessage('Error de conexión.', 'error');
    }

    btnCreate.disabled = false;
    btnCreate.innerHTML = '<i class="fas fa-check"></i> Crear';
  });

  inputName.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') btnCreate.click();
  });

  // ── Delete project ──
  btnCancelDelete.addEventListener('click', function () {
    deleteOverlay.classList.add('hidden');
    pendingDeleteSlug = null;
  });

  deleteOverlay.addEventListener('click', function (e) {
    if (e.target === deleteOverlay) {
      deleteOverlay.classList.add('hidden');
      pendingDeleteSlug = null;
    }
  });

  btnConfirmDelete.addEventListener('click', async function () {
    if (!pendingDeleteSlug) return;

    btnConfirmDelete.disabled = true;
    btnConfirmDelete.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

    try {
      var resp = await fetch('/env/api/delete-project.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
        body: JSON.stringify({ slug: pendingDeleteSlug })
      });
      var data = await resp.json();

      if (data.success) {
        loadProjects();
      } else {
        alert(data.error || 'Error al eliminar.');
      }
    } catch (e) {
      alert('Error de conexión.');
    }

    deleteOverlay.classList.add('hidden');
    pendingDeleteSlug = null;
    btnConfirmDelete.disabled = false;
    btnConfirmDelete.innerHTML = '<i class="fas fa-trash"></i> Eliminar';
  });

  // ── Cambiar contraseñas ──
  var pwMessage = document.getElementById('pw-message');

  document.querySelectorAll('.btn-change-pw').forEach(function (btn) {
    btn.addEventListener('click', async function () {
      var user = btn.dataset.user;
      var input = document.getElementById('pw-' + user);
      var password = input.value.trim();

      if (password.length < 6) {
        pwMessage.textContent = 'La contraseña debe tener al menos 6 caracteres.';
        pwMessage.className = 'form-message error';
        pwMessage.classList.remove('hidden');
        return;
      }

      btn.disabled = true;
      var originalHtml = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      try {
        var resp = await fetch('/env/api/change-password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
          body: JSON.stringify({ username: user, password: password })
        });
        var data = await resp.json();

        if (data.success) {
          pwMessage.textContent = data.message;
          pwMessage.className = 'form-message success';
          input.value = '';
        } else {
          pwMessage.textContent = data.error || 'Error al cambiar la contraseña.';
          pwMessage.className = 'form-message error';
        }
        pwMessage.classList.remove('hidden');
      } catch (e) {
        pwMessage.textContent = 'Error de conexión.';
        pwMessage.className = 'form-message error';
        pwMessage.classList.remove('hidden');
      }

      btn.disabled = false;
      btn.innerHTML = originalHtml;
    });
  });

  // ── Guardar correo de recuperación ──
  var btnSaveEmail = document.getElementById('btn-save-email');
  var emailInput = document.getElementById('recovery-email');
  var emailMessage = document.getElementById('email-message');

  if (btnSaveEmail) {
    btnSaveEmail.addEventListener('click', async function () {
      var email = emailInput.value.trim();
      if (!email) {
        emailMessage.textContent = 'Ingresa un correo electrónico.';
        emailMessage.className = 'form-message error';
        emailMessage.classList.remove('hidden');
        return;
      }

      btnSaveEmail.disabled = true;
      btnSaveEmail.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      try {
        var resp = await fetch('/env/api/update-email.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
          body: JSON.stringify({ email: email })
        });
        var data = await resp.json();

        if (data.success) {
          emailMessage.textContent = data.message;
          emailMessage.className = 'form-message success';
        } else {
          emailMessage.textContent = data.error || 'Error al guardar.';
          emailMessage.className = 'form-message error';
        }
        emailMessage.classList.remove('hidden');
      } catch (e) {
        emailMessage.textContent = 'Error de conexión.';
        emailMessage.className = 'form-message error';
        emailMessage.classList.remove('hidden');
      }

      btnSaveEmail.disabled = false;
      btnSaveEmail.innerHTML = '<i class="fas fa-save"></i> Guardar';
    });
  }

  // ── Helpers ──
  function showMessage(text, type) {
    formMessage.textContent = text;
    formMessage.className = 'form-message ' + type;
    formMessage.classList.remove('hidden');
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

})();
