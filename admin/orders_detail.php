<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
if (!$id) {
    flashMsg("ID Order tidak valid.", 'error');
    header('Location: orders.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    flashMsg("Order tidak ditemukan.", 'error');
    header('Location: orders.php');
    exit;
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'pay') {
    $stmtPay = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
    $stmtPay->bind_param('s', $id);
    if ($stmtPay->execute()) {
        addLog($db, 'Order lunas', "$id lunas", 'order');
        flashMsg("Order $id ditandai lunas.", 'success');
    } else {
        flashMsg("Gagal memproses pembayaran: " . $db->error, 'error');
    }
    header("Location: orders_detail.php?id=" . urlencode($id));
    exit;
}

$pageTitle = 'Detail Order ' . htmlspecialchars($id) . ' — KostHub';
$pageTitleShort = 'Order / Penyewaan';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Detail Order <?= htmlspecialchars($id) ?></h2>
      <p>Rincian data transaksi dan status sewa</p>
    </div>
    <a href="orders.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card" style="margin-bottom: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom: 1px solid var(--border-soft); padding-bottom:12px">
      <h3 style="margin:0; font-size:16px; color:var(--slate-white)">Informasi Sewa</h3>
      <div><?= statusBadge($order['status']) ?></div>
    </div>
    
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span style="color:var(--slate-muted)">Customer</span><span style="font-weight:600; color:var(--slate-bright)"><?= htmlspecialchars($order['customer']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span style="color:var(--slate-muted)">Kamar</span><span style="font-weight:600; color:var(--slate-bright)"><?= htmlspecialchars($order['room']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span style="color:var(--slate-muted)">Tipe Sewa</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($order['type']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span style="color:var(--slate-muted)">Tanggal Mulai</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($order['start']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span style="color:var(--slate-muted)">Tanggal Selesai</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($order['end']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0;"><span style="color:var(--slate-muted)">Total Pembayaran</span><span style="color:var(--brand-accent); font-size:16px; font-weight:700"><?= fmtRupiah($order['total']) ?></span></div>
  </div>

  <?php if ($order['status'] === 'pending'): ?>
    <div class="card">
      <div style="margin-bottom:12px"><h3 style="margin:0; font-size:15px; color:var(--slate-white)">Tindakan</h3></div>
      <p style="font-size: 13px; color: var(--slate-muted); margin-bottom: 16px;">Jika customer telah melakukan transfer atau membayar tunai, tandai tagihan ini sebagai lunas.</p>
      
      <form method="POST" action="orders_detail.php?id=<?= urlencode($id) ?>" style="display:flex; justify-content:flex-end">
        <input type="hidden" name="action" value="pay">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-circle" style="font-size:13px"></i> Tandai Lunas
        </button>
      </form>
    </div>
  <?php endif; ?>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
