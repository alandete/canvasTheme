<?php
require_once __DIR__ . '/../auth.php';
requireAdminApi();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($input['email'] ?? ''));

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Correo electrónico inválido']);
    exit;
}

$config = loadConfig();
$config['recovery_email'] = $email;
saveConfig($config);

echo json_encode([
    'success' => true,
    'message' => 'Correo de recuperación actualizado'
]);
