<?php
$basePath = '../';
require_once '../includes/db.php';
requireOwner();

$pageTitle = 'Laporan Keuangan — KostHub';
$pageTitleShort = 'Laporan Keuangan';

// Default date range: last 6 months to today
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-6 months'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Query orders in date range
$stmt = $db->prepare("SELECT * FROM orders WHERE start >= ? AND start <= ? ORDER BY start DESC, id DESC");
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$totalRev = 0;
$pendingInv = 0;
$paidCount = 0;
$pendingCount = 0;

foreach ($orders as $o) {
    if ($o['status'] === 'paid') {
        $totalRev += $o['total'];
        $paidCount++;
    } elseif ($o['status'] === 'pending') {
        $pendingInv += $o['total'];
        $pendingCount++;
    }
}

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Laporan Keuangan</h2>
      <p>Analisis pendapatan dan invoice penyewaan kamar</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <!-- Date Filter Form -->
  <div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="financial_reports.php" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label class="form-label" for="start_date" style="margin-bottom: 6px; font-weight: 500; color: var(--slate-text);">Mulai Tanggal</label>
        <input class="form-input" type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required />
      </div>
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label class="form-label" for="end_date" style="margin-bottom: 6px; font-weight: 500; color: var(--slate-text);">Sampai Tanggal</label>
        <input class="form-input" type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required />
      </div>
      <div style="display: flex; gap: 8px;">
        <button type="submit" class="btn btn-primary" style="padding: 9px 16px;">
          <i class="bi bi-filter"></i> Filter
        </button>
        <a href="financial_reports.php" class="btn btn-secondary" style="padding: 9px 16px; text-decoration: none; display: inline-flex; align-items: center;">
          Reset
        </a>
      </div>
    </form>
  </div>

  <!-- Stats Cards Grid -->
  <div class="stats-grid" style="margin-bottom: 20px;">
    <div class="stat-card">
      <div class="icon-wrap ic-green"><i class="bi bi-cash-stack" style="font-size:16px"></i></div>
      <div class="label">Total Pendapatan (Lunas)</div>
      <div class="value" style="font-size: 22px;"><?= fmtRupiah($totalRev) ?></div>
      <div class="sub"><?= $paidCount ?> transaksi lunas</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-amber"><i class="bi bi-clock" style="font-size:16px"></i></div>
      <div class="label">Invoice Pending (Belum Bayar)</div>
      <div class="value" style="font-size: 22px;"><?= fmtRupiah($pendingInv) ?></div>
      <div class="sub"><?= $pendingCount ?> invoice pending</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-blue"><i class="bi bi-calculator" style="font-size:16px"></i></div>
      <div class="label">Total Nilai Transaksi</div>
      <div class="value" style="font-size: 22px;"><?= fmtRupiah($totalRev + $pendingInv) ?></div>
      <div class="sub">Dari <?= count($orders) ?> total order</div>
    </div>
  </div>

  <!-- Detailed Transaction Table -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Rincian Transaksi Penyewaan</span>
      <div class="search-wrap" style="max-width: 300px; margin-bottom: 0;">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
        <input id="report-search" placeholder="Cari ID order, nama, kamar..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Customer</th>
            <th>Kamar</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Total Bayar</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="report-tbody">
          <?php if (empty($orders)): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--slate-muted)">Tidak ada data transaksi pada periode ini</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--brand-accent)"><?= htmlspecialchars($o['id']) ?></span></td>
                <td><div style="font-weight:600"><?= htmlspecialchars($o['customer']) ?></div></td>
                <td><b><?= htmlspecialchars($o['room']) ?></b></td>
                <td><?= htmlspecialchars($o['start']) ?></td>
                <td><?= htmlspecialchars($o['end']) ?></td>
                <td style="font-weight:600"><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <div class="pagination">
      <span class="info" id="report-count">Menampilkan <?= count($orders) ?> order dalam periode ini</span>
    </div>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/table-search.js?v=<?= time() ?>"></script>
<script>
initTableSearch('report-search', '#report-tbody tr', 'report-count', 'Menampilkan {count} order dalam periode ini');
</script>

<?php require_once '../components/footer_scripts.php'; ?>
