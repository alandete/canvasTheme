<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('edit', 20);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input    = json_decode(file_get_contents('php://input'), true);
$slug     = trim($input['slug'] ?? '');
$name     = trim($input['name'] ?? '');
$orgType  = $input['orgType'] ?? 'none';
$orgCount = intval($input['orgCount'] ?? 0);

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

$changes = [];

// Update project name in index.html if provided
if ($name) {
    $indexFile = $projectPath . '/index.html';
    if (file_exists($indexFile)) {
        $html = file_get_contents($indexFile);
        $escaped = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        // Replace first <h2>...</h2> with new name
        $html = preg_replace('/<h2>[^<]*<\/h2>/', '<h2>' . $escaped . '</h2>', $html, 1);
        file_put_contents($indexFile, $html);
        $changes[] = 'Nombre actualizado';
    }
}

// Add organization pages if requested
if ($orgType !== 'none' && $orgCount > 0) {
    $orgLabels = [
        'semanas'  => 'Semana',
        'modulos'  => 'Módulo',
        'unidades' => 'Unidad',
    ];
    $orgLabel = $orgLabels[$orgType] ?? '';

    if ($orgLabel) {
        $templatesDir = realpath(__DIR__ . '/../templates');
        $orgTpl = file_get_contents($templatesDir . '/pages/organization.html');
        $added = 0;

        for ($i = 1; $i <= $orgCount; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $label = $orgLabel . ' ' . $num;
            $fileName = strtolower(str_replace(
                ['ó', 'á', 'é', 'í', 'ú'],
                ['o', 'a', 'e', 'i', 'u'],
                $orgLabel
            )) . '-' . $num;
            $filePath = $projectPath . '/pages/' . $fileName . '.html';

            if (!file_exists($filePath)) {
                $content = str_replace('{{ORG_LABEL}}', $label, $orgTpl);
                file_put_contents($filePath, $content);
                $added++;
            }
        }

        if ($added > 0) {
            $changes[] = $added . ' página(s) de organización agregada(s)';
        }
    }
}

if (empty($changes)) {
    echo json_encode(['success' => true, 'message' => 'Sin cambios']);
} else {
    echo json_encode(['success' => true, 'message' => implode('. ', $changes)]);
}
