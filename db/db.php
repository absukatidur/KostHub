<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'kosmanager');
if ($db->connect_error) {
    die('Database connection failed: ' . $db->connect_error);
}
$db->set_charset('utf8mb4');

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

function fmtRupiah($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function flashMsg($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $bgColor = $flash['type'] === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)';
        $color = $flash['type'] === 'success' ? '#10b981' : '#ef4444';
        $borderColor = $flash['type'] === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';
        
        echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . '" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: space-between; background: ' . $bgColor . '; color: ' . $color . '; border: 1px solid ' . $borderColor . ';">';
        echo '<span>' . htmlspecialchars($flash['msg']) . '</span>';
        echo '<button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: inherit; line-height: 1;">&times;</button>';
        echo '</div>';
    }
}

function requireAdmin() {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../login.php');
        exit;
    }
}

function requireUser() {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        header('Location: ../login.php');
        exit;
    }
}

function statusBadge($s) {
    $map = [
        'occupied' => '<span class="badge badge-green">Terisi</span>',
        'empty' => '<span class="badge badge-blue">Kosong</span>',
        'paid' => '<span class="badge badge-green">Lunas</span>',
        'pending' => '<span class="badge badge-amber">Belum Bayar</span>',
        'ok' => '<span class="badge badge-green">Normal</span>',
        'cleaning' => '<span class="badge badge-amber">Cleaning</span>',
        'maintenance' => '<span class="badge badge-red">Perbaikan</span>',
    ];
    return $map[$s] ?? htmlspecialchars($s);
}

function repairStatusBadge($s) {
    $map = [
        'pending' => '<span class="badge badge-amber">Butuh Perbaikan</span>',
        'repairing' => '<span class="badge badge-red">Sedang Perbaikan</span>',
        'done' => '<span class="badge badge-gray">Selesai</span>',
    ];
    return $map[$s] ?? htmlspecialchars($s);
}
?>
