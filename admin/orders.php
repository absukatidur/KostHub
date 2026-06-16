<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Order / Penyewaan — KostHub';
$pageTitleShort = 'Order / Penyewaan';

// Handle POST actions (delete / pay)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($id) {
        if ($action === 'delete') {
            $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                addLog($db, 'Order dihapus', "$id dihapus", 'order');
                flashMsg("Order $id berhasil dihapus.", 'success');
            } else {
                flashMsg("Gagal menghapus order: " . $db->error, 'error');
            }
        } elseif ($action === 'pay') {
            $stmt = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                addLog($db, 'Order lunas', "$id lunas", 'order');
                flashMsg("Order $id ditandai lunas.", 'success');
            } else {
                flashMsg("Gagal memproses pembayaran: " . $db->error, 'error');
            }
        }
    }
    header('Location: orders.php');
    exit;
}

// Fetch all orders
$orders = $db->query("SELECT * FROM orders ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

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
    <a href="orders_form.php" class="btn btn-primary btn-link">
      <i class="bi bi-plus-lg" class="fs-14"></i> Buat Order
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" class="fs-14"></i>
        <input id="ord-search" placeholder="Cari order..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Penghuni</th>
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
            <tr><td colspan="8" class="td-empty">Tidak ada data order</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><span class="td-mono-accent"><?= htmlspecialchars($o['id']) ?></span></td>
                <td><div class="td-bold"><?= htmlspecialchars($o['customer']) ?></div></td>
                <td><b><?= htmlspecialchars($o['room']) ?></b></td>
                <td>
                  <div class="text-sm"><?= htmlspecialchars($o['start']) ?></div>
                  <div class="text-sm text-muted">s/d <?= htmlspecialchars($o['end']) ?></div>
                </td>
                <td><?= htmlspecialchars($o['type']) ?></td>
                <td class="td-bold"><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
                <td>
                  <div class="action-group">
                    <a href="orders_detail.php?id=<?= urlencode($o['id']) ?>" class="btn btn-secondary btn-sm" title="Detail">
                      <i class="bi bi-eye" class="text-sm"></i>
                    </a>
                    <?php if ($o['status'] === 'pending'): ?>
                      <form method="POST" action="orders.php" class="inline-form">
                        <input type="hidden" name="action" value="pay">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($o['id']) ?>">
                        <button type="submit" class="btn btn-success btn-sm">Lunas</button>
                      </form>
                    <?php endif; ?>
                    <form method="POST" action="orders.php" onsubmit="return confirm('Hapus Order <?= htmlspecialchars($o['id']) ?>?');" class="inline-form">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($o['id']) ?>">
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
    
    <div class="pagination">
      <span class="info" id="ord-count"><?= count($orders) ?> order</span>
    </div>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/table-search.js?v=<?= time() ?>"></script>
<script>
initTableSearch('ord-search', '#ord-tbody tr', 'ord-count', '{count} order');
</script>

<?php require_once '../components/footer_scripts.php'; ?>
