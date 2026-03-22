<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
rateLimit('create-guest', 5);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input    = json_decode(file_get_contents('php://input'), true);
$username = strtolower(trim($input['username'] ?? ''));
$password = trim($input['password'] ?? '');

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario y contraseña son obligatorios']);
    exit;
}

if (mb_strlen($username) < 3 || mb_strlen($username) > 30) {
    http_response_code(400);
    echo json_encode(['error' => 'El usuario debe tener entre 3 y 30 caracteres']);
    exit;
}

if (!preg_match('/^[a-z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode(['error' => 'El usuario solo puede contener letras, números y guion bajo']);
    exit;
}

if (mb_strlen($password) < 6 || mb_strlen($password) > 72) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener entre 6 y 72 caracteres']);
    exit;
}

$config = loadConfig();

// Verificar que no exista
if (isset($config['users'][$username])) {
    http_response_code(409);
    echo json_encode(['error' => 'Ya existe un usuario con ese nombre']);
    exit;
}

// Verificar que no haya ya un guest
foreach ($config['users'] as $u) {
    if ($u['role'] === 'guest') {
        http_response_code(409);
        echo json_encode(['error' => 'Ya existe un usuario invitado']);
        exit;
    }
}

$config['users'][$username] = [
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'role'     => 'guest'
];

saveConfig($config);

echo json_encode([
    'success' => true,
    'message' => 'Invitado "' . $username . '" creado'
]);
