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

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Konfirmasi Pemesanan</h2>
      <p>Lengkapi rincian sewa untuk Kamar <b><?= htmlspecialchars($id) ?></b></p>
    </div>
    <a href="browse_rooms.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card" style="margin-bottom: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom: 1px solid var(--border-soft); padding-bottom:10px">
      <h3 style="margin:0; font-size:15px; color:var(--slate-white)">Detail Kamar</h3>
      <span class="badge badge-blue">Tersedia</span>
    </div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:6px 0;"><span style="color:var(--slate-muted)">ID Kamar</span><span style="color:var(--slate-bright); font-weight:600"><?= htmlspecialchars($room['id']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:6px 0;"><span style="color:var(--slate-muted)">Tipe / Lantai</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($room['type']) ?> (Lantai <?= htmlspecialchars($room['floor']) ?>)</span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:6px 0;"><span style="color:var(--slate-muted)">Harga Sewa</span><span style="color:var(--brand-accent); font-weight:600"><?= fmtRupiah($room['price']) ?> / <?= htmlspecialchars(strtolower($room['rent'])) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:6px 0;"><span style="color:var(--slate-muted)">Fasilitas</span><span style="color:var(--slate-bright); max-width:70%; text-align:right"><?= htmlspecialchars($room['facilities'] ?: 'Fasilitas standar kos') ?></span></div>
  </div>

  <div id="book-page-data" data-room-price="<?= intval($room['price']) ?>"></div>
  <div class="card">
    <div style="margin-bottom: 16px;"><h3 style="margin:0; font-size:15px; color:var(--slate-white)">Rincian Sewa</h3></div>
    
    <form method="POST" autocomplete="off" style="display:flex; flex-direction:column; gap:16px">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tipe Sewa</label>
          <select class="filter-select" style="width:100%" id="bk-type" name="type" onchange="calculatePeriod()" required>
            <option value="Harian" <?= $room['rent'] === 'Harian' ? 'selected' : '' ?>>Harian</option>
            <option value="Bulanan" <?= $room['rent'] === 'Bulanan' ? 'selected' : '' ?>>Bulanan</option>
            <option value="Tahunan" <?= $room['rent'] === 'Tahunan' ? 'selected' : '' ?>>Tahunan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tanggal Mulai</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="date" id="bk-start" name="start" value="<?= date('Y-m-d') ?>" required onchange="calculatePeriod()" />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tanggal Akhir</label>
        <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
               type="date" id="bk-end" name="end" required readonly />
      </div>

      <div style="background:var(--blue-faded); border:1px solid var(--blue-soft); border-radius:8px; padding:14px; margin-top:8px">
        <div style="font-size:12px; font-weight:600; color:var(--brand-accent-hover); margin-bottom:8px">RINGKASAN PEMESANAN</div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <span style="color:var(--slate-muted)">Total Pembayaran</span>
          <span id="bk-total-display" style="color:var(--brand-accent-hover); font-size:16px; font-weight:700">Rp 0</span>
        </div>
        <input type="hidden" id="bk-total" name="total" value="0" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="browse_rooms.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/user-book.js?v=<?= time() ?>"></script>

<?php require_once '../components/user_footer_scripts.php'; ?>
