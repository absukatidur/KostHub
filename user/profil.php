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

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Profil Saya</h2>
      <p>Data diri yang terdaftar</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; border-bottom: 1px solid var(--border-soft); padding-bottom: 20px;">
      <div class="avatar" style="width: 64px; height: 64px; font-size: 24px; border-radius: 50%;">
        <?= htmlspecialchars($initial) ?>
      </div>
      <div>
        <h3 style="margin: 0; font-size: 18px; color: var(--slate-white);"><?= htmlspecialchars($customer['name']) ?></h3>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: var(--slate-muted);">
          <i class="bi bi-door-open-fill"></i> 
          <?php if (!empty($customer['room'])): ?>
            Kamar <?= htmlspecialchars($customer['room']) ?>
          <?php else: ?>
            Belum sewa kamar
          <?php endif; ?>
        </p>
      </div>
    </div>

    <div class="detail-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-faint);">
      <span style="color: var(--slate-muted); font-weight: 500;">ID Customer</span>
      <span style="font-family: 'DM Mono', monospace; font-size: 13px; color: var(--slate-bright);"><?= htmlspecialchars($customer['id']) ?></span>
    </div>
    
    <div class="detail-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-faint);">
      <span style="color: var(--slate-muted); font-weight: 500;">Email</span>
      <span style="color: var(--brand-accent);"><?= htmlspecialchars($customer['email']) ?></span>
    </div>

    <div class="detail-row" style="display: flex; justify-content: space-between; padding: 10px 0;">
      <span style="color: var(--slate-muted); font-weight: 500;">WhatsApp</span>
      <span style="color: var(--slate-bright);"><?= htmlspecialchars($customer['wa']) ?></span>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
