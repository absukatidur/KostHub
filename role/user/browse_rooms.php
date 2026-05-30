<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Cari Kamar Tersedia — KostHub';
$pageTitleShort = 'Cari Kamar';

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

$hasActiveRoom = !empty($customer['room']);

// Fetch empty rooms
$rooms = $db->query("SELECT * FROM rooms WHERE status = 'empty' ORDER BY floor, id")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Cari Kamar Tersedia</h2>
      <p>Pilih kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <?php if ($hasActiveRoom): ?>
    <div class="alert alert-warning" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(249, 180, 122, 0.1); border: 1px solid rgba(249, 180, 122, 0.2); color: var(--amber-pale);">
      <i class="bi bi-exclamation-circle-fill" style="margin-right:8px"></i>
      Anda sudah menempati <b>Kamar <?= htmlspecialchars($customer['room']) ?></b>. Jika ingin pindah kamar, silakan ajukan di menu <a href="layanan.php" style="color:var(--brand-accent); font-weight:600; text-decoration:underline;">Layanan Pengajuan</a>.
    </div>
  <?php endif; ?>

  <div class="browse-filters" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
    <select class="filter-select" id="bf-type">
      <option value="">Semua Tipe</option>
      <option value="Standar">Standar</option>
      <option value="VIP">VIP</option>
      <option value="Executive">Executive</option>
    </select>
    <select class="filter-select" id="bf-floor">
      <option value="">Semua Lantai</option>
      <option value="1">Lantai 1</option>
      <option value="2">Lantai 2</option>
      <option value="3">Lantai 3</option>
    </select>
  </div>

  <!-- Rooms Cards Grid -->
  <div class="browse-rooms-grid" id="rooms-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
    <?php if (empty($rooms)): ?>
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--slate-muted)">
        Tidak ada kamar kosong yang tersedia saat ini.
      </div>
    <?php else: ?>
      <?php foreach ($rooms as $r): ?>
        <div class="room-browse-card" data-type="<?= htmlspecialchars($r['type']) ?>" data-floor="<?= htmlspecialchars($r['floor']) ?>" style="background: var(--bg-card); border: 1px solid var(--border-dim); border-radius: 12px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s;">
          <div>
            <div class="rbc-header" style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span class="badge badge-blue"><?= htmlspecialchars($r['type']) ?></span>
              <span class="badge badge-gray">Lantai <?= htmlspecialchars($r['floor']) ?></span>
            </div>
            <div class="rbc-body" style="margin-bottom: 16px;">
              <h3 style="margin: 0 0 6px 0; font-size: 18px; color: var(--slate-white)">Kamar <?= htmlspecialchars($r['id']) ?></h3>
              <div style="font-size: 13.5px; color: var(--slate-muted); line-height: 1.5; margin-bottom: 8px;">
                <i class="bi bi-info-circle"></i> Tipe Sewa: <b><?= htmlspecialchars($r['rent']) ?></b>
              </div>
              <div style="font-size: 12.5px; color: var(--slate-mid); line-height: 1.4; border-top: 1px solid var(--border-faint); padding-top: 8px;">
                <?= htmlspecialchars($r['facilities'] ?: 'Fasilitas standar kos') ?>
              </div>
              <div style="margin-top: 14px; display: flex; align-items: baseline; gap: 4px;">
                <span style="font-size: 20px; font-weight: 700; color: var(--brand-accent)"><?= fmtRupiah($r['price']) ?></span>
                <span style="font-size: 11px; color: var(--slate-muted)">/ <?= htmlspecialchars(strtolower($r['rent'])) ?></span>
              </div>
            </div>
          </div>
          <div class="rbc-footer">
            <?php if ($hasActiveRoom): ?>
              <button class="btn btn-secondary w-full" style="width:100%" disabled>Pesan Kamar</button>
            <?php else: ?>
              <a href="book.php?id=<?= urlencode($r['id']) ?>" class="btn btn-primary" style="width: 100%; justify-content: center; text-decoration: none;">
                <i class="bi bi-calendar-check"></i> Pesan Kamar
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="browse-empty" id="empty-msg" style="display: none; text-align: center; padding: 40px; color: var(--slate-muted)">
    <i class="bi bi-search" style="font-size: 40px; margin-bottom: 12px; display: block;"></i>
    <div style="font-size: 16px; font-weight: 600; color: var(--slate-white);">Tidak ada kamar tersedia</div>
    <div style="font-size: 13px; color: var(--slate-muted);">Coba ubah filter pencarian Anda</div>
  </div>
</div>

<script>
<script src="<?= $basePath ?? '' ?>assets/js/user-browse-rooms.js?v=<?= time() ?>"></script>
</script>

<?php require_once '../components/user_footer_scripts.php'; ?>
