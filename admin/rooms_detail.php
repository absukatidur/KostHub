<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
if (!$id) {
    flashMsg("ID Kamar tidak valid.", 'error');
    header('Location: rooms.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    flashMsg("Kamar tidak ditemukan.", 'error');
    header('Location: rooms.php');
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'empty';
    $tenant = $_POST['tenant'] ?? '-';
    $until = $_POST['until'] ?? '-';

    if ($status !== 'occupied') {
        $tenant = '-';
        $until = '-';
        
        // Also clear room in customers table
        $db->query("UPDATE customers SET room = '' WHERE room = '" . $db->real_escape_string($id) . "'");
    } else {
        if ($tenant !== '-') {
            // Update customer's room
            $db->query("UPDATE customers SET room = '" . $db->real_escape_string($id) . "' WHERE name = '" . $db->real_escape_string($tenant) . "'");
        }
    }

    $stmt = $db->prepare("UPDATE rooms SET status = ?, tenant = ?, `until` = ? WHERE id = ?");
    $stmt->bind_param('ssss', $status, $tenant, $until, $id);
    if ($stmt->execute()) {
        addLog($db, 'Status kamar diperbarui', "Status kamar $id diperbarui menjadi $status", 'room');
        flashMsg("Status kamar berhasil diperbarui.", 'success');
    } else {
        flashMsg("Gagal memperbarui status: " . $db->error, 'error');
    }
    header("Location: rooms_detail.php?id=" . urlencode($id));
    exit;
}

// Fetch all customers for tenant dropdown
$customers = $db->query("SELECT name FROM customers ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Detail Kamar ' . htmlspecialchars($id) . ' — KostHub';
$pageTitleShort = 'Tipe Kamar';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Detail Kamar <?= htmlspecialchars($id) ?></h2>
      <p>Lihat rincian unit dan ubah status operasional</p>
    </div>
    <a href="rooms.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card card-mb">
    <div class="card-section-header">
      <h3 class="card-section-title">Informasi Unit</h3>
      <div><?= statusBadge($room['status']) ?></div>
    </div>
    
    <div class="detail-row"><span class="detail-key">ID Kamar</span><span class="detail-val"><?= htmlspecialchars($room['id']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Lantai</span><span class="detail-val">Lantai <?= htmlspecialchars($room['floor']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tipe</span><span class="detail-val"><?= htmlspecialchars($room['type']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val"><?= htmlspecialchars($room['rent']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Harga</span><span class="detail-val"><?= fmtRupiah($room['price']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Penghuni</span><span class="detail-val"><?= htmlspecialchars($room['tenant']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Sewa Hingga</span><span class="detail-val"><?= htmlspecialchars($room['until']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Fasilitas</span><span class="detail-val" style="max-width:70%; text-align:right"><?= htmlspecialchars($room['facilities'] ?: '-') ?></span></div>
  </div>

  <div class="card">
    <div class="mb-16"><h3 class="card-section-title">Update Status Operasional</h3></div>
    
    <form method="POST" autocomplete="off" class="form-stack">
      <div class="form-group">
        <label class="form-label">Status Kamar</label>
        <select class="filter-select-full" id="status-select" name="status" onchange="toggleTenantFields()" required>
          <option value="empty" <?= $room['status'] === 'empty' ? 'selected' : '' ?>>Kosong</option>
          <option value="occupied" <?= $room['status'] === 'occupied' ? 'selected' : '' ?>>Terisi</option>
          <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Perbaikan</option>
        </select>
      </div>

      <div id="tenant-fields" style="display: <?= $room['status'] === 'occupied' ? 'block' : 'none' ?>; border-top: 1px dashed var(--border-dim); padding-top: 14px;">
        <div class="form-group" style="margin-bottom: 12px">
          <label class="form-label">Nama Penghuni</label>
          <select class="filter-select-full" name="tenant">
            <option value="-" <?= $room['tenant'] === '-' ? 'selected' : '' ?>>- Pilih Penghuni -</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= htmlspecialchars($c['name']) ?>" <?= $room['tenant'] === $c['name'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sewa Hingga</label>
          <input class="form-input" 
                 type="date" name="until" value="<?= htmlspecialchars($room['until'] !== '-' ? $room['until'] : '') ?>" />
        </div>
      </div>

      <div class="form-actions">
        <a href="rooms.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Status</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleTenantFields() {
  const status = document.getElementById('status-select').value;
  const fields = document.getElementById('tenant-fields');
  if (status === 'occupied') {
    fields.style.display = 'block';
  } else {
    fields.style.display = 'none';
  }
}
</script>

<?php require_once '../components/footer_scripts.php'; ?>
