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

  <div class="three-col" style="margin-bottom:14px">
    <?php if (empty($facilities)): ?>
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--slate-muted)">
        Belum ada fasilitas umum yang terdaftar.
      </div>
    <?php else: ?>
      <?php foreach ($facilities as $f): ?>
        <div class="card" style="position:relative">
          <div style="position:absolute; top:14px; right:14px">
            <?= repairStatusBadge($f['status']) ?>
          </div>
          <div style="font-size:40px; border-radius:10px; background:var(--blue-faded); display:flex; align-items:center; justify-content:center; margin-bottom:12px; color:var(--brand-accent); width: 48px; height: 48px;">
            <i class="bi bi-buildings" style="font-size:18px"></i>
          </div>
          <div style="font-weight:700; font-size:15px; margin-bottom:4px; color:var(--slate-white)"><?= htmlspecialchars($f['name']) ?></div>
          <div style="font-size:12px; color:var(--slate-muted); margin-bottom:8px">Lantai <?= htmlspecialchars($f['floor']) ?> · <?= htmlspecialchars($f['id']) ?></div>
          <div style="font-size:13px; color:var(--slate-mid); margin-bottom:14px; min-height: 40px;"><?= htmlspecialchars($f['desc']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
