<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Dashboard — KostHub';
$pageTitleShort = 'Dashboard';

// Date Text
$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$dayName = $days[date('l')];
$monthName = $months[date('F')];
$dateText = $dayName . ', ' . date('j') . ' ' . $monthName . ' ' . date('Y') . ' · Portal Penghuni';

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

// Get room info
$room = null;
if (!empty($customer['room'])) {
    $rs = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $rs->bind_param('s', $customer['room']);
    $rs->execute();
    $room = $rs->get_result()->fetch_assoc();
}

// Get pending orders
$os = $db->prepare("SELECT * FROM orders WHERE customer = ? AND status = 'pending'");
$os->bind_param('s', $customer['name']);
$os->execute();
$pendingOrders = $os->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's active repairs
$target = 'Kamar ' . ($customer['room'] ?? '');
$rp = $db->prepare("SELECT COUNT(*) as count FROM repairs WHERE target = ? AND status != 'done'");
$rp->bind_param('s', $target);
$rp->execute();
$activeRepairsCount = $rp->get_result()->fetch_assoc()['count'] ?? 0;

// Get user's pending requests
$rq = $db->prepare("SELECT COUNT(*) as count FROM requests WHERE customer_id = ? AND status = 'pending'");
$rq->bind_param('s', $cid);
$rq->execute();
$pendingRequestsCount = $rq->get_result()->fetch_assoc()['count'] ?? 0;

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Selamat datang, <?= htmlspecialchars($customer['name']) ?> 👋</h2>
      <p><?= htmlspecialchars($dateText) ?></p>
    </div>
  </div>

  <?php showFlash(); ?>

  <!-- Alerts for pending orders or no room -->
  <div class="mb-16">
    <?php if (!$room): ?>
      <div class="no-room-cta">
        <div class="no-room-icon">
          <i class="bi bi-house"></i>
        </div>
        <h3>Anda belum memiliki kamar</h3>
        <p>Jelajahi kamar yang tersedia dan pesan kamar impian Anda sekarang!</p>
        <a href="browse_rooms.php" class="btn btn-primary btn-link">
          <i class="bi bi-search"></i> Cari Kamar Sekarang
        </a>
      </div>
    <?php else: ?>
      <?php foreach ($pendingOrders as $o): ?>
        <div class="user-alert alert-warning">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-exclamation-circle"></i>
            <span>Tagihan <?= htmlspecialchars($o['id']) ?> sebesar <?= fmtRupiah($o['total']) ?> belum dibayar</span>
          </div>
          <a href="tagihan.php" class="btn btn-primary btn-sm btn-link alert-action">Bayar</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if ($room): ?>
    <!-- Room Info Card -->
    <div class="card card-mb">
      <div class="user-room-info-header">
        <div class="icon-wrap ic-blue">
          <i class="bi bi-door-open"></i>
        </div>
        <div style="flex: 1">
          <h3>Kamar <?= htmlspecialchars($room['id']) ?></h3>
          <p>Lantai <?= htmlspecialchars($room['floor']) ?> · <?= htmlspecialchars($room['type']) ?></p>
        </div>
        <div>
          <?= statusBadge($room['status']) ?>
        </div>
      </div>
      
      <div class="detail-row"><span class="detail-key">Tipe</span><span class="detail-val"><?= htmlspecialchars($room['type']) ?> (<?= htmlspecialchars($room['rent']) ?>)</span></div>
      <div class="detail-row"><span class="detail-key">Harga</span><span class="detail-val"><?= fmtRupiah($room['price']) ?></span></div>
      <div class="detail-row"><span class="detail-key">Fasilitas</span><span class="detail-val"><?= htmlspecialchars($room['facilities'] ?: '-') ?></span></div>
      
      <div class="detail-row">
        <span class="detail-key">Sewa Hingga</span>
        <span class="detail-val">
          <?php 
          $until = $room['until'];
          if ($until && $until !== '-') {
              $diff = ceil((strtotime($until) - time()) / 86400);
              $cls = 'ok';
              $label = "$diff hari lagi";
              if ($diff <= 7) {
                  $cls = 'danger';
                  $label = $diff <= 0 ? 'Sudah lewat!' : "$diff hari lagi!";
              } elseif ($diff <= 30) {
                  $cls = 'warning';
              }
              echo htmlspecialchars($until) . ' <span class="lease-countdown ' . $cls . '">' . $label . '</span>';
          } else {
              echo '-';
          }
          ?>
        </span>
      </div>
    </div>
  <?php endif; ?>

  <!-- Stats Grid -->
  <div class="stats-grid mb-16">
    <!-- Total Tagihan -->
    <div class="stat-card">
      <div class="icon-wrap ic-blue"><i class="bi bi-receipt"></i></div>
      <div class="label">Total Tagihan</div>
      <div class="value"><?= count($pendingOrders) ?></div>
      <div class="sub">belum dibayar</div>
    </div>
    <!-- Perbaikan Aktif -->
    <div class="stat-card">
      <div class="icon-wrap ic-amber"><i class="bi bi-wrench"></i></div>
      <div class="label">Perbaikan Aktif</div>
      <div class="value"><?= $activeRepairsCount ?></div>
      <div class="sub">sedang proses</div>
    </div>
    <!-- Pengajuan Pending -->
    <div class="stat-card">
      <div class="icon-wrap ic-purple"><i class="bi bi-file-earmark-text"></i></div>
      <div class="label">Pengajuan</div>
      <div class="value"><?= $pendingRequestsCount ?></div>
      <div class="sub">menunggu persetujuan</div>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
