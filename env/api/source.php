<?php
require_once __DIR__ . '/../auth.php';
requireLoginApi();
header('Content-Type: application/json; charset=utf-8');

$project = $_GET['project'] ?? '';
$page    = $_GET['page'] ?? 'index';

if (!preg_match('/^[a-z0-9\-_]+$/i', $project)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro inválido']);
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
    $htmlFile = $projectPath . '/index.html';
} else {
    if (!preg_match('/^[a-z0-9\-_]+$/i', $page)) {
        http_response_code(400);
        echo json_encode(['error' => 'Página inválida']);
        exit;
    }
    $htmlFile = $projectPath . '/pages/' . $page . '.html';
}

$masterFile  = $projectPath . '/css/' . $project . '-master.css';
$mobileFile  = $projectPath . '/css/' . $project . '-mobile.css';
$desktopFile = $projectPath . '/css/' . $project . '-desktop.css';

$result = [
    'html'       => file_exists($htmlFile)    ? file_get_contents($htmlFile)    : '',
    'cssMaster'  => file_exists($masterFile)  ? file_get_contents($masterFile)  : '',
    'css'        => file_exists($mobileFile)  ? file_get_contents($mobileFile)  : '',
    'cssDesktop' => file_exists($desktopFile) ? file_get_contents($desktopFile) : '',
    'js'         => file_exists($projectPath . '/js/' . $project . '-scripts.js')
                     ? file_get_contents($projectPath . '/js/' . $project . '-scripts.js')
                     : (file_exists($projectPath . '/js/scripts.js') ? file_get_contents($projectPath . '/js/scripts.js') : '')
];

echo json_encode($result, JSON_UNESCAPED_UNICODE);
