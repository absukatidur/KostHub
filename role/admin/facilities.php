<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Fasilitas Umum</h2>
      <p>Kelola fasilitas bersama di kos</p>
    </div>
    <a href="facilities_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-plus-lg" style="font-size:14px"></i> Tambah Fasilitas
    </a>
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
            <?= statusBadge($f['status']) ?>
          </div>
          <div style="font-size:40px; border-radius:10px; background:var(--blue-faded); display:flex; align-items:center; justify-content:center; margin-bottom:12px; color:var(--brand-accent); width: 48px; height: 48px;">
            <i class="bi bi-buildings" style="font-size:18px"></i>
          </div>
          <div style="font-weight:700; font-size:15px; margin-bottom:4px; color:var(--slate-white)"><?= htmlspecialchars($f['name']) ?></div>
          <div style="font-size:12px; color:var(--slate-muted); margin-bottom:8px">Lantai <?= htmlspecialchars($f['floor']) ?> · <?= htmlspecialchars($f['id']) ?></div>
          <div style="font-size:13px; color:var(--slate-mid); margin-bottom:14px; min-height: 40px;"><?= htmlspecialchars($f['desc']) ?></div>
          <div style="display:flex; gap:6px">
            <a href="facilities_form.php?id=<?= urlencode($f['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
              <i class="bi bi-pencil" style="font-size:12px"></i>
            </a>
            <form method="POST" action="facilities.php" onsubmit="return confirm('Hapus fasilitas <?= htmlspecialchars($f['name']) ?>?');" style="display:inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= htmlspecialchars($f['id']) ?>">
              <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                <i class="bi bi-trash" style="font-size:12px"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
