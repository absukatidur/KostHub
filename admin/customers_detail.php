<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
if (!$id) {
    flashMsg("ID Penghuni tidak valid.", 'error');
    header('Location: customers.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    flashMsg("Penghuni tidak ditemukan.", 'error');
    header('Location: customers.php');
    exit;
}

// Fetch room details if assigned
$room = null;
if (!empty($customer['room'])) {
    $stmt_room = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt_room->bind_param('s', $customer['room']);
    $stmt_room->execute();
    $room = $stmt_room->get_result()->fetch_assoc();
}

// Fetch all orders/invoices for this customer name
$stmt_orders = $db->prepare("SELECT * FROM orders WHERE customer = ? ORDER BY id DESC");
$stmt_orders->bind_param('s', $customer['name']);
$stmt_orders->execute();
$orders = $stmt_orders->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Detail Penghuni — ' . htmlspecialchars($customer['name']);
$pageTitleShort = 'Penghuni';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container-md">
  <div class="section-header">
    <div>
      <h2>Detail Penghuni</h2>
      <p>Data lengkap dan riwayat sewa penghuni</p>
    </div>
    <div class="action-group">
      <a href="customers.php" class="btn btn-secondary btn-link">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
      <a href="customers_form.php?id=<?= urlencode($customer['id']) ?>" class="btn btn-primary btn-link">
        <i class="bi bi-pencil"></i> Edit Penghuni
      </a>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="two-col card-mb">
    <!-- Profile & Contact Card -->
    <div class="card form-stack">
      <div style="display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--border-soft); padding-bottom:14px">
        <div class="avatar" style="width:48px; height:48px; font-size:18px; border-radius:50%"><?= strtoupper(substr($customer['name'], 0, 2)) ?></div>
        <div>
          <h3 class="card-section-title"><?= htmlspecialchars($customer['name']) ?></h3>
          <span class="td-mono"><?= htmlspecialchars($customer['id']) ?></span>
        </div>
      </div>
      <div class="detail-row"><span class="detail-key">Email</span><span class="text-accent"><?= htmlspecialchars($customer['email']) ?></span></div>
      <div class="detail-row"><span class="detail-key">WhatsApp</span><span class="detail-val"><?= htmlspecialchars($customer['wa']) ?></span></div>
    </div>

    <!-- Room Status Card -->
    <div class="card form-stack">
      <div class="card-section-header">
        <h3 class="card-section-title">Status Kamar</h3>
        <?php if ($room): ?>
          <?= statusBadge($room['status']) ?>
        <?php else: ?>
          <span class="badge badge-gray">Tidak Ada</span>
        <?php endif; ?>
      </div>
      <?php if ($room): ?>
        <div class="detail-row"><span class="detail-key">Kamar</span><a href="rooms_detail.php?id=<?= urlencode($room['id']) ?>" class="text-accent td-bold btn-link"><?= htmlspecialchars($room['id']) ?></a></div>
        <div class="detail-row"><span class="detail-key">Tipe / Lantai</span><span class="detail-val"><?= htmlspecialchars($room['type']) ?> (Lantai <?= htmlspecialchars($room['floor']) ?>)</span></div>
        <div class="detail-row"><span class="detail-key">Sewa Hingga</span><span class="detail-val"><?= htmlspecialchars($room['until']) ?></span></div>
      <?php else: ?>
        <div class="td-empty" style="padding:15px">Penghuni belum menempati kamar manapun.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Rent/Orders History -->
  <div class="card">
    <div class="mb-16"><h3 class="card-section-title">Riwayat Transaksi / Order</h3></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Kamar</th>
            <th>Tipe Sewa</th>
            <th>Total Tagihan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="5" class="td-empty">Belum ada riwayat transaksi</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><b class="td-mono"><?= htmlspecialchars($o['id']) ?></b></td>
                <td><?= htmlspecialchars($o['room']) ?></td>
                <td><?= htmlspecialchars($o['type']) ?> (<?= htmlspecialchars($o['start']) ?> s/d <?= htmlspecialchars($o['end']) ?>)</td>
                <td><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
