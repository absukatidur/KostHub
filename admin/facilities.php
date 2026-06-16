<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Fasilitas Umum — KostHub';
$pageTitleShort = 'Fasilitas Umum';

// Handle delete facility
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $db->prepare("DELETE FROM facilities WHERE id = ?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute()) {
            addLog($db, 'Fasilitas dihapus', "$id dihapus", 'room');
            flashMsg("Fasilitas $id berhasil dihapus.", 'success');
        } else {
            flashMsg("Gagal menghapus fasilitas: " . $db->error, 'error');
        }
    }
    header('Location: facilities.php');
    exit;
}

// Fetch all facilities
$facilities = $db->query("SELECT * FROM facilities ORDER BY id")->fetch_all(MYSQLI_ASSOC);

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
    <a href="facilities_form.php" class="btn btn-primary btn-link">
      <i class="bi bi-plus-lg" style="font-size: 14px;"></i> Tambah Fasilitas
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="three-col" class="mb-14">
    <?php if (empty($facilities)): ?>
      <div class="card td-empty" style="grid-column: 1 / -1; padding: 40px">
        Belum ada fasilitas umum yang terdaftar.
      </div>
    <?php else: ?>
      <?php foreach ($facilities as $f): ?>
        <div class="card" class="card-relative">
          <div class="card-badge-top-right">
            <?= repairStatusBadge($f['status']) ?>
          </div>
          <div class="icon-lg-square">
            <i class="bi bi-buildings" style="font-size: 18px;"></i>
          </div>
          <div style="font-weight:700; font-size:15px; margin-bottom:4px; color:var(--slate-white)"><?= htmlspecialchars($f['name']) ?></div>
          <div class="text-sm text-muted" class="mb-8">Lantai <?= htmlspecialchars($f['floor']) ?> · <?= htmlspecialchars($f['id']) ?></div>
          <div style="font-size:13px; color:var(--slate-mid); margin-bottom:14px; min-height: 40px;"><?= htmlspecialchars($f['desc']) ?></div>
          <div class="action-group">
            <a href="facilities_form.php?id=<?= urlencode($f['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
              <i class="bi bi-pencil" class="text-sm"></i>
            </a>
            <form method="POST" action="facilities.php" onsubmit="return confirm('Hapus fasilitas <?= htmlspecialchars($f['name']) ?>?');" class="inline-form">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= htmlspecialchars($f['id']) ?>">
              <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                <i class="bi bi-trash" class="text-sm"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
