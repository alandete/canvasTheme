<?php
require_once __DIR__ . '/../auth.php';
requireLogin();

$project = $_GET['project'] ?? '';

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $project)) {
    http_response_code(400);
    exit('Nombre de proyecto inválido');
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = realpath($projectsBase . '/' . $project);

if (!$projectPath || strpos($projectPath, $projectsBase) !== 0 || !is_dir($projectPath)) {
    http_response_code(404);
    exit('Proyecto no encontrado');
}

$date      = date('Y-m-d_H-i-s');
$filename  = $project . '_' . $date . '.zip';
$tmpFile   = tempnam(sys_get_temp_dir(), 'canvas_export_');
$backupDir = $projectPath . '/backups';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$zip = new ZipArchive();
if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('No se pudo crear el ZIP');
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    $filePath = $file->getRealPath();
    // Skip the backups folder
    if (strpos($filePath, $backupDir) === 0) continue;
    $relativePath = $project . '/' . str_replace('\\', '/', substr($filePath, strlen($projectPath) + 1));
    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    } else {
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

// Save a local copy in the project's backups folder
copy($tmpFile, $backupDir . '/' . $filename);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: no-cache');
readfile($tmpFile);
unlink($tmpFile);
