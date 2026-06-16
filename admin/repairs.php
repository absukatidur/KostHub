<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Perbaikan — KostHub';
$pageTitleShort = 'Perbaikan';

// Handle delete repair report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $db->prepare("DELETE FROM repairs WHERE id = ?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute()) {
            addLog($db, 'Perbaikan dihapus', "$id dihapus", 'repair');
            flashMsg("Laporan perbaikan $id berhasil dihapus.", 'success');
        } else {
            flashMsg("Gagal menghapus laporan: " . $db->error, 'error');
        }
    }
    header('Location: repairs.php');
    exit;
}

// Fetch all repairs
$repairs = $db->query("SELECT * FROM repairs ORDER BY reported DESC")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Perbaikan</h2>
      <p>Monitor kerusakan kamar &amp; fasilitas umum</p>
    </div>
    <a href="repairs_form.php" class="btn btn-primary btn-link">
      <i class="bi bi-plus-lg" class="fs-14"></i> Laporkan Kerusakan
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" class="fs-14"></i>
        <input id="rep-search" placeholder="Cari laporan..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Target</th>
            <th>Masalah</th>
            <th>Prioritas</th>
            <th>Dilaporkan</th>
            <th>Teknisi</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="rep-tbody">
          <?php if (empty($repairs)): ?>
            <tr><td colspan="8" class="td-empty">Tidak ada data laporan perbaikan</td></tr>
          <?php else: ?>
            <?php foreach ($repairs as $r): ?>
              <tr>
                <td><span class="td-mono"><?= htmlspecialchars($r['id']) ?></span></td>
                <td><b><?= htmlspecialchars($r['target']) ?></b></td>
                <td><?= htmlspecialchars($r['issue']) ?></td>
                <td>
                  <?php $count = intval($r['votes'] ?: 1); ?>
                  <?php if ($count > 1): ?>
                    <span class="badge badge-red text-xs">Dilaporkan oleh <?= $count ?> orang</span>
                  <?php else: ?>
                    <span class="badge badge-gray text-xs">Dilaporkan oleh 1 orang</span>
                  <?php endif; ?>
                </td>
                <td class="text-sm text-muted"><?= htmlspecialchars($r['reported']) ?></td>
                <td><?= htmlspecialchars($r['tech']) ?></td>
                <td><?= repairStatusBadge($r['status']) ?></td>
                <td>
                  <div class="action-group">
                    <a href="repairs_form.php?id=<?= urlencode($r['id']) ?>" class="btn btn-secondary btn-sm">Update</a>
                    <form method="POST" action="repairs.php" onsubmit="return confirm('Hapus laporan perbaikan <?= htmlspecialchars($r['id']) ?>?');" class="inline-form">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
                      <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="bi bi-trash" class="text-sm"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('rep-search');
  if (searchInput) {
    searchInput.oninput = function() {
      const q = this.value.toLowerCase();
      const rows = document.querySelectorAll('#rep-tbody tr');
      rows.forEach(tr => {
        if (tr.cells.length < 2) return;
        const text = tr.textContent.toLowerCase();
        if (text.includes(q)) {
          tr.style.display = '';
        } else {
          tr.style.display = 'none';
        }
      });
    };
  }
});
</script>

<?php require_once '../components/footer_scripts.php'; ?>
