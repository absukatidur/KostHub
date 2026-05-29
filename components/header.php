<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="KosManager — Sistem manajemen kamar kos modern. Kelola kamar, penghuni, order, perbaikan, dan fasilitas dengan mudah.">
  <title><?= $pageTitle ?? 'KosManager — Room Management System' ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  
  <?php if (!empty($extraCss)): ?>
    <?php foreach ((array)$extraCss as $css): ?>
      <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>