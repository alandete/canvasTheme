<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('toggle', 20);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$slug   = trim($input['slug'] ?? '');
$active = $input['active'] ?? true;

if (!$slug || !preg_match('/^[a-z0-9\-_]+$/i', $slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'Slug inválido']);
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $slug;

if (!$projectsBase || !is_dir($projectPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Proyecto no encontrado']);
    exit;
}

$flagFile = $projectPath . '/.disabled';

if ($active) {
    if (file_exists($flagFile)) unlink($flagFile);
} else {
    file_put_contents($flagFile, '');
}

echo json_encode(['success' => true, 'active' => $active]);
