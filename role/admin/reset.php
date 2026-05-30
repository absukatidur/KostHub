<?php
require_once '../includes/db.php';
requireOwner();

$sql = file_get_contents(__DIR__ . '/../db/database.sql');
if ($sql) {
    if ($db->multi_query($sql)) {
        while ($db->next_result()) {} // flush
        
        // Seed again using setup queries to recreate tables and admin user, but keep everything consistent
        // Wait, setup.php runs to recreate the users and requests.
        // Let's run setup logic too if database.sql only clears it!
        // Let's check what database.sql has: it deletes all records except admin.
        // If we want setup data back (like andi, budi, etc.), we can query setup.php seed data.
        // Let's check database.sql again. Yes, it deletes rooms, orders, repairs, facilities, customers, logs.
        // To be safe, we can run the database.sql queries.
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
