# Canvas Themes

Ambiente de desarrollo para diseñar y previsualizar temas personalizados para **Canvas LMS** (Instructure).

Permite crear proyectos con HTML y CSS optimizados para Canvas, previsualizarlos en un entorno que simula el aula virtual, compilar archivos para móvil y desktop, y exportar código limpio listo para subir.

## Características

- **Simulador Canvas** — Interfaz que replica la estructura del aula virtual (barra de navegación, menú del curso, área de contenido, columna de estado)
- **Simulador móvil** — Vista previa en dispositivos móvil y tablet con orientación vertical/horizontal y modo oscuro
- **Sistema de proyectos** — Cada proyecto tiene su HTML, CSS y JS separados del ambiente
- **CSS Master** — Archivo único de trabajo que se compila automáticamente en versiones mobile y desktop
- **Dark mode** — Soporte para `prefers-color-scheme: dark` y simulación en el ambiente de pruebas
- **Variables CSS** — Sistema de variables con `color-mix()` que se adapta automáticamente entre modos claro y oscuro
- **Visor de código** — Muestra el código limpio (sin elementos del ambiente) listo para copiar
- **Autenticación** — Login con roles Admin y Guest
- **Documentación** — Referencia integrada de HTML/CSS permitido en Canvas, variables y buenas prácticas

## Requisitos

| Requisito | Valor |
|---|---|
| PHP | 7.0+ (recomendado 7.4+) |
| Servidor web | Apache, Nginx o LiteSpeed |
| Base de datos | No requiere |
| Composer | No requiere |
| Directorio con escritura | `projects/` y `env/config/` |

## Instalación

1. Clonar el repositorio en el directorio del servidor web:
   ```bash
   git clone https://github.com/alandete/canvasTheme.git CanvasThemes
   ```

2. Acceder desde el navegador:
   ```
   http://localhost/CanvasThemes/
   ```

3. Completar el asistente de configuración inicial (crear usuario admin).

## Estructura

```
CanvasThemes/
├── env/                    # Ambiente de desarrollo
│   ├── api/                # APIs PHP (proyectos, compilación, auth)
│   ├── config/             # Configuración de usuarios (JSON)
│   ├── css/                # Estilos del ambiente
│   ├── js/                 # JavaScript del ambiente
│   ├── templates/          # Plantillas base para nuevos proyectos
│   │   ├── css/master.css  # Plantilla CSS con variables
│   │   ├── pages/          # Plantillas HTML de páginas
│   │   └── js/             # Plantilla JS
│   ├── index.php           # Ambiente principal
│   ├── admin.php           # Administración (proyectos + usuarios)
│   ├── docs.php            # Documentación Canvas
│   ├── auth.php            # Sistema de autenticación
│   ├── setup.php           # Configuración inicial
│   └── reset-password.php  # Recuperación de acceso
├── projects/               # Proyectos de usuario
│   └── {nombre}/
│       ├── index.html
│       ├── pages/
│       ├── css/
│       │   ├── {nombre}-master.css
│       │   ├── {nombre}-mobile.css   (generado)
│       │   └── {nombre}-desktop.css  (generado)
│       └── js/
└── index.php               # Redirect a env/
```

## Flujo de trabajo

1. **Crear proyecto** desde Admin → Nuevo Proyecto
2. **Editar** el archivo `master.css` con los estilos del proyecto
3. **Editar** los archivos HTML de las páginas
4. **Previsualizar** en el ambiente (desktop y móvil)
5. **Compilar** CSS (master → mobile + desktop)
6. **Copiar** el código limpio desde el visor de código
7. **Subir** a Canvas LMS

## Variables CSS

Los proyectos usan un sistema de variables que separa los colores de la marca de los colores de uso:

```css
:root {
  /* Editar solo estos */
  --ct-primary-base: #0374B5;
  --ct-secondary-base: #2D3B45;
  --ct-accent-1-base: #E63946;
  --ct-accent-2-base: #457B9D;
  --ct-accent-3-base: #2B9348;
}
```

La paleta de grises se genera con `color-mix()` y se adapta automáticamente en dark mode invirtiendo los extremos. Las sombras, bordes y fondos heredan de la paleta.

## Compilación CSS

El archivo master contiene todo: variables, dark mode, estilos base y media queries. Al compilar se generan:

- **mobile** — Todo hasta 992px, sin bloque de pruebas `html[data-theme="dark"]`
- **desktop** — Todo completo, sin bloque de pruebas

## Seguridad

- Autenticación con sesiones PHP y contraseñas hasheadas (bcrypt)
- CSRF tokens en todas las operaciones POST
- Rate limiting por sesión
- Protección contra path traversal
- Headers de seguridad (X-Content-Type-Options, X-Frame-Options)
- `.htaccess` para bloquear acceso directo a archivos sensibles

## Recuperación de acceso

Si se pierden las credenciales, acceder a `reset-password.php`:

- **Con correo** — Resetea la contraseña del admin
- **Reiniciar usuarios** — Elimina usuarios, conserva proyectos
- **Reinicio de fábrica** — Elimina todo y vuelve al setup inicial

## Licencia

Uso interno.
