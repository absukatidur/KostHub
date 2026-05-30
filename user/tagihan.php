<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Tagihan & Pembayaran — KostHub';
$pageTitleShort = 'Tagihan';

$cid = $_SESSION['customer_id'];

// Get customer info
$stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param('s', $cid);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

// Fetch all orders for this customer
$stmt_orders = $db->prepare("SELECT * FROM orders WHERE customer = ? ORDER BY id DESC");
$stmt_orders->bind_param('s', $customer['name']);
$stmt_orders->execute();
$orders = $stmt_orders->get_result()->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Tagihan & Pembayaran</h2>
      <p>Riwayat transaksi sewa kamar</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Kamar</th>
            <th>Periode</th>
            <th>Tipe</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--slate-muted); padding:40px">Tidak ada tagihan</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--brand-accent)"><?= htmlspecialchars($o['id']) ?></span></td>
                <td><b><?= htmlspecialchars($o['room']) ?></b></td>
                <td>
                  <div style="font-size: 13px; color: var(--slate-bright);"><?= htmlspecialchars($o['start']) ?></div>
                  <div style="font-size: 12px; color: var(--slate-muted)">s/d <?= htmlspecialchars($o['end']) ?></div>
                </td>
                <td><?= htmlspecialchars($o['type']) ?></td>
                <td style="font-weight:600"><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
                <td>
                  <?php if ($o['status'] === 'pending'): ?>
                    <a href="pay.php?id=<?= urlencode($o['id']) ?>" class="btn btn-success btn-sm">
                      <i class="bi bi-credit-card" style="font-size:12px"></i> Bayar
                    </a>
                  <?php else: ?>
                    <span style="font-size:12px; color:var(--green-vivid)">✓ Lunas</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
