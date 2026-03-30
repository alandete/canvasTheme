/* ══════════════════════════════════════════════════════════════════════════
   Palette Generator — Powered by Chroma.js
   Generates accessible color swatches from CSS custom properties.
   ══════════════════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  if (typeof chroma === 'undefined') return;

  // ── Helpers ──

  function getCSSVar(name) {
    // Try content-body first (project CSS is scoped there), then root
    var contentBody = document.getElementById('content-body');
    if (contentBody) {
      var val = getComputedStyle(contentBody).getPropertyValue(name).trim();
      if (val) return val;
    }
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  }

  function ratio(fg, bg) {
    try { return chroma.contrast(fg, bg); }
    catch (e) { return 0; }
  }

  function ratioLabel(r) {
    var text = r.toFixed(1) + ':1';
    if (r >= 7) return { text: text + ' AAA', cls: 'pass' };
    if (r >= 4.5) return { text: text + ' AA', cls: 'pass' };
    if (r >= 3) return { text: text + ' AA grande', cls: 'pass' };
    return { text: text + ' Falla', cls: 'fail' };
  }

  function bestText(bg) {
    try {
      var c = chroma(bg);
      return c.luminance() > 0.35 ? '#1A1A1A' : '#FFFFFF';
    } catch (e) { return '#1A1A1A'; }
  }

  function swatch(bg, name, varName, contrastBg) {
    contrastBg = contrastBg || '#FFFFFF';
    var textColor = bestText(bg);
    var r = ratio(bg, contrastBg);
    var label = ratioLabel(r);
    return '<div class="palette-swatch">' +
      '<div class="palette-swatch-color" style="background:' + bg + ';color:' + textColor + ';">Aa</div>' +
      '<div class="palette-swatch-info">' +
        '<span class="swatch-name">' + name + '</span>' +
        '<span class="swatch-var">' + varName + '</span>' +
        '<span class="swatch-var">' + bg + '</span>' +
      '</div></div>';
  }

  function swatchWithContrast(bg, name, varName, contrastAgainst, contrastLabel) {
    var textColor = bestText(bg);
    var r = ratio(contrastAgainst, bg);
    var label = ratioLabel(r);
    return '<div class="palette-swatch">' +
      '<div class="palette-swatch-color" style="background:' + bg + ';color:' + textColor + ';">Aa</div>' +
      '<div class="palette-swatch-info">' +
        '<span class="swatch-name">' + name + '</span>' +
        '<span class="swatch-var">' + varName + '</span>' +
        '<span class="swatch-var">' + bg + '</span>' +
        '<span class="swatch-contrast ' + label.cls + '">' + (contrastLabel || 'vs texto') + ': ' + label.text + '</span>' +
      '</div></div>';
  }

  function btnDemo(bg, text, label, sublabel) {
    var r = ratio(text, bg);
    var rl = ratioLabel(r);
    return '<div style="text-align:center;">' +
      '<div class="palette-btn-demo" style="background:' + bg + ';color:' + text + ';">' + label +
        '<small>' + sublabel + '</small>' +
      '</div>' +
      '<span class="swatch-contrast ' + rl.cls + '" style="margin-top:6px;display:inline-block;font-size:.7rem;">' + rl.text + '</span>' +
    '</div>';
  }

  // ── Read colors from CSS ──

  function readPalette() {
    // Read computed colors from CSS first, fallback to base
    var primary = getCSSVar('--ct-primary') || getCSSVar('--ct-primary-base') || '#0374B5';
    var secondary = getCSSVar('--ct-secondary') || getCSSVar('--ct-secondary-base') || '#2D3B45';
    // But always use base for generation
    var primaryBase = getCSSVar('--ct-primary-base') || primary;
    var secondaryBase = getCSSVar('--ct-secondary-base') || secondary;
    primary = primaryBase;
    secondary = secondaryBase;

    // Generate scales with chroma
    var pLight = chroma.mix('white', primary, 0.3, 'oklch').hex();
    var pDark = chroma.mix('black', primary, 0.3, 'oklch').hex();
    var sLight = chroma.mix('white', secondary, 0.3, 'oklch').hex();
    var sDark = chroma.mix('black', secondary, 0.3, 'oklch').hex();

    // Neutrals
    var neutrals = {};
    var steps = [0, 3, 7, 14, 25, 40, 55, 68, 78, 87, 93, 97];
    var names = ['000','050','100','200','300','400','500','600','700','800','900','950'];
    for (var i = 0; i < steps.length; i++) {
      var gray = chroma.mix('white', 'black', steps[i] / 100, 'oklch');
      neutrals[names[i]] = chroma.mix(gray, secondary, 0.04, 'oklch').hex();
    }

    // Light tokens
    var light = {
      bg: neutrals['000'],
      bgRaised: neutrals['000'],
      bgSubtle: neutrals['050'],
      bgMuted: neutrals['100'],
      text: neutrals['950'],
      textSecondary: neutrals['600'],
      textMuted: neutrals['400'],
      border: neutrals['200'],
      borderStrong: neutrals['300']
    };

    // Dark tokens — enough separation between bg levels
    var dark = {
      bg: neutrals['950'],
      bgRaised: neutrals['800'],
      bgSubtle: neutrals['900'],
      bgMuted: neutrals['700'],
      text: neutrals['050'],
      textSecondary: neutrals['300'],
      textMuted: neutrals['500'],
      border: neutrals['700'],
      borderStrong: neutrals['600']
    };

    // Dark primary/secondary (lightened for dark bg)
    var pDarkMode = chroma.mix(primary, 'white', 0.35, 'oklch').hex();
    var sDarkMode = chroma.mix(secondary, 'white', 0.45, 'oklch').hex();

    // Button text: auto-detect
    var btnPrimaryText = bestText(primary);
    var btnSecondaryText = bestText(secondary);
    var btnPrimaryTextDark = bestText(pDarkMode);
    var btnSecondaryTextDark = bestText(sDarkMode);

    // Hover/active
    var pHover = chroma.mix(primary, 'black', 0.18, 'oklch').hex();
    var pActive = chroma.mix(primary, 'black', 0.30, 'oklch').hex();
    var sHover = chroma.mix(secondary, 'black', 0.18, 'oklch').hex();
    var sActive = chroma.mix(secondary, 'black', 0.30, 'oklch').hex();
    var pHoverDark = chroma.mix(pDarkMode, 'white', 0.18, 'oklch').hex();
    var pActiveDark = chroma.mix(pDarkMode, 'white', 0.30, 'oklch').hex();
    var sHoverDark = chroma.mix(sDarkMode, 'white', 0.18, 'oklch').hex();
    var sActiveDark = chroma.mix(sDarkMode, 'white', 0.30, 'oklch').hex();

    return {
      primary: primary, pLight: pLight, pDark: pDark,
      secondary: secondary, sLight: sLight, sDark: sDark,
      neutrals: neutrals, light: light, dark: dark,
      pDarkMode: pDarkMode, sDarkMode: sDarkMode,
      btnPrimaryText: btnPrimaryText, btnSecondaryText: btnSecondaryText,
      btnPrimaryTextDark: btnPrimaryTextDark, btnSecondaryTextDark: btnSecondaryTextDark,
      pHover: pHover, pActive: pActive, sHover: sHover, sActive: sActive,
      pHoverDark: pHoverDark, pActiveDark: pActiveDark,
      sHoverDark: sHoverDark, sActiveDark: sActiveDark
    };
  }

  // ── Render Tabs ──

  function renderColors(p) {
    var html = '';

    html += '<h2>Colores base</h2><div class="palette-grid">';
    html += swatch(p.primary, 'Primario', '--ct-primary-base');
    html += swatch(p.secondary, 'Secundario', '--ct-secondary-base');
    html += '</div>';

    html += '<h2>Escala primario</h2><div class="palette-grid">';
    html += swatch(p.pLight, 'Light', '--ct-primary-light');
    html += swatch(p.primary, 'Base', '--ct-primary');
    html += swatch(p.pDark, 'Dark', '--ct-primary-dark');
    html += '</div>';

    html += '<h2>Escala secundario</h2><div class="palette-grid">';
    html += swatch(p.sLight, 'Light', '--ct-secondary-light');
    html += swatch(p.secondary, 'Base', '--ct-secondary');
    html += swatch(p.sDark, 'Dark', '--ct-secondary-dark');
    html += '</div>';

    html += '<h2>Neutros tintados</h2><div class="palette-grid">';
    var names = ['000','050','100','200','300','400','500','600','700','800','900','950'];
    for (var i = 0; i < names.length; i++) {
      html += swatch(p.neutrals[names[i]], names[i], '--ct-neutral-' + names[i]);
    }
    html += '</div>';

    return html;
  }

  function renderTokens(p) {
    var html = '';

    html += '<h2>Fondos (Light)</h2><div class="palette-grid">';
    html += swatchWithContrast(p.light.bg, 'Body', '--ct-bg', p.light.text, 'vs texto');
    html += swatchWithContrast(p.light.bgRaised, 'Raised', '--ct-bg-raised', p.light.text, 'vs texto');
    html += swatchWithContrast(p.light.bgSubtle, 'Subtle', '--ct-bg-subtle', p.light.text, 'vs texto');
    html += swatchWithContrast(p.light.bgMuted, 'Muted', '--ct-bg-muted', p.light.text, 'vs texto');
    html += '</div>';

    html += '<h2>Fondos (Dark)</h2><div class="palette-grid">';
    html += swatchWithContrast(p.dark.bg, 'Body', '--ct-bg', p.dark.text, 'vs texto');
    html += swatchWithContrast(p.dark.bgRaised, 'Raised', '--ct-bg-raised', p.dark.text, 'vs texto');
    html += swatchWithContrast(p.dark.bgSubtle, 'Subtle', '--ct-bg-subtle', p.dark.text, 'vs texto');
    html += swatchWithContrast(p.dark.bgMuted, 'Muted', '--ct-bg-muted', p.dark.text, 'vs texto');
    html += '</div>';

    html += '<h2>Texto sobre fondos</h2><div class="palette-grid">';
    html += swatchWithContrast(p.light.bg, 'Principal', '--ct-text', p.light.text, 'Light');
    html += swatchWithContrast(p.light.bg, 'Secundario', '--ct-text-secondary', p.light.textSecondary, 'Light');
    html += swatchWithContrast(p.light.bg, 'Muted', '--ct-text-muted', p.light.textMuted, 'Light');
    html += swatchWithContrast(p.dark.bg, 'Principal', '--ct-text (dark)', p.dark.text, 'Dark');
    html += swatchWithContrast(p.dark.bg, 'Secundario', '--ct-text-secondary (dark)', p.dark.textSecondary, 'Dark');
    html += swatchWithContrast(p.dark.bg, 'Muted', '--ct-text-muted (dark)', p.dark.textMuted, 'Dark');
    html += '</div>';

    html += '<h2>Enlaces sobre fondo</h2><div class="palette-grid">';
    html += swatchWithContrast(p.light.bg, 'Link (light)', '--ct-link', p.primary, 'Light');
    html += swatchWithContrast(p.dark.bg, 'Link (dark)', '--ct-link', p.pDarkMode, 'Dark');
    html += '</div>';

    html += '<h2>Bordes</h2><div class="palette-grid">';
    html += swatch(p.light.border, 'Normal (light)', '--ct-border');
    html += swatch(p.light.borderStrong, 'Fuerte (light)', '--ct-border-strong');
    html += swatch(p.dark.border, 'Normal (dark)', '--ct-border');
    html += swatch(p.dark.borderStrong, 'Fuerte (dark)', '--ct-border-strong');
    html += '</div>';

    return html;
  }

  function renderButtons(p) {
    var html = '';

    html += '<h2>Primario — Light</h2><div class="palette-row">';
    html += btnDemo(p.primary, p.btnPrimaryText, 'Normal', p.primary);
    html += btnDemo(p.pHover, p.btnPrimaryText, 'Hover', p.pHover);
    html += btnDemo(p.pActive, p.btnPrimaryText, 'Active', p.pActive);
    html += '</div>';

    html += '<h2>Secundario — Light</h2><div class="palette-row">';
    html += btnDemo(p.secondary, p.btnSecondaryText, 'Normal', p.secondary);
    html += btnDemo(p.sHover, p.btnSecondaryText, 'Hover', p.sHover);
    html += btnDemo(p.sActive, p.btnSecondaryText, 'Active', p.sActive);
    html += '</div>';

    html += '<h2>Outline — Light</h2><div class="palette-row">';
    html += '<div style="text-align:center;"><div class="palette-btn-demo" style="background:transparent;color:' + p.primary + ';border:1px solid ' + p.primary + ';">Normal</div></div>';
    html += '</div>';

    // Dark
    html += '<div style="background:' + p.dark.bg + ';padding:20px;border-radius:12px;border:1px solid ' + p.dark.border + ';">';
    html += '<h2 style="color:' + p.dark.textSecondary + ';margin-top:0;">Primario — Dark</h2><div class="palette-row">';
    html += btnDemo(p.pDarkMode, p.btnPrimaryTextDark, 'Normal', p.pDarkMode);
    html += btnDemo(p.pHoverDark, p.btnPrimaryTextDark, 'Hover', p.pHoverDark);
    html += btnDemo(p.pActiveDark, p.btnPrimaryTextDark, 'Active', p.pActiveDark);
    html += '</div>';

    html += '<h2 style="color:' + p.dark.textSecondary + ';">Secundario — Dark</h2><div class="palette-row">';
    html += btnDemo(p.sDarkMode, p.btnSecondaryTextDark, 'Normal', p.sDarkMode);
    html += btnDemo(p.sHoverDark, p.btnSecondaryTextDark, 'Hover', p.sHoverDark);
    html += btnDemo(p.sActiveDark, p.btnSecondaryTextDark, 'Active', p.sActiveDark);
    html += '</div>';

    html += '<h2 style="color:' + p.dark.textSecondary + ';">Outline — Dark</h2><div class="palette-row">';
    html += '<div style="text-align:center;"><div class="palette-btn-demo" style="background:transparent;color:' + p.pDarkMode + ';border:1px solid ' + p.pDarkMode + ';">Normal</div></div>';
    html += '</div></div>';

    return html;
  }

  function renderStructures(p) {
    var html = '';

    // ── Tarjeta informativa: 2 variantes light + 2 dark ──
    function cardBorder(mode) {
      var m = mode === 'dark' ? p.dark : p.light;
      var link = mode === 'dark' ? p.pDarkMode : p.primary;
      var btnBg = mode === 'dark' ? p.pDarkMode : p.primary;
      var btnText = mode === 'dark' ? p.btnPrimaryTextDark : p.btnPrimaryText;
      var border = mode === 'dark' ? m.borderStrong : m.border;
      var cardBg = mode === 'dark' ? m.bgRaised : 'transparent';
      var label = mode === 'dark' ? 'Dark — con borde' : 'Light — con borde';

      return '<div class="palette-preview" style="background:' + m.bg + ';">' +
        '<div class="palette-mode-label" style="color:' + m.textSecondary + ';">' + label + '</div>' +
        '<div style="background:' + cardBg + ';border:1px solid ' + border + ';border-radius:8px;padding:16px;">' +
          '<h4 style="margin:0 0 6px;color:' + link + ';font-size:1rem;">Título de la tarjeta</h4>' +
          '<p style="margin:0 0 10px;color:' + m.textSecondary + ';font-size:.85rem;">Descripción breve del contenido.</p>' +
          '<div class="palette-row" style="margin:0;">' +
            btnDemo(btnBg, btnText, 'Primario', '') +
            '<div style="text-align:center;"><div class="palette-btn-demo" style="background:transparent;color:' + link + ';border:1px solid ' + link + ';font-size:.8rem;">Outline</div></div>' +
          '</div>' +
        '</div>' +
      '</div>';
    }

    function cardFilled(mode) {
      var m = mode === 'dark' ? p.dark : p.light;
      var link = mode === 'dark' ? p.pDarkMode : p.primary;
      var btnBg = mode === 'dark' ? p.pDarkMode : p.primary;
      var btnText = mode === 'dark' ? p.btnPrimaryTextDark : p.btnPrimaryText;
      var bg = mode === 'dark' ? m.bgMuted : m.bgSubtle;
      var label = mode === 'dark' ? 'Dark — con fondo' : 'Light — con fondo';

      return '<div class="palette-preview" style="background:' + m.bg + ';">' +
        '<div class="palette-mode-label" style="color:' + m.textSecondary + ';">' + label + '</div>' +
        '<div style="background:' + bg + ';border:none;border-radius:8px;padding:16px;">' +
          '<h4 style="margin:0 0 6px;color:' + link + ';font-size:1rem;">Título de la tarjeta</h4>' +
          '<p style="margin:0 0 10px;color:' + m.textSecondary + ';font-size:.85rem;">Descripción breve del contenido.</p>' +
          '<div class="palette-row" style="margin:0;">' +
            btnDemo(btnBg, btnText, 'Primario', '') +
            '<div style="text-align:center;"><div class="palette-btn-demo" style="background:transparent;color:' + link + ';border:1px solid ' + link + ';font-size:.8rem;">Outline</div></div>' +
          '</div>' +
        '</div>' +
      '</div>';
    }

    html += '<h2>Tarjeta informativa</h2>';
    html += '<div class="palette-compare">' + cardBorder('light') + cardFilled('light') + '</div>';
    html += '<div class="palette-compare">' + cardBorder('dark') + cardFilled('dark') + '</div>';

    // ── Sección de contenido ──
    function section(mode) {
      var m = mode === 'dark' ? p.dark : p.light;
      var link = mode === 'dark' ? p.pDarkMode : p.primary;
      var headerBg = mode === 'dark' ? m.bgMuted : m.bgSubtle;
      var headerBorder = '1px solid ' + (mode === 'dark' ? m.borderStrong : m.border);

      return '<div class="palette-preview" style="background:' + m.bg + ';">' +
        '<div class="palette-mode-label" style="color:' + m.textSecondary + ';">' + (mode === 'dark' ? 'Dark' : 'Light') + '</div>' +
        '<div style="display:flex;align-items:center;gap:10px;padding:12px;background:' + headerBg + ';border:' + headerBorder + ';border-radius:8px;margin-bottom:10px;">' +
          '<span style="font-size:1.2rem;color:' + link + ';">★</span>' +
          '<div><div style="font-size:.95rem;font-weight:600;color:' + m.text + ';">Introducción a la semana</div>' +
          '<div style="font-size:.75rem;color:' + m.textSecondary + ';">Lo que exploraremos juntos</div></div>' +
        '</div>' +
        '<div style="padding:0 12px;">' +
          '<p style="color:' + m.text + ';font-size:.85rem;margin:0 0 6px;">Texto de cuerpo con información del contenido.</p>' +
          '<span style="color:' + link + ';font-size:.8rem;">Ver más →</span>' +
        '</div>' +
      '</div>';
    }

    html += '<h2>Sección de contenido</h2><div class="palette-compare">';
    html += section('light') + section('dark');
    html += '</div>';

    // ── Banner de acción ──
    function banner(mode) {
      var m = mode === 'dark' ? p.dark : p.light;
      var bannerBg = mode === 'dark' ? p.pDarkMode : p.primary;
      var bannerText = mode === 'dark' ? p.btnPrimaryTextDark : p.btnPrimaryText;
      var bannerTextR = ratio(bannerText, bannerBg);
      var bannerLabel = ratioLabel(bannerTextR);
      var invertBtnBg = mode === 'dark' ? m.bg : m.bg;
      var invertBtnText = bannerBg;
      var outlineBorder = mode === 'dark' ? bannerText : bannerText;

      return '<div class="palette-preview" style="background:' + m.bg + ';">' +
        '<div class="palette-mode-label" style="color:' + m.textSecondary + ';">' + (mode === 'dark' ? 'Dark' : 'Light') +
          ' <span class="swatch-contrast ' + bannerLabel.cls + '" style="font-size:.6rem;">' + bannerLabel.text + '</span></div>' +
        '<div style="background:' + bannerBg + ';color:' + bannerText + ';padding:20px;border-radius:8px;text-align:center;">' +
          '<div style="font-size:1.1rem;font-weight:600;margin:0 0 4px;">Open Class — Semana 2</div>' +
          '<div style="font-size:.8rem;opacity:.85;margin:0 0 14px;">Sesión sincrónica con el docente</div>' +
          '<div class="palette-row" style="margin:0;justify-content:center;">' +
            '<span class="palette-btn-demo" style="background:' + invertBtnBg + ';color:' + invertBtnText + ';font-size:.8rem;">Unirse</span>' +
            '<span class="palette-btn-demo" style="background:transparent;color:' + bannerText + ';border:1px solid ' + outlineBorder + ';font-size:.8rem;">Ver grabación</span>' +
          '</div>' +
        '</div>' +
      '</div>';
    }

    html += '<h2>Banner de acción</h2><div class="palette-compare">';
    html += banner('light') + banner('dark');
    html += '</div>';

    // ── Muestra de botones en contexto ──
    function buttonsInContext(mode) {
      var m = mode === 'dark' ? p.dark : p.light;
      var pBg = mode === 'dark' ? p.pDarkMode : p.primary;
      var pText = mode === 'dark' ? p.btnPrimaryTextDark : p.btnPrimaryText;
      var sBg = mode === 'dark' ? p.sDarkMode : p.secondary;
      var sText = mode === 'dark' ? p.btnSecondaryTextDark : p.btnSecondaryText;
      var link = mode === 'dark' ? p.pDarkMode : p.primary;

      return '<div class="palette-preview" style="background:' + m.bg + ';">' +
        '<div class="palette-mode-label" style="color:' + m.textSecondary + ';">' + (mode === 'dark' ? 'Dark' : 'Light') + '</div>' +
        '<p style="color:' + m.text + ';font-size:.85rem;margin:0 0 12px;">Selecciona una opción para continuar:</p>' +
        '<div class="palette-row" style="margin:0 0 10px;">' +
          btnDemo(pBg, pText, 'Primario', '') +
          btnDemo(sBg, sText, 'Secundario', '') +
          '<div style="text-align:center;"><div class="palette-btn-demo" style="background:transparent;color:' + link + ';border:1px solid ' + link + ';font-size:.85rem;">Outline</div></div>' +
        '</div>' +
        '<p style="color:' + m.textSecondary + ';font-size:.8rem;margin:0;">Los tres estilos de botón sobre fondo ' + (mode === 'dark' ? 'oscuro' : 'claro') + '</p>' +
      '</div>';
    }

    html += '<h2>Botones en contexto</h2><div class="palette-compare">';
    html += buttonsInContext('light') + buttonsInContext('dark');
    html += '</div>';

    return html;
  }

  // ── Init ──

  function init() {
    var colorsPanel = document.getElementById('panel-colors');
    if (!colorsPanel) return;

    // Verify CSS is loaded by checking if primary-base has a value
    var test = getCSSVar('--ct-primary-base');
    if (!test || test === '') {
      // CSS not ready, retry
      setTimeout(init, 150);
      return;
    }

    var p = readPalette();
    console.log('[palette] dark bg:', p.dark.bg, '| bgRaised:', p.dark.bgRaised, '| bgSubtle:', p.dark.bgSubtle);
    colorsPanel.innerHTML = renderColors(p);
    document.getElementById('panel-tokens').innerHTML = renderTokens(p);
    document.getElementById('panel-buttons').innerHTML = renderButtons(p);
    document.getElementById('panel-structures').innerHTML = renderStructures(p);
  }

  // Run when content loads (MutationObserver for dynamic loading)
  var observer = new MutationObserver(function () {
    if (document.getElementById('panel-colors')) {
      // Wait for stylesheet to apply before reading computed values
      setTimeout(init, 200);
      observer.disconnect();
    }
  });
  observer.observe(document.body, { childList: true, subtree: true });

})();
