<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('create', 10);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input    = json_decode(file_get_contents('php://input'), true);
$name     = trim($input['name'] ?? '');
$slug     = trim($input['slug'] ?? '');
$cdns     = $input['cdns'] ?? [];
$colors   = $input['colors'] ?? [];
$orgType  = $input['orgType'] ?? 'none';
$orgCount = intval($input['orgCount'] ?? 0);

if (!$name || !$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y slug son requeridos']);
    exit;
}

if (!validateStringLength($name, 100)) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre es demasiado largo (máx 100 caracteres)']);
    exit;
}

if (!validateSlug($slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'El slug contiene caracteres inválidos o es demasiado largo']);
    exit;
}

if (!validateOrgCount($orgCount)) {
    http_response_code(400);
    echo json_encode(['error' => 'La cantidad de organización debe ser entre 0 y 30']);
    exit;
}

$projectsBase = realpath(__DIR__ . '/../../projects');
$projectPath  = $projectsBase . '/' . $slug;
$templatesDir = realpath(__DIR__ . '/../templates');

if (is_dir($projectPath)) {
    http_response_code(409);
    echo json_encode(['error' => 'Ya existe un proyecto con ese nombre']);
    exit;
}

// Create directories
$dirs = [$projectPath, $projectPath . '/pages', $projectPath . '/css', $projectPath . '/js', $projectPath . '/img'];
foreach ($dirs as $dir) {
    if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo crear el directorio']);
        exit;
    }
}

// CDN imports
$cdnUrls = [
    'bootstrap'       => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'bootstrap-icons' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'fontawesome'     => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'animate'         => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
];

$imports = '';
foreach ($cdns as $cdn) {
    if (isset($cdnUrls[$cdn])) {
        $imports .= '@import url("' . $cdnUrls[$cdn] . '");' . "\n";
    }
}

$escName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

// Organization
$orgLabels = [
    'semanas'  => 'Semana',
    'modulos'  => 'Módulo',
    'unidades' => 'Unidad',
];
$orgLabel = $orgLabels[$orgType] ?? '';

// ── Helper: read template and replace placeholders ──
function tpl($file, $vars = []) {
    global $templatesDir;
    $content = file_get_contents($templatesDir . '/' . $file);
    foreach ($vars as $key => $val) {
        $content = str_replace('{{' . $key . '}}', $val, $content);
    }
    return $content;
}

// ── Write files from templates ──

// HTML
file_put_contents($projectPath . '/index.html', tpl('index.html', ['PROJECT_NAME' => $escName]));
file_put_contents($projectPath . '/pages/tarea.html', tpl('pages/tarea.html'));
file_put_contents($projectPath . '/pages/quiz.html', tpl('pages/quiz.html'));
file_put_contents($projectPath . '/pages/foros.html', tpl('pages/foros.html'));

// CSS — master (archivo de trabajo)
$masterContent = tpl('css/master.css', ['IMPORTS' => $imports]);

// Reemplazar colores base si se proporcionaron
$colorMap = [
    'primary'   => '--ct-primary-base',
    'secondary' => '--ct-secondary-base',
    'accent1'   => '--ct-accent-1-base',
    'accent2'   => '--ct-accent-2-base',
    'accent3'   => '--ct-accent-3-base',
];
foreach ($colorMap as $key => $varName) {
    if (!empty($colors[$key]) && preg_match('/^#[0-9A-Fa-f]{6}$/', $colors[$key])) {
        $masterContent = preg_replace(
            '/' . preg_quote($varName, '/') . ':\s*#[0-9A-Fa-f]{6}/',
            $varName . ': ' . $colors[$key],
            $masterContent
        );
    }
}

file_put_contents($projectPath . '/css/' . $slug . '-master.css', $masterContent);

// Compilar mobile y desktop desde el master
include __DIR__ . '/compile-css-fn.php';
compileCssFromMaster($projectPath, $slug);

// Snippets
file_put_contents($projectPath . '/snippets.html', tpl('snippets.html'));

// JS
file_put_contents($projectPath . '/js/' . $slug . '-scripts.js', tpl('js/scripts.js', ['PROJECT_NAME' => $escName]));

// Organization pages
if ($orgType !== 'none' && $orgCount > 0) {
    $orgTpl = file_get_contents($templatesDir . '/pages/organization.html');
    for ($i = 1; $i <= $orgCount; $i++) {
        $num = str_pad($i, 2, '0', STR_PAD_LEFT);
        $label = $orgLabel . ' ' . $num;
        $fileName = strtolower(str_replace(['ó', 'á', 'é', 'í', 'ú'], ['o', 'a', 'e', 'i', 'u'], $orgLabel)) . '-' . $num;
        $content = str_replace('{{ORG_LABEL}}', $label, $orgTpl);
        file_put_contents($projectPath . '/pages/' . $fileName . '.html', $content);
    }
}

echo json_encode(['success' => true, 'slug' => $slug]);
