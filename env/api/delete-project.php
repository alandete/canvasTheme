<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('delete', 5);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$slug  = trim($input['slug'] ?? '');

// Validate
if (!$slug || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'Slug inválido']);
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $slug;

// Verify exists and is inside projects dir
if (!is_dir($projectPath) || strpos(realpath($projectPath), $projectsBase) !== 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Proyecto no encontrado']);
    exit;
}

// Recursive delete
function deleteDir($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            deleteDir($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

deleteDir($projectPath);

if (is_dir($projectPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo eliminar completamente el proyecto']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Proyecto eliminado'
]);
