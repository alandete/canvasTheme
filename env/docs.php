<?php require_once __DIR__ . '/auth.php'; requireLogin(); setSecurityHeaders(); ?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canvas Themes – Documentación</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="/env/css/docs.css">
</head>
<body>

  <header class="docs-header">
    <div class="docs-header-left">
      <a href="/admin" class="back-link"><i class="fas fa-arrow-left"></i></a>
      <h1><i class="fas fa-book"></i> Documentación Canvas LMS</h1>
    </div>
  </header>

  <div class="docs-layout">

    <!-- Sidebar TOC -->
    <nav class="docs-sidebar">
      <ul class="toc">
        <li class="toc-title">Referencia</li>
        <li><a href="#html-permitido" class="toc-link active"><i class="fas fa-code"></i> HTML Permitido</a></li>
        <li><a href="#html-restringido" class="toc-link"><i class="fas fa-ban"></i> HTML Restringido</a></li>
        <li><a href="#atributos" class="toc-link"><i class="fas fa-tag"></i> Atributos</a></li>
        <li><a href="#css-permitido" class="toc-link"><i class="fas fa-palette"></i> CSS Permitido</a></li>
        <li><a href="#css-restringido" class="toc-link"><i class="fas fa-times-circle"></i> CSS Restringido</a></li>
        <li class="toc-title">Temas y Modos</li>
        <li><a href="#dark-mode" class="toc-link"><i class="fas fa-moon"></i> Modo Oscuro</a></li>
        <li><a href="#high-contrast" class="toc-link"><i class="fas fa-adjust"></i> Alto Contraste</a></li>
        <li><a href="#variables-css" class="toc-link"><i class="fas fa-swatchbook"></i> Variables CSS</a></li>
        <li class="toc-title">Recursos</li>
        <li><a href="#tipografia" class="toc-link"><i class="fas fa-font"></i> Tipografía</a></li>
        <li><a href="#buenas-practicas" class="toc-link"><i class="fas fa-lightbulb"></i> Buenas Prácticas</a></li>
        <li><a href="#fuentes" class="toc-link"><i class="fas fa-link"></i> Fuentes</a></li>
        <li class="toc-title">Ambiente</li>
        <li><a href="#acceso" class="toc-link"><i class="fas fa-key"></i> Acceso y Usuarios</a></li>
        <li><a href="#flujo-trabajo" class="toc-link"><i class="fas fa-project-diagram"></i> Flujo de Trabajo</a></li>
      </ul>
    </nav>

    <!-- Content -->
    <main class="docs-content">

      <!-- ════════════════════════════════════════ -->
      <section id="html-permitido">
        <h2><i class="fas fa-code"></i> HTML Permitido</h2>
        <p>Canvas sanitiza todo el HTML usando <code>canvas_sanitize</code>. Solo las siguientes etiquetas sobreviven al guardado:</p>

        <h3>Estructura y semántica</h3>
        <div class="tag-grid">
          <span class="tag allowed">&lt;a&gt;</span>
          <span class="tag allowed">&lt;article&gt;</span>
          <span class="tag allowed">&lt;aside&gt;</span>
          <span class="tag allowed">&lt;blockquote&gt;</span>
          <span class="tag allowed">&lt;br&gt;</span>
          <span class="tag allowed">&lt;dd&gt;</span>
          <span class="tag allowed">&lt;del&gt;</span>
          <span class="tag allowed">&lt;details&gt;</span>
          <span class="tag allowed">&lt;div&gt;</span>
          <span class="tag allowed">&lt;dl&gt;</span>
          <span class="tag allowed">&lt;dt&gt;</span>
          <span class="tag allowed">&lt;footer&gt;</span>
          <span class="tag allowed">&lt;h2&gt;</span>
          <span class="tag allowed">&lt;h3&gt;</span>
          <span class="tag allowed">&lt;h4&gt;</span>
          <span class="tag allowed">&lt;h5&gt;</span>
          <span class="tag allowed">&lt;h6&gt;</span>
          <span class="tag allowed">&lt;header&gt;</span>
          <span class="tag allowed">&lt;hr&gt;</span>
          <span class="tag allowed">&lt;ins&gt;</span>
          <span class="tag allowed">&lt;li&gt;</span>
          <span class="tag allowed">&lt;map&gt;</span>
          <span class="tag allowed">&lt;mark&gt;</span>
          <span class="tag allowed">&lt;nav&gt;</span>
          <span class="tag allowed">&lt;ol&gt;</span>
          <span class="tag allowed">&lt;p&gt;</span>
          <span class="tag allowed">&lt;pre&gt;</span>
          <span class="tag allowed">&lt;section&gt;</span>
          <span class="tag allowed">&lt;small&gt;</span>
          <span class="tag allowed">&lt;span&gt;</span>
          <span class="tag allowed">&lt;summary&gt;</span>
          <span class="tag allowed">&lt;ul&gt;</span>
        </div>

        <h3>Formato de texto</h3>
        <div class="tag-grid">
          <span class="tag allowed">&lt;b&gt;</span>
          <span class="tag allowed">&lt;big&gt;</span>
          <span class="tag allowed">&lt;cite&gt;</span>
          <span class="tag allowed">&lt;code&gt;</span>
          <span class="tag allowed">&lt;em&gt;</span>
          <span class="tag allowed">&lt;i&gt;</span>
          <span class="tag allowed">&lt;kbd&gt;</span>
          <span class="tag allowed">&lt;strong&gt;</span>
          <span class="tag allowed">&lt;sub&gt;</span>
          <span class="tag allowed">&lt;sup&gt;</span>
          <span class="tag allowed">&lt;u&gt;</span>
          <span class="tag allowed">&lt;var&gt;</span>
        </div>

        <h3>Media</h3>
        <div class="tag-grid">
          <span class="tag allowed">&lt;audio&gt;</span>
          <span class="tag allowed">&lt;embed&gt;</span>
          <span class="tag allowed">&lt;iframe&gt;</span>
          <span class="tag allowed">&lt;img&gt;</span>
          <span class="tag allowed">&lt;object&gt;</span>
          <span class="tag allowed">&lt;picture&gt;</span>
          <span class="tag allowed">&lt;source&gt;</span>
          <span class="tag allowed">&lt;track&gt;</span>
          <span class="tag allowed">&lt;video&gt;</span>
        </div>

        <h3>Tablas</h3>
        <div class="tag-grid">
          <span class="tag allowed">&lt;table&gt;</span>
          <span class="tag allowed">&lt;thead&gt;</span>
          <span class="tag allowed">&lt;tbody&gt;</span>
          <span class="tag allowed">&lt;tfoot&gt;</span>
          <span class="tag allowed">&lt;tr&gt;</span>
          <span class="tag allowed">&lt;th&gt;</span>
          <span class="tag allowed">&lt;td&gt;</span>
          <span class="tag allowed">&lt;col&gt;</span>
          <span class="tag allowed">&lt;colgroup&gt;</span>
          <span class="tag allowed">&lt;caption&gt;</span>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="html-restringido">
        <h2><i class="fas fa-ban"></i> HTML Restringido</h2>
        <p>Estas etiquetas son <strong>eliminadas silenciosamente</strong> al guardar contenido:</p>

        <div class="tag-grid">
          <span class="tag blocked">&lt;script&gt;</span>
          <span class="tag blocked">&lt;style&gt;</span>
          <span class="tag blocked">&lt;link&gt;</span>
          <span class="tag blocked">&lt;meta&gt;</span>
          <span class="tag blocked">&lt;head&gt;</span>
          <span class="tag blocked">&lt;body&gt;</span>
          <span class="tag blocked">&lt;html&gt;</span>
          <span class="tag blocked">&lt;h1&gt;</span>
          <span class="tag blocked">&lt;form&gt;</span>
          <span class="tag blocked">&lt;input&gt;</span>
          <span class="tag blocked">&lt;select&gt;</span>
          <span class="tag blocked">&lt;textarea&gt;</span>
          <span class="tag blocked">&lt;button&gt;</span>
        </div>

        <div class="callout warning">
          <i class="fas fa-exclamation-triangle"></i>
          <div>
            <strong>Importante:</strong> <code>&lt;h1&gt;</code> no está en la lista permitida de Canvas. Usa <code>&lt;h2&gt;</code> como encabezado principal en tus páginas. <code>&lt;style&gt;</code> tampoco se permite en el cuerpo de la página — solo estilos inline con el atributo <code>style=""</code>.
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="atributos">
        <h2><i class="fas fa-tag"></i> Atributos Permitidos</h2>

        <h3>Universales (todos los elementos)</h3>
        <div class="tag-grid">
          <span class="tag attr">style</span>
          <span class="tag attr">class</span>
          <span class="tag attr">id</span>
          <span class="tag attr">title</span>
          <span class="tag attr">role</span>
          <span class="tag attr">lang</span>
          <span class="tag attr">dir</span>
          <span class="tag attr">data-*</span>
        </div>

        <h3>Atributos por elemento</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Elemento</th><th>Atributos</th></tr>
          </thead>
          <tbody>
            <tr><td><code>&lt;a&gt;</code></td><td>href, target, name</td></tr>
            <tr><td><code>&lt;img&gt;</code></td><td>src, alt, height, width, usemap, title, align</td></tr>
            <tr><td><code>&lt;iframe&gt;</code></td><td>src, width, height, name, allowfullscreen</td></tr>
            <tr><td><code>&lt;video&gt;</code></td><td>src, poster, width, height, controls, muted, playsinline</td></tr>
            <tr><td><code>&lt;audio&gt;</code></td><td>src, controls, muted</td></tr>
            <tr><td><code>&lt;table&gt;</code></td><td>summary, width, border, cellpadding, cellspacing</td></tr>
          </tbody>
        </table>

        <h3>Protocolos permitidos en URLs</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Contexto</th><th>Protocolos</th></tr>
          </thead>
          <tbody>
            <tr><td><code>a[href]</code></td><td>http, https, mailto, tel, ftp, skype</td></tr>
            <tr><td><code>img[src]</code>, <code>iframe[src]</code></td><td>http, https, relativo</td></tr>
            <tr><td><code>audio/video</code></td><td>data, http, https, relativo</td></tr>
          </tbody>
        </table>

        <div class="callout danger">
          <i class="fas fa-shield-alt"></i>
          <div>
            <strong>Bloqueado:</strong> El protocolo <code>javascript:</code> siempre es eliminado en cualquier atributo URL.
          </div>
        </div>

        <h3>Atributos data-* bloqueados</h3>
        <div class="tag-grid">
          <span class="tag blocked">data-xml</span>
          <span class="tag blocked">data-method</span>
          <span class="tag blocked">data-turn-into-dialog</span>
          <span class="tag blocked">data-flash-message</span>
          <span class="tag blocked">data-popup-within</span>
          <span class="tag blocked">data-html-tooltip-title</span>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="css-permitido">
        <h2><i class="fas fa-palette"></i> CSS Permitido (inline)</h2>
        <p>Solo se permiten estilos inline (<code>style=""</code>). Las siguientes propiedades y sus variantes son aceptadas:</p>

        <div class="css-grid">
          <div class="css-group">
            <h4>Layout</h4>
            <ul>
              <li><code>display</code> (flex, grid, block, inline, etc.)</li>
              <li><code>position</code> (relative, absolute, fixed, sticky)</li>
              <li><code>top</code>, <code>right</code>, <code>left</code></li>
              <li><code>float</code>, <code>clear</code></li>
              <li><code>z-index</code></li>
              <li><code>visibility</code></li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Flexbox</h4>
            <ul>
              <li><code>flex</code> y todas sus variantes</li>
              <li><code>flex-direction</code></li>
              <li><code>flex-wrap</code></li>
              <li><code>justify-content</code></li>
              <li><code>align-items</code></li>
              <li><code>gap</code></li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Grid</h4>
            <ul>
              <li><code>grid</code> y todas sus variantes</li>
              <li><code>grid-template-columns</code></li>
              <li><code>grid-template-rows</code></li>
              <li><code>grid-row</code>, <code>grid-column</code></li>
              <li><code>gap</code></li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Dimensiones</h4>
            <ul>
              <li><code>width</code>, <code>height</code></li>
              <li><code>max-width</code>, <code>max-height</code></li>
              <li><code>min-width</code>, <code>min-height</code></li>
              <li><code>overflow</code>, <code>overflow-x</code>, <code>overflow-y</code></li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Espaciado</h4>
            <ul>
              <li><code>margin</code> y variantes (-top, -right, etc.)</li>
              <li><code>padding</code> y variantes</li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Borde y fondo</h4>
            <ul>
              <li><code>border</code> y variantes</li>
              <li><code>border-radius</code></li>
              <li><code>background</code> y variantes</li>
              <li><code>background-color</code></li>
              <li><code>background-image</code> (solo http/https)</li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Texto</h4>
            <ul>
              <li><code>color</code></li>
              <li><code>font</code> y variantes</li>
              <li><code>font-family</code> (solo fuentes del sistema)</li>
              <li><code>font-size</code>, <code>font-weight</code></li>
              <li><code>line-height</code></li>
              <li><code>text-align</code></li>
              <li><code>text-decoration</code></li>
              <li><code>text-indent</code></li>
              <li><code>white-space</code></li>
              <li><code>direction</code></li>
            </ul>
          </div>
          <div class="css-group">
            <h4>Otros</h4>
            <ul>
              <li><code>cursor</code></li>
              <li><code>list-style</code></li>
              <li><code>table-layout</code></li>
              <li><code>vertical-align</code></li>
              <li><code>zoom</code></li>
            </ul>
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="css-restringido">
        <h2><i class="fas fa-times-circle"></i> CSS Restringido</h2>
        <p>Las siguientes propiedades <strong>no están en la lista blanca</strong> y son eliminadas:</p>

        <div class="tag-grid">
          <span class="tag blocked">box-shadow</span>
          <span class="tag blocked">text-shadow</span>
          <span class="tag blocked">text-transform</span>
          <span class="tag blocked">opacity</span>
          <span class="tag blocked">transform</span>
          <span class="tag blocked">transition</span>
          <span class="tag blocked">animation</span>
          <span class="tag blocked">@keyframes</span>
          <span class="tag blocked">filter</span>
          <span class="tag blocked">clip-path</span>
          <span class="tag blocked">object-fit</span>
          <span class="tag blocked">letter-spacing</span>
          <span class="tag blocked">word-spacing</span>
          <span class="tag blocked">user-select</span>
          <span class="tag blocked">content</span>
          <span class="tag blocked">--variables CSS</span>
        </div>

        <h3>At-rules no disponibles</h3>
        <p>Como <code>&lt;style&gt;</code> y <code>&lt;link&gt;</code> son eliminados, estas reglas no funcionan en contenido de páginas:</p>
        <div class="tag-grid">
          <span class="tag blocked">@import</span>
          <span class="tag blocked">@font-face</span>
          <span class="tag blocked">@keyframes</span>
          <span class="tag blocked">@media</span>
          <span class="tag blocked">@supports</span>
        </div>

        <div class="callout info">
          <i class="fas fa-info-circle"></i>
          <div>
            <strong>Excepción:</strong> Los administradores pueden subir archivos CSS/JS personalizados a nivel de cuenta desde <strong>Admin > Temas > Subir CSS/JS</strong>. Estos archivos se inyectan en el <code>&lt;head&gt;</code> y pueden usar cualquier propiedad CSS y at-rules sin restricción.
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="dark-mode">
        <h2><i class="fas fa-moon"></i> Modo Oscuro</h2>

        <div class="callout warning">
          <i class="fas fa-exclamation-triangle"></i>
          <div>
            <strong>Estado actual:</strong> Canvas <strong>NO tiene modo oscuro nativo</strong> para la interfaz web (hasta 2026). Instructure ha indicado que está "en exploración activa". Las apps móviles (iOS/Android) sí lo soportan.
          </div>
        </div>

        <h3>Cómo funciona en móvil</h3>
        <ul class="docs-list">
          <li>Invierte el esquema de colores claro a oscuro</li>
          <li>Usa tonos oscuros refinados (no negro puro <code>#000</code>)</li>
          <li>Transforma los colores institucionales</li>
          <li><strong>NO ajusta</strong> colores hardcodeados en el contenido personalizado — solo invierte blanco/negro básico</li>
        </ul>

        <h3>Mejores prácticas para compatibilidad</h3>

        <div class="practice-card good">
          <h4><i class="fas fa-check-circle"></i> Hacer</h4>
          <ul>
            <li>Heredar colores del contenedor padre (no fijar colores)</li>
            <li>Si fijas colores, asegurar contraste suficiente en ambos modos</li>
            <li>Usar colores con buen contraste sobre fondos claros y oscuros</li>
            <li>Probar invirtiendo los colores manualmente para simular modo oscuro móvil</li>
          </ul>
        </div>

        <div class="practice-card bad">
          <h4><i class="fas fa-times-circle"></i> Evitar</h4>
          <ul>
            <li><code>color: #000000</code> o <code>color: black</code> — no se adapta</li>
            <li><code>background-color: #ffffff</code> o <code>background: white</code></li>
            <li>Depender de <code>prefers-color-scheme</code> — Canvas web no lo soporta</li>
            <li>Asumir que existe una clase CSS o atributo <code>data-</code> para detectar dark mode</li>
          </ul>
        </div>

        <h3>Estrategia para nuestros proyectos</h3>
        <p>Usar <strong>solo variables de Canvas</strong> (<code>--ic-brand-*</code>) para que los diseños se adapten automáticamente cuando Canvas cambie de tema o implemente dark mode:</p>
        <pre><code>/* CORRECTO: usa variables de Canvas con fallback */
#content-body .mi-titulo {
  color: var(--ic-brand-font-color-dark, #2D3B45);
}

#content-body .mi-link {
  color: var(--ic-link-color, #0374B5);
}

#content-body .mi-boton {
  background: var(--ic-brand-primary, #0374B5);
  color: #fff;
}

/* Texto secundario: usar opacity sobre el color heredado */
#content-body .mi-descripcion {
  color: var(--ic-brand-font-color-dark, #2D3B45);
  opacity: 0.7;
}

/* Bordes: usar currentColor con opacity */
#content-body .mi-card {
  border: 1px solid currentColor;
  border-opacity: 0.12; /* no existe, usar alternativa: */
  border: 1px solid rgba(0, 0, 0, 0.12);
}</code></pre>

        <div class="callout danger">
          <i class="fas fa-exclamation-circle"></i>
          <div>
            <strong>NO hacer:</strong> No crear variables propias (<code>--mi-color</code>) con colores hardcodeados. Canvas no las conoce y no se adaptarán al cambiar de tema. Siempre usar <code>var(--ic-brand-*)</code> o <code>opacity</code> para variaciones.
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="high-contrast">
        <h2><i class="fas fa-adjust"></i> Alto Contraste</h2>
        <p>Canvas incluye un modo <strong>High Contrast UI</strong> que los usuarios activan desde <strong>Cuenta > Configuración > Opciones de funciones</strong>.</p>

        <h3>Qué hace</h3>
        <ul class="docs-list">
          <li>Aumenta el contraste de colores en texto, botones y elementos UI</li>
          <li>Apunta a cumplir <strong>WCAG 2.1 Nivel AAA</strong></li>
          <li>Carga un bundle CSS alternativo (<code>new_styles_high_contrast</code>)</li>
          <li>Puede sobreescribir <code>--ic-brand-primary</code> y <code>--ic-link-color</code></li>
        </ul>

        <div class="callout info">
          <i class="fas fa-info-circle"></i>
          <div>
            Para detectar alto contraste en JavaScript del admin, se puede revisar <code>ENV.use_high_contrast</code> en el objeto global de Canvas.
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="variables-css">
        <h2><i class="fas fa-swatchbook"></i> Variables CSS de Canvas</h2>
        <p>Disponibles en archivos CSS subidos a nivel de admin. <strong>No funcionan en estilos inline.</strong></p>

        <div class="callout info">
          <i class="fas fa-info-circle"></i>
          <div>
            <strong>Estrategia de variables:</strong> Separar en dos grupos: <strong>variables de Canvas</strong> (sobreescritura de <code>--ic-brand-*</code>) y <strong>variables propias</strong> (<code>--ct-*</code>) para lo que Canvas no define. Ambas deben tener versión dark en <code>@media (prefers-color-scheme: dark)</code>.
          </div>
        </div>

        <h3>Variables que afectan el área de contenido</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Variable</th><th>Default</th><th>Qué afecta</th></tr>
          </thead>
          <tbody>
            <tr><td><code>--ic-brand-primary</code></td><td>#0374B5</td><td>Color primario, estados activos, acentos</td></tr>
            <tr><td><code>--ic-brand-font-color-dark</code></td><td>#2D3B45</td><td>Todo el texto del body en páginas de contenido</td></tr>
            <tr><td><code>--ic-link-color</code></td><td>#0374B5</td><td>Todos los enlaces en páginas, tareas, foros</td></tr>
            <tr><td><code>--ic-brand-button--primary-bgd</code></td><td>#0374B5</td><td>Fondo de botones primarios (Submit, Save)</td></tr>
            <tr><td><code>--ic-brand-button--primary-text</code></td><td>#FFFFFF</td><td>Texto de botones primarios</td></tr>
          </tbody>
        </table>

        <h3>Variables derivadas (auto-generadas)</h3>
        <p>Canvas genera variantes automáticas. No se configuran directamente:</p>
        <table class="docs-table">
          <thead>
            <tr><th>Variable</th><th>Base</th><th>Transformación</th></tr>
          </thead>
          <tbody>
            <tr><td><code>--ic-brand-primary-darkened-5</code></td><td>primary</td><td>5% más oscuro</td></tr>
            <tr><td><code>--ic-brand-primary-darkened-10</code></td><td>primary</td><td>10% más oscuro</td></tr>
            <tr><td><code>--ic-brand-primary-lightened-5</code></td><td>primary</td><td>5% más claro</td></tr>
            <tr><td><code>--ic-brand-primary-lightened-15</code></td><td>primary</td><td>15% más claro</td></tr>
            <tr><td><code>--ic-brand-font-color-dark-lightened-15</code></td><td>font-color-dark</td><td>15% más claro</td></tr>
            <tr><td><code>--ic-brand-font-color-dark-lightened-30</code></td><td>font-color-dark</td><td>~28% más claro</td></tr>
            <tr><td><code>--ic-link-color-darkened-10</code></td><td>link-color</td><td>10% más oscuro</td></tr>
            <tr><td><code>--ic-link-color-lightened-10</code></td><td>link-color</td><td>10% más claro</td></tr>
          </tbody>
        </table>

        <h3>Variables de navegación (no afectan contenido)</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Variable</th><th>Descripción</th></tr>
          </thead>
          <tbody>
            <tr><td><code>--ic-brand-global-nav-bgd</code></td><td>Fondo de la navegación lateral</td></tr>
            <tr><td><code>--ic-brand-global-nav-ic-icon-svg-fill</code></td><td>Color de íconos</td></tr>
            <tr><td><code>--ic-brand-global-nav-menu-item__text-color</code></td><td>Texto del menú</td></tr>
            <tr><td><code>--ic-brand-global-nav-logo-bgd</code></td><td>Fondo del logo</td></tr>
          </tbody>
        </table>

        <h3>Estructura recomendada para proyectos</h3>
        <pre><code>/* 1. Sobreescritura de Canvas (si la marca lo requiere) */
