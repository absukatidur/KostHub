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

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Detail Order <?= htmlspecialchars($id) ?></h2>
      <p>Rincian data transaksi dan status sewa</p>
    </div>
    <a href="orders.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card card-mb">
    <div class="card-section-header">
      <h3 class="card-section-title">Informasi Sewa</h3>
      <div><?= statusBadge($order['status']) ?></div>
    </div>
    
    <div class="detail-row"><span class="detail-key">Penghuni</span><span class="detail-val"><?= htmlspecialchars($order['customer']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Kamar</span><span class="detail-val"><?= htmlspecialchars($order['room']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val"><?= htmlspecialchars($order['type']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tanggal Mulai</span><span class="detail-val"><?= htmlspecialchars($order['start']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tanggal Selesai</span><span class="detail-val"><?= htmlspecialchars($order['end']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Total Pembayaran</span><span class="text-accent" style="font-size:16px; font-weight:700"><?= fmtRupiah($order['total']) ?></span></div>
  </div>

  <?php if ($order['status'] === 'pending'): ?>
    <div class="card">
      <div class="mb-12"><h3 class="card-section-title" style="font-size: 15px;">Tindakan</h3></div>
      <p class="text-sm text-muted" class="mb-16">Jika customer telah melakukan transfer atau membayar tunai, tandai tagihan ini sebagai lunas.</p>
      
      <form method="POST" action="orders_detail.php?id=<?= urlencode($id) ?>" class="form-actions" style="margin-top:0">
        <input type="hidden" name="action" value="pay">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-circle" style="font-size: 13px;"></i> Tandai Lunas
        </button>
      </form>
    </div>
  <?php endif; ?>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
