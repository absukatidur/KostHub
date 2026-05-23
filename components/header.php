<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= $metaDesc ?? 'KostHub — Sistem manajemen kamar kos modern. Kelola kamar, penghuni, order, perbaikan, dan fasilitas dengan mudah.' ?>">
  <title><?= $pageTitle ?? 'KostHub — Room Management System' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
<?php if (!empty($extraCss)): ?>
<?php foreach ((array)$extraCss as $css): ?>
  <link rel="stylesheet" href="<?= $css ?>">
<?php endforeach; ?>
<?php endif; ?>
</head>
<body>