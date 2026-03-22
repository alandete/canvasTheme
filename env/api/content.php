<?php
require_once __DIR__ . '/../auth.php';
requireLoginApi();
header('Content-Type: application/json; charset=utf-8');

$project = $_GET['project'] ?? '';
$page    = $_GET['page'] ?? 'index';

if (!preg_match('/^[a-z0-9\-_]+$/i', $project) || !preg_match('/^[a-z0-9\-_]+$/i', $page)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $project;

if (!$projectsBase || !is_dir($projectPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Proyecto no encontrado']);
    exit;
}

if ($page === 'index') {
    $filePath = $projectPath . '/index.html';
} else {
    $filePath = $projectPath . '/pages/' . $page . '.html';
}

if (!file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Página no encontrada']);
    exit;
}

$html = file_get_contents($filePath);
$basePath = '../projects/' . $project;

$mobileFile  = $projectPath . '/css/' . $project . '-mobile.css';
$desktopFile = $projectPath . '/css/' . $project . '-desktop.css';

$result = [
    'html'           => $html,
    'project'        => $project,
    'page'           => $page,
    'cssPath'        => file_exists($mobileFile)  ? $basePath . '/css/' . basename($mobileFile)  : null,
    'cssDesktopPath' => file_exists($desktopFile)  ? $basePath . '/css/' . basename($desktopFile) : null,
    'jsPath'         => file_exists($projectPath . '/js/scripts.js') ? $basePath . '/js/scripts.js' : null
];

echo json_encode($result, JSON_UNESCAPED_UNICODE);
