<?php
// KostHub/index.php
$pageTitle = 'KostHub — Admin Dashboard';
$extraJs = [
    'assets/js/pages-dashboard.js',
];

require_once 'components/header.php';         // Berisi <html>, <head>, <link css>
require_once 'components/admin_sidebar.php';  // Berisi <nav id="sidebar">
require_once 'components/admin_topbar.php';   // Berisi <header id="topbar"> dan <main id="page-content">

// Include semua template HTML untuk Admin
require_once 'templates/admin/dashboard.php';
// ... require template lain (kamar, order, dll)

require_once 'components/footer_scripts.php'; // Berisi <script src="..."> dan penutup </body></html>
?>