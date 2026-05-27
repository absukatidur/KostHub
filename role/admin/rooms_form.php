<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$isEdit = !empty($id);
$room = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();
    if (!$room) {
        flashMsg("Kamar tidak ditemukan.", 'error');
        header('Location: rooms.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = trim($_POST['id'] ?? '');
    $floor = intval($_POST['floor'] ?? 1);
    $type = $_POST['type'] ?? 'Standar';
    $rent = $_POST['rent'] ?? 'Bulanan';
    $price = intval($_POST['price'] ?? 0);
    $facilities = trim($_POST['facilities'] ?? '');

    if (!$newId) {
        $error = 'ID Kamar harus diisi';
    } else {
        if ($isEdit) {
            // Edit Mode
            $idChanged = ($newId !== $id);
            $canUpdate = true;

            if ($idChanged) {
                // Check if new ID already exists
                $chk = $db->prepare("SELECT id FROM rooms WHERE id = ?");
                $chk->bind_param('s', $newId);
                $chk->execute();
                if ($chk->get_result()->num_rows > 0) {
                    $error = 'ID Kamar baru sudah digunakan oleh kamar lain';
                    $canUpdate = false;
                }
            }

            if ($canUpdate) {
                $stmt = $db->prepare("UPDATE rooms SET id = ?, floor = ?, type = ?, rent = ?, price = ?, facilities = ? WHERE id = ?");
                $stmt->bind_param('sisssss', $newId, $floor, $type, $rent, $price, $facilities, $id);
                if ($stmt->execute()) {
                    if ($idChanged) {
                        $db->query("UPDATE customers SET room='$newId' WHERE room='$id'");
                        $db->query("UPDATE orders SET room='$newId' WHERE room='$id'");
                        $db->query("UPDATE repairs SET target='$newId' WHERE target='$id'");
                        addLog($db, 'Kamar diperbarui', "ID Kamar $id diganti menjadi $newId", 'room');
                    } else {
                        addLog($db, 'Kamar diperbarui', "Kamar $id diperbarui", 'room');
                    }
                    flashMsg("Kamar berhasil diperbarui.", 'success');
                    header('Location: rooms.php');
                    exit;
                } else {
                    $error = 'Gagal menyimpan: ' . $db->error;
                }
            }
        } else {
            // Add Mode
            // Check if ID already exists
            $chk = $db->prepare("SELECT id FROM rooms WHERE id = ?");
            $chk->bind_param('s', $newId);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $error = 'ID Kamar sudah digunakan';
            } else {
                $stmt = $db->prepare("INSERT INTO rooms (id, floor, type, rent, price, status, tenant, `until`, facilities) VALUES (?, ?, ?, ?, ?, 'empty', '-', '-', ?)");
                $stmt->bind_param('sisssi', $newId, $floor, $type, $rent, $price, $facilities);
                if ($stmt->execute()) {
                    addLog($db, 'Kamar ditambah', "Kamar $newId ditambahkan", 'room');
                    flashMsg("Kamar $newId berhasil ditambahkan.", 'success');
                    header('Location: rooms.php');
                    exit;
                } else {
                    $error = 'Gagal menyimpan: ' . $db->error;
                }
            }
        }
    }
}

$pageTitle = ($isEdit ? 'Edit Kamar' : 'Tambah Kamar') . ' — KostHub';
$pageTitleShort = 'Tipe Kamar';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Kamar' : 'Tambah Kamar' ?></h2>
      <p><?= $isEdit ? 'Perbarui data kamar ' . htmlspecialchars($id) : 'Tambahkan unit kamar kos baru' ?></p>
    </div>
    <a href="rooms.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <form method="POST" autocomplete="off" style="display:flex; flex-direction:column; gap:16px">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" for="mk-id" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">ID Kamar</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 id="mk-id" name="id" placeholder="A101" value="<?= htmlspecialchars($_POST['id'] ?? ($room['id'] ?? '')) ?>" required <?= $isEdit ? '' : 'autofocus' ?> />
        </div>
        <div class="form-group">
          <label class="form-label" for="mk-floor" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Lantai</label>
          <select class="filter-select" style="width:100%" id="mk-floor" name="floor" required>
            <?php 
            $selectedFloor = $_POST['floor'] ?? ($room['floor'] ?? 1); 
            for($f = 1; $f <= 3; $f++): ?>
              <option value="<?= $f ?>" <?= $selectedFloor == $f ? 'selected' : '' ?>><?= $f ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" for="mk-type" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tipe Kamar</label>
          <select class="filter-select" style="width:100%" id="mk-type" name="type" required>
            <?php 
            $selectedType = $_POST['type'] ?? ($room['type'] ?? 'Standar'); 
            foreach(['Standar', 'VIP', 'Executive'] as $t): ?>
              <option value="<?= $t ?>" <?= $selectedType === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="mk-rent" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Tipe Sewa</label>
          <select class="filter-select" style="width:100%" id="mk-rent" name="rent" required>
            <?php 
            $selectedRent = $_POST['rent'] ?? ($room['rent'] ?? 'Bulanan'); 
            foreach(['Harian', 'Bulanan', 'Tahunan'] as $r): ?>
              <option value="<?= $r ?>" <?= $selectedRent === $r ? 'selected' : '' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="mk-price" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Harga Sewa</label>
        <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
               type="number" id="mk-price" name="price" placeholder="800000" value="<?= htmlspecialchars($_POST['price'] ?? ($room['price'] ?? '')) ?>" required />
      </div>

      <div class="form-group">
        <label class="form-label" for="mk-fac" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Fasilitas Kamar</label>
        <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                  rows="3" id="mk-fac" name="facilities" placeholder="AC, WiFi, Kamar Mandi Dalam..."><?= htmlspecialchars($_POST['facilities'] ?? ($room['facilities'] ?? '')) ?></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="rooms.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
