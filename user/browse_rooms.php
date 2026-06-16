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
    <div class="alert-warning">
      <i class="bi bi-exclamation-circle-fill"></i>
      Anda sudah menempati <b>Kamar <?= htmlspecialchars($customer['room']) ?></b>. Jika ingin pindah kamar, silakan
      ajukan di menu <a href="layanan.php" class="text-accent font-w600 text-underline">Layanan Pengajuan.</a>
    </div>
  <?php endif; ?>

  <div class="browse-filters">
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
  <div class="browse-rooms-grid" id="rooms-grid">
    <?php if (empty($rooms)): ?>
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--slate-muted)">
        Tidak ada kamar kosong yang tersedia saat ini.
      </div>
    <?php else: ?>
      <?php foreach ($rooms as $r): ?>
        <div class="room-browse-card" data-type="<?= htmlspecialchars($r['type']) ?>"
          data-floor="<?= htmlspecialchars($r['floor']) ?>">
          <div>
            <div class="rbc-header">
              <span class="badge badge-blue"><?= htmlspecialchars($r['type']) ?></span>
              <span class="badge badge-gray">Lantai <?= htmlspecialchars($r['floor']) ?></span>
            </div>
            <div class="rbc-body">
              <h3 class="rbc-room-name">Kamar <?= htmlspecialchars($r['id']) ?></h3>
              <div class="rbc-room-info">
                <i class="bi bi-info-circle"></i> Tipe Sewa: <b><?= htmlspecialchars($r['rent']) ?></b>
              </div>
              <div class="rbc-facilities">
                <?= htmlspecialchars($r['facilities'] ?: 'Fasilitas standar kos') ?>
              </div>
              <div class="rbc-price-row">
                <span class="rbc-price"><?= fmtRupiah($r['price']) ?></span>
                <span class="rbc-rent-type">/ <?= htmlspecialchars(strtolower($r['rent'])) ?></span>
              </div>
            </div>
          </div>
          <div class="rbc-footer">
            <?php if ($hasActiveRoom): ?>
              <button class="btn btn-secondary w-full" disabled>Pesan Kamar</button>
            <?php else: ?>
              <a href="book.php?id=<?= urlencode($r['id']) ?>" class="btn btn-primary w-full btn-link">
                <i class="bi bi-calendar-check"></i> Pesan Kamar
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="browse-empty" id="empty-msg" style="display: none;">
    <i class="bi bi-search"></i>
    <div class="browse-empty-title">Tidak ada kamar tersedia</div>
    <div class="browse-empty-desc">Coba ubah filter pencarian Anda</div>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/user-browse-rooms.js?v=<?= time() ?>"></script>

<?php require_once '../components/user_footer_scripts.php'; ?>