:root {
  --ic-brand-primary: #E63946;
  --ic-brand-font-color-dark: #2D3B45;
  --ic-link-color: #E63946;
}

/* 2. Variables propias (lo que Canvas no define) */
:root {
  --ct-accent: #457B9D;
  --ct-bg-card: #FFFFFF;
  --ct-border: rgba(0, 0, 0, 0.12);
}

/* 3. Dark mode */
@media (prefers-color-scheme: dark) {
  :root {
    --ic-brand-primary: #FF6B7A;
    --ic-brand-font-color-dark: #E0E0E0;
    --ic-link-color: #FF6B7A;

    --ct-accent: #6BA3C7;
    --ct-bg-card: #1E1E1E;
    --ct-border: rgba(255, 255, 255, 0.12);
  }
}

/* Para el ambiente de pruebas (no subir a Canvas) */
html[data-theme="dark"] {
  /* mismos valores que el @media de arriba */
}</code></pre>

        <h3>API</h3>
        <pre><code>GET /api/v1/brand_variables</code></pre>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="tipografia">
        <h2><i class="fas fa-font"></i> Tipografía</h2>

        <h3>Font stack por defecto</h3>
        <pre><code>"LatoWeb", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif</code></pre>

        <h3>Uso de fuentes web</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Contexto</th><th>¿Fuentes web?</th><th>Detalle</th></tr>
          </thead>
          <tbody>
            <tr>
              <td>Contenido de páginas</td>
              <td><span class="badge no">No</span></td>
              <td>No se pueden cargar @font-face ni Google Fonts (sin &lt;style&gt; ni &lt;link&gt;)</td>
            </tr>
            <tr>
              <td>CSS de admin</td>
              <td><span class="badge yes">Sí</span></td>
              <td>Se puede usar @font-face y @import en archivos subidos a nivel de cuenta</td>
            </tr>
            <tr>
              <td>Inline font-family</td>
              <td><span class="badge partial">Parcial</span></td>
              <td>Solo fuentes del sistema ya instaladas en el dispositivo del usuario</td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="buenas-practicas">
        <h2><i class="fas fa-lightbulb"></i> Buenas Prácticas</h2>

        <div class="practice-grid">
          <div class="practice-card good">
            <h4><i class="fas fa-check-circle"></i> Estructura</h4>
            <ul>
              <li>Usar <code>&lt;h2&gt;</code> como encabezado principal (no <code>&lt;h1&gt;</code>)</li>
              <li>Prefijar selectores CSS con <code>#content-body</code> para evitar colisiones</li>
              <li>Usar solo HTML sin <code>&lt;html&gt;</code>, <code>&lt;head&gt;</code>, <code>&lt;body&gt;</code></li>
              <li>Mantener la jerarquía semántica de encabezados</li>
            </ul>
          </div>

          <div class="practice-card good">
            <h4><i class="fas fa-check-circle"></i> CSS</h4>
            <ul>
              <li>Usar <code>var(--ic-brand-font-color-dark)</code> para texto</li>
              <li>Usar <code>var(--ic-link-color)</code> para enlaces</li>
              <li>Usar <code>var(--ic-brand-primary)</code> para acentos</li>
              <li>Usar <code>opacity</code> para variaciones de color (secundario, muted)</li>
              <li>No crear variables propias con colores hardcodeados</li>
              <li>Usar unidades relativas (<code>em</code>, <code>rem</code>, <code>%</code>)</li>
            </ul>
          </div>

          <div class="practice-card good">
            <h4><i class="fas fa-check-circle"></i> Accesibilidad</h4>
            <ul>
              <li>Incluir atributos <code>alt</code> en imágenes</li>
              <li>Usar atributos <code>role</code> y <code>aria-*</code> cuando corresponda</li>
              <li>Asegurar contraste mínimo 4.5:1 para texto</li>
              <li>No depender solo del color para transmitir información</li>
            </ul>
          </div>

          <div class="practice-card bad">
            <h4><i class="fas fa-times-circle"></i> Evitar</h4>
            <ul>
              <li>No usar <code>&lt;h1&gt;</code> — Canvas lo elimina</li>
              <li>No usar <code>&lt;style&gt;</code> en el contenido de páginas</li>
              <li>No crear variables CSS propias (<code>--mi-var: #color</code>)</li>
              <li>No hardcodear colores de texto — usar <code>var(--ic-brand-*)</code></li>
              <li>No depender de <code>box-shadow</code>, <code>transition</code>, <code>animation</code></li>
              <li>No asumir que las fuentes web estarán disponibles</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="fuentes">
        <h2><i class="fas fa-link"></i> Fuentes de Referencia</h2>
        <ul class="docs-list sources">
          <li><a href="https://community.instructure.com/t5/Canvas-Resource-Documents/Canvas-HTML-Editor-Allowlist/ta-p/387066" target="_blank">Canvas HTML Editor Allowlist – Instructure Community</a></li>
          <li><a href="https://github.com/instructure/canvas-lms/blob/master/gems/canvas_sanitize/lib/canvas_sanitize/canvas_sanitize.rb" target="_blank">canvas_sanitize.rb – GitHub Source</a></li>
          <li><a href="https://community.canvaslms.com/t5/Canvas-Question-Forum/Is-there-also-a-CSS-whitelist/m-p/188731" target="_blank">CSS Whitelist – Community Discussion</a></li>
          <li><a href="https://canvas.instructure.com/doc/api/brand_configs.html" target="_blank">Brand Configs API Documentation</a></li>
          <li><a href="https://community.canvaslms.com/t5/Canvas-Basics-Guide/How-do-I-enable-the-high-contrast-user-interface-in-Canvas/ta-p/615334" target="_blank">High Contrast UI – Canvas Guide</a></li>
          <li><a href="https://community.canvaslms.com/thread/13604-what-is-the-default-font-family-in-canvas" target="_blank">Default Font Family – Community</a></li>
          <li><a href="https://community.canvaslms.com/t5/Admin-Guide/How-do-I-upload-custom-JavaScript-and-CSS-files-to-an-account/ta-p/253" target="_blank">Upload Custom CSS/JS – Admin Guide</a></li>
        </ul>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="acceso">
        <h2><i class="fas fa-key"></i> Acceso y Usuarios</h2>

        <h3>Roles</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Rol</th><th>Puede hacer</th></tr>
          </thead>
          <tbody>
            <tr><td><strong>Admin</strong></td><td>Crear, editar, eliminar y compilar proyectos. Gestionar usuarios y contraseñas.</td></tr>
            <tr><td><strong>Invitado</strong></td><td>Ver proyectos, código fuente y documentación. Sin permisos de escritura.</td></tr>
          </tbody>
        </table>

        <h3>Configuración inicial</h3>
        <p>La primera vez que se accede al ambiente, se muestra un asistente de configuración donde se crea el usuario admin, opcionalmente un invitado y un correo de recuperación.</p>

        <h3>Cambiar contraseñas</h3>
        <p>El admin puede cambiar las contraseñas de todos los usuarios desde <strong>Admin → Usuarios</strong>.</p>

        <h3>Recuperar acceso</h3>
        <p>Si olvidaste tu contraseña, accede a <a href="/admin/reset-password"><code>/admin/reset-password</code></a> desde el enlace en la página de login. Hay tres opciones:</p>
        <table class="docs-table">
          <thead>
            <tr><th>Opción</th><th>Qué hace</th><th>Proyectos</th></tr>
          </thead>
          <tbody>
            <tr><td><strong>Correo de recuperación</strong></td><td>Resetea la contraseña del admin a <code>admin2026</code></td><td>Se conservan</td></tr>
            <tr><td><strong>Reiniciar usuarios</strong></td><td>Elimina usuarios, vuelve al asistente inicial</td><td>Se conservan</td></tr>
            <tr><td><strong>Reinicio de fábrica</strong></td><td>Elimina usuarios y todos los proyectos (excepto demo)</td><td>Se eliminan</td></tr>
          </tbody>
        </table>

        <h3>Transferir a otro usuario</h3>
        <ul class="docs-list">
          <li><strong>Conservando proyectos:</strong> "Reiniciar usuarios" → el nuevo usuario configura sus datos y tiene acceso a los proyectos existentes.</li>
          <li><strong>Ambiente limpio:</strong> "Reinicio de fábrica" → el nuevo usuario empieza desde cero solo con el proyecto demo.</li>
        </ul>
      </section>

      <!-- ════════════════════════════════════════ -->
      <section id="flujo-trabajo">
        <h2><i class="fas fa-project-diagram"></i> Flujo de Trabajo</h2>

        <h3>Estructura de archivos</h3>
        <table class="docs-table">
          <thead>
            <tr><th>Archivo</th><th>Descripción</th></tr>
          </thead>
          <tbody>
            <tr><td><code>slug-master.css</code></td><td>Archivo de trabajo. Se edita aquí y se compila para generar los otros dos.</td></tr>
            <tr><td><code>slug-mobile.css</code></td><td>Generado. Contiene estilos hasta 992px. Para subir a Canvas (móvil/tablet).</td></tr>
            <tr><td><code>slug-desktop.css</code></td><td>Generado. Contiene todos los estilos. Para subir a Canvas (desktop).</td></tr>
          </tbody>
        </table>

        <h3>Proceso</h3>
        <ol class="docs-list">
          <li>Crear un proyecto desde <strong>Admin → Proyectos → Nuevo</strong></li>
          <li>Editar el archivo <code>master.css</code> con tus estilos</li>
          <li>Editar los archivos HTML de las páginas</li>
          <li>Previsualizar en el ambiente (desktop y móvil)</li>
          <li>Compilar CSS (botón <i class="fas fa-cogs"></i> en el ambiente o en Admin)</li>
          <li>Copiar el código limpio desde el visor de código (<i class="fas fa-code"></i>)</li>
          <li>Subir a Canvas</li>
        </ol>

        <h3>Dark mode</h3>
        <p>El archivo master incluye tres bloques para dark mode:</p>
        <ul class="docs-list">
          <li><code>@media (prefers-color-scheme: dark)</code> — funciona en Canvas real cuando el dispositivo tiene dark mode activado.</li>
          <li><code>html[data-theme="dark"]</code> — solo para el ambiente de pruebas. Se elimina automáticamente al compilar.</li>
        </ul>

        <h3>Variables CSS</h3>
        <p>Los colores de la marca se definen como <code>-base</code> al inicio del master. Todo lo demás hereda de ellos:</p>
        <ul class="docs-list">
          <li><code>--ct-primary-base</code>, <code>--ct-secondary-base</code> — colores principales</li>
          <li><code>--ct-accent-1-base</code> a <code>--ct-accent-3-base</code> — colores de acento</li>
          <li><code>--ct-primary</code>, <code>--ct-accent-1</code>, etc. — colores de uso (se aclaran en dark mode automáticamente)</li>
          <li><code>--ct-gray-000</code> a <code>--ct-gray-1000</code> — paleta de grises con <code>color-mix()</code></li>
        </ul>
        <p>Para personalizar un proyecto, solo cambia los 5 valores <code>-base</code> al inicio del master. El dark mode, los grises y las sombras se adaptan solos.</p>
      </section>

    </main>
  </div>

  <script src="/env/js/docs.js"></script>
</body>
</html>
