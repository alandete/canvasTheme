<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('password', 5);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input    = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

$config = loadConfig();
if (!$config || !isset($config['users'][$username])) {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

if (mb_strlen($password) < 6 || mb_strlen($password) > 72) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener entre 6 y 72 caracteres']);
    exit;
}

$config['users'][$username]['password'] = password_hash($password, PASSWORD_DEFAULT);
saveConfig($config);

echo json_encode([
    'success' => true,
    'message' => 'Contraseña de "' . $username . '" actualizada'
]);
