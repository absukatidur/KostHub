<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Tipe Kamar — KostHub';
$pageTitleShort = 'Tipe Kamar';

// Handle delete room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute()) {
            addLog($db, 'Kamar dihapus', "Kamar $id dihapus", 'room');
            flashMsg("Kamar $id berhasil dihapus.", 'success');
        } else {
            flashMsg("Gagal menghapus kamar: " . $db->error, 'error');
        }
    }
    header('Location: rooms.php');
    exit;
}

// Fetch all rooms
$rooms = $db->query("SELECT * FROM rooms ORDER BY id")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Tipe Kamar</h2>
      <p>Kelola data master kamar kos</p>
    </div>
    <a href="rooms_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-plus-lg" style="font-size:14px"></i> Tambah Kamar
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
        <input id="room-search" placeholder="Cari kamar..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Kamar</th>
            <th>Tipe</th>
            <th>Lantai</th>
            <th>Tipe Sewa</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="room-tbody">
          <?php if (empty($rooms)): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--slate-muted)">Tidak ada data kamar</td></tr>
          <?php else: ?>
            <?php foreach ($rooms as $r): ?>
              <tr>
                <td><b><?= htmlspecialchars($r['id']) ?></b></td>
                <td><?= htmlspecialchars($r['type']) ?></td>
                <td>Lantai <?= htmlspecialchars($r['floor']) ?></td>
                <td><?= htmlspecialchars($r['rent']) ?></td>
                <td><?= fmtRupiah($r['price']) ?></td>
                <td><?= statusBadge($r['status']) ?></td>
                <td>
                  <div style="display:flex; gap:6px">
                    <a href="rooms_detail.php?id=<?= urlencode($r['id']) ?>" class="btn btn-secondary btn-sm" title="Detail">
                      <i class="bi bi-eye" style="font-size:12px"></i>
                    </a>
                    <a href="rooms_form.php?id=<?= urlencode($r['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                      <i class="bi bi-pencil" style="font-size:12px"></i>
                    </a>
                    <form method="POST" action="rooms.php" onsubmit="return confirm('Hapus Kamar <?= htmlspecialchars($r['id']) ?>? Kamar akan dihapus permanen.');" style="display:inline;">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
                      <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="bi bi-trash" style="font-size:12px"></i>
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
    
    <div class="pagination">
      <span class="info" id="room-count">Menampilkan <?= count($rooms) ?> kamar</span>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('room-search');
  if (searchInput) {
    searchInput.oninput = function() {
      const q = this.value.toLowerCase();
      let visibleCount = 0;
      const rows = document.querySelectorAll('#room-tbody tr');
      rows.forEach(tr => {
        // Skip empty row if present
        if (tr.cells.length < 2) return;
        const text = tr.textContent.toLowerCase();
        if (text.includes(q)) {
          tr.style.display = '';
          visibleCount++;
        } else {
          tr.style.display = 'none';
        }
      });
      document.getElementById('room-count').textContent = `Menampilkan ${visibleCount} kamar`;
    };
  }
});
</script>

<?php require_once '../components/footer_scripts.php'; ?>
