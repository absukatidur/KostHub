<?php
$currentScript = basename($_SERVER['SCRIPT_NAME']);
$displayName = 'Portal Penghuni';
if (!empty($_SESSION['customer_id'])) {
    $cQuery = $db->prepare("SELECT name FROM customers WHERE id = ?");
    $cQuery->bind_param('s', $_SESSION['customer_id']);
    $cQuery->execute();
    $cResult = $cQuery->get_result()->fetch_assoc();
    if ($cResult) {
        $displayName = $cResult['name'];
    }
}

// Query pending bills/orders for the tenant
$tagihanBadge = 0;
if (!empty($_SESSION['customer_id'])) {
    $roomQuery = $db->prepare("SELECT room FROM customers WHERE id = ?");
    $roomQuery->bind_param('s', $_SESSION['customer_id']);
    $roomQuery->execute();
    $roomRes = $roomQuery->get_result()->fetch_assoc();
    if ($roomRes && !empty($roomRes['room'])) {
        $rName = $roomRes['room'];
        $oQuery = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE room = ? AND status = 'pending'");
        $oQuery->bind_param('s', $rName);
        $oQuery->execute();
        $oRes = $oQuery->get_result()->fetch_assoc();
        if ($oRes) {
            $tagihanBadge = $oRes['count'];
        }
    }
}
?>
<!-- ══════════════════════════ USER SIDEBAR ══════════════════════════ -->
<nav id="sidebar">
  <div class="sidebar-logo">
    <h1>Kost<span>Hub</span></h1>
    <p id="user-subtitle"><?= htmlspecialchars($displayName) ?></p>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-label">Menu</div>
      <a href="dashboard.php" class="nav-item <?= $currentScript === 'dashboard.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="tagihan.php" class="nav-item <?= $currentScript === 'tagihan.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-receipt"></i> Tagihan 
        <span class="badge" id="tagihan-badge"><?= $tagihanBadge > 0 ? $tagihanBadge : '' ?></span>
      </a>
      <a href="perbaikan.php" class="nav-item <?= $currentScript === 'perbaikan.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-wrench"></i> Perbaikan
      </a>
      <a href="fasilitas.php" class="nav-item <?= $currentScript === 'fasilitas.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-buildings"></i> Fasilitas
      </a>
      <a href="browse_rooms.php" class="nav-item <?= $currentScript === 'browse_rooms.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-search"></i> Cari Kamar
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-label">Akun</div>
      <a href="profil.php" class="nav-item <?= $currentScript === 'profil.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-person"></i> Profil
      </a>
      <a href="layanan.php" class="nav-item <?= $currentScript === 'layanan.php' ? 'active' : '' ?>" style="text-decoration: none;">
        <i class="bi bi-file-text"></i> Layanan
      </a>
    </div>
  </div>
  <div class="sidebar-footer">
    <a href="../logout.php" class="nav-item" style="color:#ef4444; text-decoration: none;">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </a>
  </div>
</nav>
