<?php
require_once '../includes/db.php';
requireOwner();

$sql = file_get_contents(__DIR__ . '/../db/database.sql');
if ($sql) {
    if ($db->multi_query($sql)) {
        while ($db->next_result()) {
        } // flush
        addLog($db, 'System Reset', 'Semua data di-reset ke kondisi awal oleh Admin', 'room');
        flashMsg('Semua data berhasil di-reset.', 'success');
    } else {
        flashMsg('Gagal mereset data: ' . $db->error, 'error');
    }
} else {
    flashMsg('File database.sql tidak ditemukan.', 'error');
}

header('Location: dashboard.php');
exit;
?>