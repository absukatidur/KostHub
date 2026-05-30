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

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Detail Kamar <?= htmlspecialchars($id) ?></h2>
      <p>Lihat rincian unit dan ubah status operasional</p>
    </div>
    <a href="rooms.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card" style="margin-bottom: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom: 1px solid var(--border-soft); padding-bottom:12px">
      <h3 style="margin:0; font-size:16px; color:var(--slate-white)">Informasi Unit</h3>
      <div><?= statusBadge($room['status']) ?></div>
    </div>
    
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">ID Kamar</span><span class="detail-val" style="font-weight:600; color:var(--slate-bright)"><?= htmlspecialchars($room['id']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Lantai</span><span class="detail-val" style="color:var(--slate-bright)">Lantai <?= htmlspecialchars($room['floor']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Tipe</span><span class="detail-val" style="color:var(--slate-bright)"><?= htmlspecialchars($room['type']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Tipe Sewa</span><span class="detail-val" style="color:var(--slate-bright)"><?= htmlspecialchars($room['rent']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Harga</span><span class="detail-val" style="color:var(--slate-bright)"><?= fmtRupiah($room['price']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Penghuni</span><span class="detail-val" style="color:var(--slate-bright)"><?= htmlspecialchars($room['tenant']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom: 1px solid var(--border-faint)"><span class="detail-key" style="color:var(--slate-muted)">Sewa Hingga</span><span class="detail-val" style="color:var(--slate-bright)"><?= htmlspecialchars($room['until']) ?></span></div>
    <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0;"><span class="detail-key" style="color:var(--slate-muted)">Fasilitas</span><span class="detail-val" style="max-width: 70%; text-align: right; color:var(--slate-bright)"><?= htmlspecialchars($room['facilities'] ?: '-') ?></span></div>
  </div>

  <div class="card">
    <div style="margin-bottom:16px"><h3 style="margin:0; font-size:16px; color:var(--slate-white)">Update Status Operasional</h3></div>
    
    <form method="POST" autocomplete="off" style="display:flex; flex-direction:column; gap:16px">
      <div class="form-group">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Status Kamar</label>
        <select class="filter-select" style="width:100%" id="status-select" name="status" onchange="toggleTenantFields()" required>
          <option value="empty" <?= $room['status'] === 'empty' ? 'selected' : '' ?>>Kosong</option>
          <option value="occupied" <?= $room['status'] === 'occupied' ? 'selected' : '' ?>>Terisi</option>
          <option value="cleaning" <?= $room['status'] === 'cleaning' ? 'selected' : '' ?>>Cleaning</option>
          <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Perbaikan</option>
        </select>
      </div>

      <div id="tenant-fields" style="display: <?= $room['status'] === 'occupied' ? 'block' : 'none' ?>; border-top: 1px dashed var(--border-dim); padding-top: 14px;">
        <div class="form-group" style="margin-bottom: 12px">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Nama Penghuni</label>
          <select class="filter-select" style="width:100%" name="tenant">
            <option value="-" <?= $room['tenant'] === '-' ? 'selected' : '' ?>>- Pilih Customer -</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= htmlspecialchars($c['name']) ?>" <?= $room['tenant'] === $c['name'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Sewa Hingga</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="date" name="until" value="<?= htmlspecialchars($room['until'] !== '-' ? $room['until'] : '') ?>" />
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="rooms.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
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
