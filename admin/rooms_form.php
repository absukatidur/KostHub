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

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Kamar' : 'Tambah Kamar' ?></h2>
      <p><?= $isEdit ? 'Perbarui data kamar ' . htmlspecialchars($id) : 'Tambahkan unit kamar kos baru' ?></p>
    </div>
    <a href="rooms.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <form method="POST" autocomplete="off" class="form-stack">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="mk-id">ID Kamar</label>
          <input class="form-input" 
                 id="mk-id" name="id" placeholder="A101" value="<?= htmlspecialchars($_POST['id'] ?? ($room['id'] ?? '')) ?>" required <?= $isEdit ? '' : 'autofocus' ?> />
        </div>
        <div class="form-group">
          <label class="form-label" for="mk-floor">Lantai</label>
          <select class="filter-select-full" id="mk-floor" name="floor" required>
            <?php 
            $selectedFloor = $_POST['floor'] ?? ($room['floor'] ?? 1); 
            for($f = 1; $f <= 3; $f++): ?>
              <option value="<?= $f ?>" <?= $selectedFloor == $f ? 'selected' : '' ?>><?= $f ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="mk-type">Tipe Kamar</label>
          <select class="filter-select-full" id="mk-type" name="type" required>
            <?php 
            $selectedType = $_POST['type'] ?? ($room['type'] ?? 'Standar'); 
            foreach(['Standar', 'VIP', 'Executive'] as $t): ?>
              <option value="<?= $t ?>" <?= $selectedType === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="mk-rent">Tipe Sewa</label>
          <select class="filter-select-full" id="mk-rent" name="rent" required>
            <?php 
            $selectedRent = $_POST['rent'] ?? ($room['rent'] ?? 'Bulanan'); 
            foreach(['Harian', 'Bulanan', 'Tahunan'] as $r): ?>
              <option value="<?= $r ?>" <?= $selectedRent === $r ? 'selected' : '' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="mk-price">Harga Sewa</label>
        <input class="form-input" 
               type="number" id="mk-price" name="price" placeholder="800000" value="<?= htmlspecialchars($_POST['price'] ?? ($room['price'] ?? '')) ?>" required />
      </div>

      <div class="form-group">
        <label class="form-label" for="mk-fac">Fasilitas Kamar</label>
        <textarea class="form-input" 
                  rows="3" id="mk-fac" name="facilities" placeholder="AC, WiFi, Kamar Mandi Dalam..."><?= htmlspecialchars($_POST['facilities'] ?? ($room['facilities'] ?? '')) ?></textarea>
      </div>

      <div class="form-actions">
        <a href="rooms.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
