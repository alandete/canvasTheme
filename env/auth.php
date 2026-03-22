<?php
/**
 * Sistema de autenticación y seguridad
 * Roles: admin (todo) | guest (solo lectura)
 * Configuración en: config/users.json
 */
session_start();

define('CONFIG_PATH', __DIR__ . '/config/users.json');

// ── Leer configuración ──
function loadConfig() {
    if (!file_exists(CONFIG_PATH)) return null;
    $data = json_decode(file_get_contents(CONFIG_PATH), true);
    return is_array($data) ? $data : null;
}

function saveConfig($config) {
    $dir = dirname(CONFIG_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents(CONFIG_PATH, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function isConfigured() {
    return file_exists(CONFIG_PATH) && loadConfig() !== null;
}

function getRecoveryEmail() {
    $config = loadConfig();
    return $config['recovery_email'] ?? '';
}

function getUsers() {
    $config = loadConfig();
    return $config['users'] ?? [];
}

// ── Headers de seguridad ──
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ── CSRF ──
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getCsrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

// ── Autenticación ──
function isLoggedIn() {
    return isset($_SESSION['user']) && isset($_SESSION['role']);
}

function currentRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return currentRole() === 'admin';
}

function isGuest() {
    return currentRole() === 'guest';
}

function login($username, $password) {
    $users = getUsers();
    if (!isset($users[$username])) return false;
    if (!password_verify($password, $users[$username]['password'])) return false;

    $_SESSION['user'] = $username;
    $_SESSION['role'] = $users[$username]['role'];
    session_regenerate_id(true);
    return true;
}

function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// ── Protección de páginas ──
function requireSetup() {
    if (!isConfigured()) {
        header('Location: setup.php');
        exit;
    }
}

function requireLogin() {
    requireSetup();
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permisos para esta acción']);
        exit;
    }
}

// ── Protección de API (POST con CSRF) ──
function requireAdminApi() {
    setSecurityHeaders();
    requireSetup();
    if (!isLoggedIn() || !isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Token CSRF inválido']);
        exit;
    }
}

function requireLoginApi() {
    setSecurityHeaders();
    requireSetup();
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
}

// ── Rate limiting básico (por sesión) ──
function rateLimit($action, $maxPerMinute = 30) {
    $key = 'rate_' . $action;
    $now = time();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }

    $_SESSION[$key] = array_filter($_SESSION[$key], function($t) use ($now) {
        return ($now - $t) < 60;
    });

    if (count($_SESSION[$key]) >= $maxPerMinute) {
        http_response_code(429);
        echo json_encode(['error' => 'Demasiadas solicitudes. Intenta en un momento.']);
        exit;
    }

    $_SESSION[$key][] = $now;
}

// ── Validaciones comunes ──
function validateSlug($slug) {
    return preg_match('/^[a-z0-9\-]{1,64}$/', $slug);
}

function validateOrgCount($count) {
    return $count >= 0 && $count <= 30;
}

function validateStringLength($str, $max = 100) {
    return mb_strlen($str) <= $max;
}
