<?php
require_once 'includes/db.php';

if (!empty($_SESSION['role'])) {
    if (in_array($_SESSION['role'], ['admin', 'owner'])) {
        header('Location: admin/dashboard.php');
        exit;
    } else if ($_SESSION['role'] === 'user') {
        header('Location: user/dashboard.php');
        exit;
    }
} else {
    header('Location: landing.php');
    exit;
}
?>
