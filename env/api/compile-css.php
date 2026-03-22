<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('compile', 20);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input   = json_decode(file_get_contents('php://input'), true);
$project = trim($input['project'] ?? '');

if (!preg_match('/^[a-z0-9\-_]+$/i', $project)) {
    http_response_code(400);
    echo json_encode(['error' => 'Proyecto inválido']);
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $project;

if (!$projectsBase || !is_dir($projectPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Proyecto no encontrado']);
    exit;
}

$masterFile = $projectPath . '/css/' . $project . '-master.css';
if (!file_exists($masterFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró el archivo master: ' . $project . '-master.css']);
    exit;
}

include __DIR__ . '/compile-css-fn.php';
$result = compileCssFromMaster($projectPath, $project);

if ($result) {
    echo json_encode([
        'success' => true,
        'files'   => [
            $project . '-mobile.css',
            $project . '-desktop.css'
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al compilar']);
}
