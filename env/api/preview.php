<?php
/**
 * Preview — Renderiza contenido del proyecto como página completa.
 * Se usa dentro del iframe del simulador móvil.
 */
require_once __DIR__ . '/../auth.php';
requireLoginApi();

$project = $_GET['project'] ?? '';
$page    = $_GET['page'] ?? 'index';
$theme   = $_GET['theme'] ?? 'light';

if (!preg_match('/^[a-z0-9\-_]+$/i', $project) || !preg_match('/^[a-z0-9\-_]+$/i', $page)) {
    http_response_code(400);
    echo 'Parámetros inválidos';
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $project;

if (!$projectsBase || !is_dir($projectPath)) {
    http_response_code(404);
    echo 'Proyecto no encontrado';
    exit;
}

if ($page === 'index') {
    $filePath = $projectPath . '/index.html';
} elseif ($page === 'snippets') {
    $filePath = $projectPath . '/snippets.html';
} else {
    $filePath = $projectPath . '/pages/' . $page . '.html';
}

if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'Página no encontrada';
    exit;
}

$html = file_get_contents($filePath);
$basePath = '/projects/' . $project;

$masterFile  = $projectPath . '/css/' . $project . '-master.css';
$mobileFile  = $projectPath . '/css/' . $project . '-mobile.css';
$desktopFile = $projectPath . '/css/' . $project . '-desktop.css';
$jsSlugFile  = $projectPath . '/js/' . $project . '-scripts.js';
$jsFile      = file_exists($jsSlugFile) ? $jsSlugFile : $projectPath . '/js/scripts.js';

$dataTheme = ($theme === 'dark') ? ' data-theme="dark"' : '';
?>
<!DOCTYPE html>
<html lang="es"<?= $dataTheme ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preview</title>
  <?php if (file_exists($masterFile)): ?>
  <link rel="stylesheet" href="<?= $basePath ?>/css/<?= basename($masterFile) ?>">
  <?php else: ?>
  <?php if (file_exists($mobileFile)): ?>
  <link rel="stylesheet" href="<?= $basePath ?>/css/<?= basename($mobileFile) ?>">
  <?php endif; ?>
  <?php if (file_exists($desktopFile)): ?>
  <link rel="stylesheet" href="<?= $basePath ?>/css/<?= basename($desktopFile) ?>">
  <?php endif; ?>
  <?php endif; ?>
  <style>
    body { margin: 0; padding: 0 1rem; }
  </style>
</head>
<body>
  <?= $html ?>
  <?php if (file_exists($jsFile)): ?>
  <script src="<?= $basePath ?>/js/<?= basename($jsFile) ?>"></script>
  <?php endif; ?>
</body>
</html>
