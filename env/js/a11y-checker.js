/* ══════════════════════════════════════════════════════════════════════════
   A11y Checker — Powered by axe-core
   Evaluates accessibility only within #content-body (project content).
   ══════════════════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  var btnA11y = document.getElementById('btn-a11y');
  var panel = document.getElementById('a11y-panel');
  var results = document.getElementById('a11y-results');
  var btnClose = document.getElementById('a11y-close');

  if (!btnA11y || !panel || !results) return;

  btnClose.addEventListener('click', function () {
    panel.classList.add('hidden');
  });

  btnA11y.addEventListener('click', function () {
    panel.classList.remove('hidden');
    runCheck();
  });

  function runCheck() {
    var target = document.getElementById('content-body');
    if (!target) {
      results.innerHTML = '<p class="a11y-placeholder">No hay contenido cargado.</p>';
      return;
    }

    results.innerHTML = '<p class="a11y-loading"><i class="fas fa-spinner fa-spin"></i> Evaluando...</p>';

    if (typeof axe === 'undefined') {
      results.innerHTML = '<p class="a11y-placeholder">axe-core no está disponible.</p>';
      return;
    }

    axe.run(target, {
      runOnly: ['wcag2a', 'wcag2aa', 'best-practice'],
      rules: {
        'frame-tested': { enabled: false }
      }
    }).then(function (res) {
      renderResults(res);
    }).catch(function (err) {
      results.innerHTML = '<p class="a11y-placeholder">Error: ' + err.message + '</p>';
    });
  }

  var impactES = { critical: 'Cr\u00edtico', serious: 'Grave', moderate: 'Moderado', minor: 'Menor' };

  function renderResults(res) {
    var html = '';

    var violations = res.violations.length;
    var passes = res.passes.length;
    var incomplete = res.incomplete.length;

    html += '<div class="a11y-summary">';
    if (violations === 0) {
      html += '<div class="a11y-summary-item a11y-pass"><i class="fas fa-check-circle"></i> Sin errores de accesibilidad</div>';
    } else {
      html += '<div class="a11y-summary-item a11y-fail"><i class="fas fa-exclamation-circle"></i> ' + violations + ' error' + (violations > 1 ? 'es' : '') + '</div>';
    }
    html += '<div class="a11y-summary-item a11y-ok"><i class="fas fa-check"></i> ' + passes + ' reglas aprobadas</div>';
    if (incomplete > 0) {
      html += '<div class="a11y-summary-item a11y-warn"><i class="fas fa-question-circle"></i> ' + incomplete + ' requiere revisi\u00f3n manual</div>';
    }
    html += '</div>';

    if (violations > 0) {
      html += '<h4 class="a11y-section-title">Errores encontrados</h4>';
      res.violations.forEach(function (v) {
        var impact = impactES[v.impact] || v.impact;
        html += '<details class="a11y-item a11y-item-fail">';
        html += '<summary><span class="a11y-impact a11y-impact-' + v.impact + '">' + impact + '</span> ' + v.help + '</summary>';
        html += '<div class="a11y-item-body">';
        html += '<p class="a11y-desc">' + v.description + '</p>';
        html += '<p class="a11y-tags">';
        v.tags.forEach(function (t) { html += '<span class="a11y-tag">' + t + '</span> '; });
        html += '</p>';
        html += '<p class="a11y-label">Elementos afectados:</p>';
        v.nodes.forEach(function (n) {
          html += '<div class="a11y-node">';
          html += '<code>' + escapeHtml(n.html) + '</code>';
          if (n.failureSummary) {
            var fix = n.failureSummary
              .replace('Fix any of the following:', 'Corregir alguno de los siguientes:')
              .replace('Fix all of the following:', 'Corregir todos los siguientes:')
              .replace('Element has insufficient color contrast', 'El elemento tiene contraste de color insuficiente')
              .replace('Expected contrast ratio of', 'Ratio de contraste esperado:')
              .replace('foreground color:', 'color del texto:')
              .replace('background color:', 'color de fondo:')
              .replace('font-size:', 'tama\u00f1o de fuente:')
              .replace('font-weight:', 'peso de fuente:')
              .replace('Element does not have an alt attribute', 'El elemento no tiene atributo alt')
              .replace('Required ARIA attribute not present', 'Falta atributo ARIA requerido')
              .replace('Element has no title attribute', 'El elemento no tiene atributo title')
              .replace('Element is in tab order and does not have accessible text', 'El elemento est\u00e1 en el orden de tabulaci\u00f3n y no tiene texto accesible')
              .replace('Heading order invalid', 'Orden de encabezados inv\u00e1lido')
              .replace(/\n/g, '<br>');
            html += '<p class="a11y-fix">' + fix + '</p>';
          }
          html += '</div>';
        });
        html += '</div></details>';
      });
    }

    if (incomplete > 0) {
      html += '<h4 class="a11y-section-title">Requiere revisi\u00f3n manual</h4>';
      res.incomplete.forEach(function (v) {
        var impact = impactES[v.impact] || v.impact;
        html += '<details class="a11y-item a11y-item-warn">';
        html += '<summary><span class="a11y-impact a11y-impact-' + v.impact + '">' + impact + '</span> ' + v.help + '</summary>';
        html += '<div class="a11y-item-body">';
        html += '<p class="a11y-desc">' + v.description + '</p>';
        v.nodes.forEach(function (n) {
          html += '<div class="a11y-node"><code>' + escapeHtml(n.html) + '</code></div>';
        });
        html += '</div></details>';
      });
    }

    results.innerHTML = html;
  }

  function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

})();
