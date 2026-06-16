<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Fasilitas Kos — KostHub';
$pageTitleShort = 'Fasilitas';

// Fetch all facilities
$facilities = $db->query("SELECT * FROM facilities ORDER BY id")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Fasilitas Umum</h2>
      <p>Daftar fasilitas bersama yang tersedia di lingkungan kos</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="three-col mb-16">
    <?php if (empty($facilities)): ?>
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--slate-muted)">
        Belum ada fasilitas umum yang terdaftar.
      </div>
    <?php else: ?>
      <?php foreach ($facilities as $f): ?>
        <div class="card user-facility-card">
          <div class="ufc-status">
            <?= repairStatusBadge($f['status']) ?>
          </div>
          <div class="ufc-icon">
            <i class="bi bi-buildings"></i>
          </div>
          <div class="ufc-name"><?= htmlspecialchars($f['name']) ?></div>
          <div class="ufc-meta">Lantai <?= htmlspecialchars($f['floor']) ?> · <?= htmlspecialchars($f['id']) ?></div>
          <div class="ufc-desc"><?= htmlspecialchars($f['desc']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
