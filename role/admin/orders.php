<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Order / Penyewaan</h2>
      <p>Kelola transaksi sewa kamar</p>
    </div>
    <a href="orders_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-plus-lg" style="font-size:14px"></i> Buat Order
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
        <input id="ord-search" placeholder="Cari order..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Customer</th>
            <th>Kamar</th>
            <th>Periode</th>
            <th>Tipe</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="ord-tbody">
          <?php if (empty($orders)): ?>
            <tr><td colspan="8" style="text-align:center; color:var(--slate-muted)">Tidak ada data order</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--brand-accent)"><?= htmlspecialchars($o['id']) ?></span></td>
                <td><div style="font-weight:600"><?= htmlspecialchars($o['customer']) ?></div></td>
                <td><b><?= htmlspecialchars($o['room']) ?></b></td>
                <td>
                  <div style="font-size:12px"><?= htmlspecialchars($o['start']) ?></div>
                  <div style="font-size:12px; color:var(--slate-muted)">s/d <?= htmlspecialchars($o['end']) ?></div>
                </td>
                <td><?= htmlspecialchars($o['type']) ?></td>
                <td style="font-weight:600"><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
                <td>
                  <div style="display:flex; gap:6px">
                    <a href="orders_detail.php?id=<?= urlencode($o['id']) ?>" class="btn btn-secondary btn-sm" title="Detail">
                      <i class="bi bi-eye" style="font-size:12px"></i>
                    </a>
                    <?php if ($o['status'] === 'pending'): ?>
                      <form method="POST" action="orders.php" style="display:inline;">
                        <input type="hidden" name="action" value="pay">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($o['id']) ?>">
                        <button type="submit" class="btn btn-success btn-sm">Lunas</button>
                      </form>
                    <?php endif; ?>
                    <form method="POST" action="orders.php" onsubmit="return confirm('Hapus Order <?= htmlspecialchars($o['id']) ?>?');" style="display:inline;">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($o['id']) ?>">
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
      <span class="info" id="ord-count"><?= count($orders) ?> order</span>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('ord-search');
  if (searchInput) {
    searchInput.oninput = function() {
      const q = this.value.toLowerCase();
      let visibleCount = 0;
      const rows = document.querySelectorAll('#ord-tbody tr');
      rows.forEach(tr => {
        if (tr.cells.length < 2) return;
        const text = tr.textContent.toLowerCase();
        if (text.includes(q)) {
          tr.style.display = '';
          visibleCount++;
        } else {
          tr.style.display = 'none';
        }
      });
      document.getElementById('ord-count').textContent = `${visibleCount} order`;
    };
  }
});
</script>

<?php require_once '../components/footer_scripts.php'; ?>
