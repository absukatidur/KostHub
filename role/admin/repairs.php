<?php
$basePath = '../';
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
    <a href="repairs_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-plus-lg" style="font-size:14px"></i> Laporkan Kerusakan
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
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
            <tr><td colspan="8" style="text-align:center; color:var(--slate-muted)">Tidak ada data laporan perbaikan</td></tr>
          <?php else: ?>
            <?php foreach ($repairs as $r): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['id']) ?></span></td>
                <td><b><?= htmlspecialchars($r['target']) ?></b></td>
                <td><?= htmlspecialchars($r['issue']) ?></td>
                <td>
                  <?php $count = intval($r['votes'] ?: 1); ?>
                  <?php if ($count > 1): ?>
                    <span class="badge badge-red" style="font-size:11px">Dilaporkan oleh <?= $count ?> orang</span>
                  <?php else: ?>
                    <span class="badge badge-gray" style="font-size:11px">Dilaporkan oleh 1 orang</span>
                  <?php endif; ?>
                </td>
                <td style="font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['reported']) ?></td>
                <td><?= htmlspecialchars($r['tech']) ?></td>
                <td><?= repairStatusBadge($r['status']) ?></td>
                <td>
                  <div style="display:flex; gap:6px">
                    <a href="repairs_form.php?id=<?= urlencode($r['id']) ?>" class="btn btn-secondary btn-sm">Update</a>
                    <form method="POST" action="repairs.php" onsubmit="return confirm('Hapus laporan perbaikan <?= htmlspecialchars($r['id']) ?>?');" style="display:inline;">
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
