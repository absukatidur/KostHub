<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'KostHub — Sistem Manajemen Kamar Kos';
$pageTitleShort = 'Dashboard';

// Date Text
$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = [
  'January' => 'Januari',
  'February' => 'Februari',
  'March' => 'Maret',
  'April' => 'April',
  'May' => 'Mei',
  'June' => 'Juni',
  'July' => 'Juli',
  'August' => 'Agustus',
  'September' => 'September',
  'October' => 'Oktober',
  'November' => 'November',
  'December' => 'Desember'
];
$dayName = $days[date('l')];
$monthName = $months[date('F')];
$dateText = $dayName . ', ' . date('j') . ' ' . $monthName . ' ' . date('Y') . ' · Data dari database';

// Fetch stats data
$rooms = $db->query("SELECT * FROM rooms ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$orders = $db->query("SELECT * FROM orders ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$repairs = $db->query("SELECT * FROM repairs")->fetch_all(MYSQLI_ASSOC);
$logs = $db->query("SELECT * FROM logs ORDER BY time DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
$facilities = $db->query("SELECT * FROM facilities")->fetch_all(MYSQLI_ASSOC);

$occupied = count(array_filter($rooms, fn($r) => $r['status'] === 'occupied'));
$empty = count(array_filter($rooms, fn($r) => $r['status'] === 'empty'));
// Count active repairs from repairs table
$maintRooms = count(array_filter($repairs, fn($rep) => $rep['type'] === 'kamar' && $rep['status'] !== 'done'));
$maintFac = count(array_filter($repairs, fn($rep) => $rep['type'] === 'fasum' && $rep['status'] !== 'done'));
$maint = $maintRooms + $maintFac;
$maintRoomsStatus = count(array_filter($rooms, fn($r) => $r['status'] === 'maintenance'));
$totalRooms = count($rooms);

$totalRev = 0;
$pendingInv = 0;
$pendingOrdersCount = 0;
foreach ($orders as $o) {
  if ($o['status'] === 'paid') {
    $totalRev += $o['total'];
  } elseif ($o['status'] === 'pending') {
    $pendingInv += $o['total'];
    $pendingOrdersCount++;
  }
}

// Chart Data — real monthly revenue from paid orders (current year)
$monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$currentYear = date('Y');
$currentMonth = (int) date('n'); // 1-12

// Query monthly totals for paid orders in the current year
$chartQuery = $db->query("SELECT MONTH(start) as m, SUM(total) as rev FROM orders WHERE status = 'paid' AND YEAR(start) = $currentYear GROUP BY MONTH(start)");
$monthlyRev = [];
while ($row = $chartQuery->fetch_assoc()) {
  $monthlyRev[(int) $row['m']] = (int) $row['rev'];
}

// Build arrays for months up to the current month
$chartMonths = [];
$revData = [];
for ($i = 1; $i <= $currentMonth; $i++) {
  $chartMonths[] = $monthNames[$i - 1];
  $revData[] = $monthlyRev[$i] ?? 0;
}
$maxRev = max($revData ?: [1]); // avoid division by zero

// Log Icons & Colors
$logIcons = ['order' => 'file-earmark-text', 'room' => 'door-open', 'customer' => 'person', 'invoice' => 'send', 'repair' => 'wrench'];
$logColors = ['order' => 'ic-blue', 'room' => 'ic-green', 'customer' => 'ic-purple', 'invoice' => 'ic-amber', 'repair' => 'ic-red'];

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Selamat datang, <?= $_SESSION['role'] === 'owner' ? 'Owner' : 'Admin' ?></h2>
      <p><?= htmlspecialchars($dateText) ?></p>
    </div>
    <a href="orders_form.php" class="btn btn-primary btn-link">
      <i class="bi bi-plus-lg" class="fs-14"></i> Buat Order
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="stats-grid">
    <!-- Kamar Kosong -->
    <div class="stat-card">
      <div class="icon-wrap ic-blue"><i class="bi bi-door-open" class="fs-16"></i></div>
      <div class="label">Kamar Kosong</div>
      <div class="value"><?= $empty ?></div>
      <div class="sub">Siap disewa</div>
    </div>
    <!-- Kamar Terisi -->
    <div class="stat-card">
      <div class="icon-wrap ic-green"><i class="bi bi-check-circle" class="fs-16"></i></div>
      <div class="label">Kamar Terisi</div>
      <div class="value"><?= $occupied ?></div>
      <div class="sub">dari <?= $totalRooms ?> kamar</div>
    </div>
    <!-- Perbaikan -->
    <div class="stat-card">
      <div class="icon-wrap ic-red"><i class="bi bi-wrench" class="fs-16"></i></div>
      <div class="label">Perbaikan</div>
      <div class="value"><?= $maint ?></div>
      <div class="sub"><?= $maintRooms ?> kamar · <?= $maintFac ?> fasilitas</div>
    </div>
    <?php if ($_SESSION['role'] === 'owner'): ?>
      <!-- Total Pendapatan -->
      <div class="stat-card">
        <div class="icon-wrap ic-green"><i class="bi bi-cash-stack" class="fs-16"></i></div>
        <div class="label">Total Pendapatan</div>
        <div class="value" class="fs-18"><?= fmtRupiah($totalRev) ?></div>
        <div class="sub">Bulan ini</div>
      </div>
      <!-- Tagihan Tertunda -->
      <div class="stat-card">
        <div class="icon-wrap ic-amber"><i class="bi bi-clock" class="fs-16"></i></div>
        <div class="label">Tagihan Tertunda</div>
        <div class="value" class="fs-18"><?= fmtRupiah($pendingInv) ?></div>
        <div class="sub"><?= $pendingOrdersCount ?> invoice</div>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($_SESSION['role'] === 'owner'): ?>
    <div class="two-col" class="mb-14">
      <div class="card">
        <div class="card-header"><span class="card-title">Pendapatan Penyewaan</span></div>
        <div class="chart-bars">
          <?php foreach ($revData as $i => $v): ?>
            <?php $barHeight = round(($v / $maxRev) * 100); ?>
            <div class="chart-bar-wrap">
              <div class="chart-bar" style="height: <?= $barHeight ?>%;" title="<?= fmtRupiah($v) ?>"></div>
              <span class="chart-bar-label"><?= $chartMonths[$i] ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="text-xs text-muted" style="text-align:center; margin-top:6px">Tahun <?= $currentYear ?> ·
          Hover untuk detail</div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Order Terbaru</span>
          <a href="orders.php" class="btn btn-secondary btn-sm btn-link">Lihat Semua</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php $recentOrders = array_slice($orders, 0, 4); ?>
              <?php if (empty($recentOrders)): ?>
                <tr>
                  <td colspan="3" class="td-empty">Tidak ada order terbaru</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentOrders as $o): ?>
                  <tr>
                    <td>
                      <div class="td-bold"><?= htmlspecialchars($o['customer']) ?></div>
                      <div class="text-xs text-muted"><?= htmlspecialchars($o['id']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($o['room']) ?></td>
                    <td><?= statusBadge($o['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="mb-14">
      <div class="card">
        <div class="card-header">
          <span class="card-title">Order Terbaru</span>
          <a href="orders.php" class="btn btn-secondary btn-sm btn-link">Lihat Semua</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php $recentOrders = array_slice($orders, 0, 4); ?>
              <?php if (empty($recentOrders)): ?>
                <tr>
                  <td colspan="3" class="td-empty">Tidak ada order terbaru</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentOrders as $o): ?>
                  <tr>
                    <td>
                      <div class="td-bold"><?= htmlspecialchars($o['customer']) ?></div>
                      <div class="text-xs text-muted"><?= htmlspecialchars($o['id']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($o['room']) ?></td>
                    <td><?= statusBadge($o['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="two-col">
    <div class="card">
      <div class="card-header"><span class="card-title">Aktivitas Terkini</span></div>
      <div class="activity-list">
        <?php $recentLogs = array_slice($logs, 0, 5); ?>
        <?php if (empty($recentLogs)): ?>
          <div class="td-empty" style="padding:20px">Tidak ada aktivitas</div>
        <?php else: ?>
          <?php foreach ($recentLogs as $l): ?>
            <?php
            $icon = $logIcons[$l['type']] ?? 'circle';
            $color = $logColors[$l['type']] ?? 'ic-gray';
            ?>
            <div class="activity-item">
              <div class="act-dot <?= $color ?>"><i class="bi bi-<?= $icon ?>" class="fs-14"></i></div>
              <div class="act-content">
                <div class="act-title"><?= htmlspecialchars($l['action']) ?></div>
                <div class="act-detail act-meta"><?= htmlspecialchars($l['detail']) ?></div>
                <div class="act-time act-meta"><?= htmlspecialchars($l['time']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <span class="card-title">Overview Kamar</span>
        <a href="rooms.php" class="btn btn-secondary btn-sm btn-link">Detail</a>
      </div>
      <div class="legend">
        <div class="legend-item">
          <div class="legend-dot" style="background: var(--green-mid);"></div>
          <span class="legend-text">Terisi (<?= $occupied ?>)</span>
        </div>
        <div class="legend-item">
          <div class="legend-dot" style="background: var(--blue-muted);"></div>
          <span class="legend-text">Kosong (<?= $empty ?>)</span>
        </div>
        <div class="legend-item">
          <div class="legend-dot" style="background: var(--red-mid);"></div>
          <span class="legend-text">Perbaikan (<?= $maintRoomsStatus ?>)</span>
        </div>
      </div>
      <div class="room-grid">
        <?php foreach ($rooms as $r): ?>
          <div class="room-cell <?= htmlspecialchars($r['status']) ?>"
            onclick="location.href='rooms_detail.php?id=<?= urlencode($r['id']) ?>'">
            <div class="room-num"><?= htmlspecialchars($r['id']) ?></div>
            <div class="room-type"><?= htmlspecialchars($r['type']) ?></div>
            <div class="room-status-text" style="font-size:10px;margin-top:2px;opacity:.7">
              <?= $r['status'] === 'occupied' ? 'Terisi' : ($r['status'] === 'empty' ? 'Kosong' : ($r['status'] === 'cleaning' ? 'Cleaning' : 'Perbaikan')) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>