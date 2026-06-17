<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$id = $_GET['id'] ?? '';
if (!$id) {
  flashMsg("ID Kamar tidak valid.", 'error');
  header('Location: browse_rooms.php');
  exit;
}

// Taking the custID for current session into var
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

// Check if customer already occupies a room
if (!empty($customer['room'])) {
  flashMsg("Anda sudah memiliki kamar aktif. Silakan ajukan checkout terlebih dahulu.", 'error');
  header('Location: browse_rooms.php');
  exit;
}

// Verify room is empty
$stmt_room = $db->prepare("SELECT * FROM rooms WHERE id = ? AND status = 'empty'");
$stmt_room->bind_param('s', $id);
$stmt_room->execute();
$room = $stmt_room->get_result()->fetch_assoc();

if (!$room) {
  flashMsg("Kamar sudah tidak tersedia untuk dipesan.", 'error');
  header('Location: browse_rooms.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = $_POST['type'] ?? 'Bulanan';
  $start = $_POST['start'] ?? '';
  $end = $_POST['end'] ?? '';
  $total = intval($_POST['total'] ?? 0);

  if (!$start || !$end || $total <= 0) {
    $error = 'Semua field wajib diisi dengan benar';
  } else {
    $db->begin_transaction();
    try {
      // Re-verify room is empty
      $rs = $db->prepare("SELECT id FROM rooms WHERE id = ? AND status = 'empty' FOR UPDATE");
      $rs->bind_param('s', $id);
      $rs->execute();
      if ($rs->get_result()->num_rows === 0) {
        throw new Exception("Kamar sudah dipesan oleh orang lain baru-baru ini.");
      }

      $orderId = nextId($db, 'orders', 'ORD-');

      // Create order
      $stmtOrd = $db->prepare("INSERT INTO orders (id, customer, room, type, `start`, `end`, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
      $stmtOrd->bind_param('ssssssi', $orderId, $customer['name'], $id, $type, $start, $end, $total);
      $stmtOrd->execute();

      // Update room
      $stmtRoom = $db->prepare("UPDATE rooms SET status = 'occupied', tenant = ?, `until` = ? WHERE id = ?");
      $stmtRoom->bind_param('sss', $customer['name'], $end, $id);
      $stmtRoom->execute();

      // Update customer
      $stmtCust = $db->prepare("UPDATE customers SET room = ? WHERE id = ?");
      $stmtCust->bind_param('ss', $id, $cid);
      $stmtCust->execute();

      addLog($db, 'Booking oleh user', "{$customer['name']} memesan Kamar $id ($orderId)", 'order');

      $db->commit();

      flashMsg("Pemesanan Kamar $id berhasil dibuat! Silakan selesaikan pembayaran tagihan Anda.", 'success');
      header("Location: pay.php?id=" . urlencode($orderId));
      exit;
    } catch (Exception $e) {
      $db->rollback();
      $error = $e->getMessage();
    }
  }
}

$pageTitle = 'Pesan Kamar ' . htmlspecialchars($id) . ' — KostHub';
$pageTitleShort = 'Cari Kamar';

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Konfirmasi Pemesanan</h2>
      <p>Lengkapi rincian sewa untuk Kamar <b><?= htmlspecialchars($id) ?></b></p>
    </div>
    <a href="browse_rooms.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card card-mb">
    <div class="card-section-header" style="margin-bottom:14px; padding-bottom:10px">
      <h3 class="card-section-title" style="font-size: 15px;">Detail Kamar</h3>
      <span class="badge badge-blue">Tersedia</span>
    </div>
    <div class="detail-row"><span class="detail-key">ID Kamar</span><span
        class="detail-val"><?= htmlspecialchars($room['id']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tipe / Lantai</span><span
        class="text-bright"><?= htmlspecialchars($room['type']) ?> (Lantai
        <?= htmlspecialchars($room['floor']) ?>)</span></div>
    <div class="detail-row"><span class="detail-key">Harga Sewa</span><span
        class="text-accent td-bold"><?= fmtRupiah($room['price']) ?> /
        <?= htmlspecialchars(strtolower($room['rent'])) ?></span></div>
    <div class="detail-row"><span class="detail-key">Fasilitas</span><span class="text-bright"
        style="max-width:70%; text-align:right"><?= htmlspecialchars($room['facilities'] ?: 'Fasilitas standar kos') ?></span>
    </div>
  </div>

  <div id="book-page-data" data-room-price="<?= intval($room['price']) ?>"></div>
  <div class="card">
    <div class="mb-16">
      <h3 class="card-section-title" style="font-size: 15px;">Rincian Sewa</h3>
    </div>

    <form method="POST" autocomplete="off" class="form-stack">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tipe Sewa</label>
          <select class="filter-select-full" id="bk-type" name="type" onchange="calculatePeriod()" required>
            <option value="Harian" <?= $room['rent'] === 'Harian' ? 'selected' : '' ?>>Harian</option>
            <option value="Bulanan" <?= $room['rent'] === 'Bulanan' ? 'selected' : '' ?>>Bulanan</option>
            <option value="Tahunan" <?= $room['rent'] === 'Tahunan' ? 'selected' : '' ?>>Tahunan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Mulai</label>
          <input class="form-input" type="date" id="bk-start" name="start" value="<?= date('Y-m-d') ?>" required
            onchange="calculatePeriod()" />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal Akhir</label>
        <input class="form-input" type="date" id="bk-end" name="end" required readonly />
      </div>

      <div class="order-summary">
        <div class="order-summary-label">RINGKASAN PEMESANAN</div>
        <div class="order-summary-row">
          <span class="text-muted">Total Pembayaran</span>
          <span id="bk-total-display" class="order-summary-total">Rp 0</span>
        </div>
        <!-- <input type="hidden" id="bk-total" name="total" value="0" /> -->
      </div>

      <div class="form-actions">
        <a href="browse_rooms.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/user-book.js?v=<?= time() ?>"></script>

<?php require_once '../components/user_footer_scripts.php'; ?>