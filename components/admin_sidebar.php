<?php
$currentScript = basename($_SERVER['SCRIPT_NAME']);

// Query badge counts directly from database
$orderBadge = 0;
$orderBadgeQuery = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
if ($orderBadgeQuery) {
    $orderBadge = $orderBadgeQuery->fetch_assoc()['count'];
}

$repairBadge = 0;
$repairBadgeQuery = $db->query("SELECT COUNT(*) as count FROM repairs WHERE status != 'done'");
if ($repairBadgeQuery) {
    $repairBadge = $repairBadgeQuery->fetch_assoc()['count'];
}

$reqBadge = 0;
$reqBadgeQuery = $db->query("SELECT COUNT(*) as count FROM requests WHERE status = 'pending'");
if ($reqBadgeQuery) {
    $reqBadge = $reqBadgeQuery->fetch_assoc()['count'];
}
?>
<!-- ══════════════════════════ SIDEBAR ══════════════════════════ -->
<nav id="sidebar">
  <div class="sidebar-logo">
    <h1>Kost<span>Hub</span></h1>
    <p>v1.0.0 · <?= htmlspecialchars($_SESSION['role'] ?? 'admin') ?></p>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-label">Utama</div>
      <a href="dashboard.php" class="nav-item <?= $currentScript === 'dashboard.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-label">Master Data</div>
      <a href="rooms.php" class="nav-item <?= ($currentScript === 'rooms.php' || $currentScript === 'rooms_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-door-open"></i> Tipe Kamar
      </a>
      <a href="customers.php" class="nav-item <?= ($currentScript === 'customers.php' || $currentScript === 'customers_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-people"></i> Customer
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-label">Operasional</div>
      <a href="manage_rooms.php" class="nav-item <?= $currentScript === 'manage_rooms.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-grid"></i> Manajemen Kamar
      </a>
      <a href="orders.php" class="nav-item <?= ($currentScript === 'orders.php' || $currentScript === 'orders_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-file-text"></i> Order / Sewa 
        <span class="badge" id="order-badge"><?= $orderBadge > 0 ? $orderBadge : '' ?></span>
      </a>
      <a href="transfer.php" class="nav-item <?= $currentScript === 'transfer.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-arrow-left-right"></i> Pindah Kamar
      </a>
      <a href="repairs.php" class="nav-item <?= ($currentScript === 'repairs.php' || $currentScript === 'repairs_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-wrench"></i> Perbaikan 
        <span class="badge" id="repair-badge"><?= $repairBadge > 0 ? $repairBadge : '' ?></span>
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-label">Lainnya</div>
      <a href="facilities.php" class="nav-item <?= ($currentScript === 'facilities.php' || $currentScript === 'facilities_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-buildings"></i> Fasilitas Umum
      </a>
      <a href="logs.php" class="nav-item <?= $currentScript === 'logs.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-card-text"></i> Log Aktivitas
      </a>
      <a href="requests.php" class="nav-item <?= $currentScript === 'requests.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-inbox"></i> Permintaan User 
        <span class="badge" id="req-badge"><?= $reqBadge > 0 ? $reqBadge : '' ?></span>
      </a>
    </div>
    <?php if ($_SESSION['role'] === 'owner'): ?>
    <div class="nav-section">
      <div class="nav-label">Owner Tools</div>
      <a href="admins.php" class="nav-item <?= ($currentScript === 'admins.php' || $currentScript === 'admins_form.php') ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-shield-lock"></i> Kelola Admin
      </a>
      <a href="financial_reports.php" class="nav-item <?= $currentScript === 'financial_reports.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
      </a>
    </div>
    <?php endif; ?>
  </div>
  <div class="sidebar-footer">
    <?php if ($_SESSION['role'] === 'owner'): ?>
      <a href="reset.php" onclick="return confirm('Apakah Anda yakin ingin mereset semua data?');" class="nav-item" style="color:var(--slate-base); text-decoration: none;">
        <i class="bi bi-arrow-counterclockwise"></i> Reset Data
      </a>
    <?php endif; ?>
    <a href="../logout.php" class="nav-item" style="color:#ef4444; text-decoration: none;">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </a>
  </div>
</nav>
