<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Profil Saya — KostHub';
$pageTitleShort = 'Profil';

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

$initial = strtoupper(substr($customer['name'], 0, 2));

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Profil Saya</h2>
      <p>Data diri yang terdaftar</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="profile-header">
      <div class="avatar profile-avatar-lg">
        <?= htmlspecialchars($initial) ?>
      </div>
      <div>
        <h3 class="profile-name"><?= htmlspecialchars($customer['name']) ?></h3>
        <p class="profile-sub">
          <i class="bi bi-door-open-fill"></i> 
          <?php if (!empty($customer['room'])): ?>
            Kamar <?= htmlspecialchars($customer['room']) ?>
          <?php else: ?>
            Belum sewa kamar
          <?php endif; ?>
        </p>
      </div>
    </div>

    <div class="detail-row">
      <span class="detail-key">ID Penghuni</span>
      <span class="detail-val td-mono"><?= htmlspecialchars($customer['id']) ?></span>
    </div>
    
    <div class="detail-row">
      <span class="detail-key">Email</span>
      <span class="detail-val text-accent"><?= htmlspecialchars($customer['email']) ?></span>
    </div>

    <div class="detail-row">
      <span class="detail-key">WhatsApp</span>
      <span class="detail-val"><?= htmlspecialchars($customer['wa']) ?></span>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
