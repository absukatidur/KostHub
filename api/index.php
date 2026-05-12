<?php
// ═══════════════════════════════════════════
// KosManager API — Router
// ═══════════════════════════════════════════

session_start();

// ─── CORS & Headers ───
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ─── Database Connection ───
$db = new mysqli('localhost', 'root', '', 'kosmanager');
if ($db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed: ' . $db->connect_error]);
    exit;
}
$db->set_charset('utf8mb4');

// ─── Parse Request ───
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$apiPos = strpos($uri, '/api');
if ($apiPos !== false) {
    $path = substr($uri, $apiPos + 4);
} else {
    $path = $uri;
}
$path     = rtrim($path, '/');
$segments = explode('/', trim($path, '/'));
$input    = json_decode(file_get_contents('php://input'), true) ?? [];

$resource = $segments[0] ?? '';
$id       = $segments[1] ?? null;

// ─── Helper Functions ───
function addLog($db, $action, $detail, $type) {
    $stmt = $db->prepare("INSERT INTO logs (time, action, detail, type) VALUES (NOW(), ?, ?, ?)");
    $stmt->bind_param('sss', $action, $detail, $type);
    $stmt->execute();
}

function nextId($db, $table, $prefix) {
    $r = $db->query("SELECT id FROM `$table` ORDER BY id DESC LIMIT 1");
    if ($r->num_rows === 0) return $prefix . '001';
    $row = $r->fetch_assoc();
    $num = intval(preg_replace('/\D/', '', $row['id']));
    return $prefix . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
}

function jsonOut($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// ─── Load Controllers ───
require_once __DIR__ . '/controllers/AuthController.php';

// ═══ ROUTING ═══
// Auth routes (public)
if ($resource === 'auth') {
    handleAuth($db, $method, $id, $input);
}

// All other routes require authentication
if (empty($_SESSION['user_id'])) {
    jsonOut(['error' => 'Not authenticated'], 401);
}


$db->close();
?>
