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

        // Only include if it has an index.html
        if (!file_exists($projPath . '/index.html')) {
            continue;
        }

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

        // Detect CSS files
        $hasCss = file_exists($projPath . '/css/' . $slug . '-mobile.css')
                  || file_exists($projPath . '/css/' . $slug . '-master.css');
        $hasMaster = file_exists($projPath . '/css/' . $slug . '-master.css');

        $projects[] = [
            'slug'      => $slug,
            'name'      => $name,
            'pages'     => $pages,
            'hasCss'    => $hasCss,
            'hasMaster' => $hasMaster,
            'hasJs'     => file_exists($projPath . '/js/scripts.js'),
            'protected' => false
        ];
    }
}

echo json_encode(['projects' => $projects], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
