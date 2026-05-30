<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customer'] ?? '';
    $roomId = $_POST['room'] ?? '';
    $type = $_POST['type'] ?? 'Bulanan';
    $start = $_POST['start'] ?? '';
    $end = $_POST['end'] ?? '';
    $total = intval($_POST['total'] ?? 0);

    if (!$customerName || !$roomId || !$start || !$end || !$total) {
        $error = 'Semua field wajib diisi';
    } else {
        // Double-check if room is still empty/cleaning
        $roomChk = $db->prepare("SELECT price, status FROM rooms WHERE id = ?");
        $roomChk->bind_param('s', $roomId);
        $roomChk->execute();
        $roomData = $roomChk->get_result()->fetch_assoc();

        if (!$roomData) {
            $error = 'Kamar tidak valid';
        } else {
            $nid = nextId($db, 'orders', 'ORD-');
            
            // Insert order
            $stmt = $db->prepare("INSERT INTO orders (id, customer, room, type, `start`, `end`, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param('ssssssi', $nid, $customerName, $roomId, $type, $start, $end, $total);
            
            if ($stmt->execute()) {
                // Update room status
                $stmtRoom = $db->prepare("UPDATE rooms SET status = 'occupied', tenant = ?, `until` = ? WHERE id = ?");
                $stmtRoom->bind_param('sss', $customerName, $end, $roomId);
                $stmtRoom->execute();

                // Update customer room
                $stmtCust = $db->prepare("UPDATE customers SET room = ? WHERE name = ?");
                $stmtCust->bind_param('ss', $roomId, $customerName);
                $stmtCust->execute();

                addLog($db, 'Order dibuat', "$nid oleh $customerName – Kamar $roomId", 'order');
                flashMsg("Order $nid berhasil dibuat.", 'success');
                header('Location: orders.php');
                exit;
            } else {
                $error = 'Gagal menyimpan order: ' . $db->error;
            }
        }
    }
}

// Fetch all customers for select dropdown
$customers = $db->query("SELECT name FROM customers ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Fetch only empty or cleaning rooms
$rooms = $db->query("SELECT id, type, price FROM rooms WHERE status IN ('empty', 'cleaning') ORDER BY id")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Buat Order Baru — KostHub';
$pageTitleShort = 'Order / Penyewaan';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Buat Order Baru</h2>
      <p>Pilih customer dan unit kamar kosong untuk penyewaan baru</p>
    </div>
    <a href="orders.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <form id="order-form" method="POST" autocomplete="off" style="display:flex; flex-direction:column; gap:16px">
      
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Customer</label>
          <select class="filter-select" style="width:100%" id="no-cust" name="customer" required>
            <option value="">-- Pilih Customer --</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= htmlspecialchars($c['name']) ?>" <?= (($_POST['customer'] ?? '') === $c['name']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Kamar</label>
          <select class="filter-select" style="width:100%" id="no-room" name="room" required onchange="calculateEndAndTotal()">
            <option value="">-- Pilih Kamar --</option>
            <?php foreach ($rooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>" data-price="<?= $r['price'] ?>" <?= (($_POST['room'] ?? '') === $r['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['type']) ?> (<?= fmtRupiah($r['price']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tipe Sewa</label>
          <select class="filter-select" style="width:100%" id="no-type" name="type" required onchange="calculateEndAndTotal()">
            <option value="Harian" <?= (($_POST['type'] ?? '') === 'Harian') ? 'selected' : '' ?>>Harian</option>
            <option value="Bulanan" <?= (($_POST['type'] ?? 'Bulanan') === 'Bulanan') ? 'selected' : '' ?>>Bulanan</option>
            <option value="Tahunan" <?= (($_POST['type'] ?? '') === 'Tahunan') ? 'selected' : '' ?>>Tahunan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tanggal Mulai</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="date" id="no-start" name="start" value="<?= htmlspecialchars($_POST['start'] ?? date('Y-m-d')) ?>" required onchange="calculateEndAndTotal()" />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tanggal Akhir</label>
        <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
               type="date" id="no-end" name="end" value="<?= htmlspecialchars($_POST['end'] ?? '') ?>" required readonly />
      </div>

      <div style="background:var(--blue-faded); border:1px solid var(--blue-soft); border-radius:8px; padding:14px; margin-top:8px">
        <div style="font-size:12px; font-weight:600; color:var(--brand-accent-hover); margin-bottom:8px">RINGKASAN ORDER</div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <span style="color:var(--slate-muted)">Total</span>
          <span id="no-total-display" style="color:var(--brand-accent-hover); font-size:16px; font-weight:700">Rp 0</span>
        </div>
        <input type="hidden" id="no-total" name="total" value="0" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="orders.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Order</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/admin-orders-form.js?v=<?= time() ?>"></script>

<?php require_once '../components/footer_scripts.php'; ?>
