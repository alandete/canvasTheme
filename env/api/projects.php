<?php
require_once __DIR__ . '/../auth.php';
requireLoginApi();
header('Content-Type: application/json; charset=utf-8');

$projectsDir = realpath(__DIR__ . '/../../projects');
$projects = [];

if ($projectsDir && is_dir($projectsDir)) {
    $dirs = array_filter(scandir($projectsDir), function ($d) use ($projectsDir) {
        return $d !== '.' && $d !== '..' && is_dir($projectsDir . '/' . $d);
    });

    foreach ($dirs as $slug) {
        $projPath = $projectsDir . '/' . $slug;

        // Only include if it has an index.html and is not disabled
        if (!file_exists($projPath . '/index.html')) {
            continue;
        }
        $isActive = !file_exists($projPath . '/.disabled');

        $name = ucwords(str_replace(['-', '_'], ' ', $slug));

        // Scan pages
        $pages = [];
        $pages[] = ['slug' => 'index', 'name' => 'Inicio', 'file' => 'index.html'];

        $pagesDir = $projPath . '/pages';
        if (is_dir($pagesDir)) {
            $htmlFiles = glob($pagesDir . '/*.html');
            $orgPages = [];
            $actPages = [];

            foreach ($htmlFiles as $f) {
                $filename = basename($f, '.html');
                $page = [
                    'slug' => $filename,
                    'name' => ucwords(str_replace(['-', '_'], ' ', $filename)),
                    'file' => 'pages/' . basename($f)
                ];
                // Organización (semana-01, modulo-02, unidad-03)
                if (preg_match('/^(semana|modulo|unidad)-\d+$/', $filename)) {
                    $orgPages[] = $page;
                } else {
                    $actPages[] = $page;
                }
            }

            // Orden: organización primero (ya viene alfabético), luego actividades
            $pages = array_merge($pages, $orgPages, $actPages);
        }

        // Palette antes de snippets
        if (file_exists($projPath . '/palette.html')) {
            $pages[] = ['slug' => 'palette', 'name' => 'Paleta de colores', 'file' => 'palette.html'];
        }

        // Snippets al final (solo desktop)
        if (file_exists($projPath . '/snippets.html')) {
            $pages[] = ['slug' => 'snippets', 'name' => 'Snippets', 'file' => 'snippets.html'];
        }

        // Detect CSS files
        $hasCss = file_exists($projPath . '/css/' . $slug . '-mobile.css')
                  || file_exists($projPath . '/css/' . $slug . '-master.css');
        $hasMaster = file_exists($projPath . '/css/' . $slug . '-master.css');

        // Read colors and CDNs from master CSS
        $colors = ['primary' => '#0374B5', 'secondary' => '#2D3B45'];
        $cdns = [];
        $masterPath = $projPath . '/css/' . $slug . '-master.css';
        if (file_exists($masterPath)) {
            $masterContent = file_get_contents($masterPath);
            if (preg_match('/--ct-primary-base:\s*(#[0-9A-Fa-f]{6})/', $masterContent, $m)) {
                $colors['primary'] = $m[1];
            }
            if (preg_match('/--ct-secondary-base:\s*(#[0-9A-Fa-f]{6})/', $masterContent, $m)) {
                $colors['secondary'] = $m[1];
            }
            // Also check legacy variable names (--up- prefix)
            if (preg_match('/--up-primario-1:\s*(#[0-9A-Fa-f]{6})/', $masterContent, $m)) {
                $colors['primary'] = $m[1];
            }
            if (preg_match('/--up-primario-2:\s*(#[0-9A-Fa-f]{6})/', $masterContent, $m)) {
                $colors['secondary'] = $m[1];
            }
            // Detect CDNs
            if (strpos($masterContent, 'bootstrap@') !== false || strpos($masterContent, 'bootstrap.min.css') !== false) $cdns[] = 'bootstrap';
            if (strpos($masterContent, 'bootstrap-icons') !== false) $cdns[] = 'bootstrap-icons';
            if (strpos($masterContent, 'font-awesome') !== false) $cdns[] = 'fontawesome';
            if (strpos($masterContent, 'animate.css') !== false || strpos($masterContent, 'animate.min.css') !== false) $cdns[] = 'animate';
        }

        // Detect organization type from existing pages
        $orgType = 'none';
        $orgCount = 0;
        if (is_dir($pagesDir)) {
            foreach (['semana' => 'semanas', 'modulo' => 'modulos', 'unidad' => 'unidades'] as $prefix => $type) {
                $orgFiles = glob($pagesDir . '/' . $prefix . '-*.html');
                if (count($orgFiles) > 0) {
                    $orgType = $type;
                    $orgCount = count($orgFiles);
                    break;
                }
            }
        }

        $projects[] = [
            'slug'      => $slug,
            'name'      => $name,
            'pages'     => $pages,
            'hasCss'    => $hasCss,
            'hasMaster' => $hasMaster,
            'hasJs'     => file_exists($projPath . '/js/' . $slug . '-scripts.js') || file_exists($projPath . '/js/scripts.js'),
            'protected' => false,
            'active'    => $isActive,
            'colors'    => $colors,
            'cdns'      => $cdns,
            'orgType'   => $orgType,
            'orgCount'  => $orgCount
        ];
    }
}

echo json_encode(['projects' => $projects], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
