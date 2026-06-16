<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$isEdit = !empty($id);
$facility = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $facility = $stmt->get_result()->fetch_assoc();
    if (!$facility) {
        flashMsg("Fasilitas tidak ditemukan.", 'error');
        header('Location: facilities.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $floor = trim($_POST['floor'] ?? '1');
    $status = $_POST['status'] ?? 'ok';
    $desc = trim($_POST['desc'] ?? '');

    if (!$name) {
        $error = 'Nama fasilitas harus diisi';
    } else {
        if ($isEdit) {
            $stmt = $db->prepare("UPDATE facilities SET name = ?, floor = ?, `desc` = ?, status = ? WHERE id = ?");
            $stmt->bind_param('sssss', $name, $floor, $desc, $status, $id);
            if ($stmt->execute()) {
                addLog($db, 'Fasilitas diperbarui', "$id diperbarui", 'room');
                flashMsg("Fasilitas $name berhasil diperbarui.", 'success');
                header('Location: facilities.php');
                exit;
            } else {
                $error = 'Gagal menyimpan: ' . $db->error;
            }
        } else {
            $nid = nextId($db, 'facilities', 'F');
            $stmt = $db->prepare("INSERT INTO facilities (id, name, floor, `desc`, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $nid, $name, $floor, $desc, $status);
            if ($stmt->execute()) {
                addLog($db, 'Fasilitas ditambah', "$name ($nid)", 'room');
                flashMsg("Fasilitas $name berhasil ditambahkan.", 'success');
                header('Location: facilities.php');
                exit;
            } else {
                $error = 'Gagal menyimpan: ' . $db->error;
            }
        }
    }
}

$pageTitle = ($isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas') . ' — KostHub';
$pageTitleShort = 'Fasilitas Umum';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas' ?></h2>
      <p><?= $isEdit ? 'Perbarui data fasilitas ' . htmlspecialchars($facility['name']) : 'Tambahkan fasilitas bersama baru' ?></p>
    </div>
    <a href="facilities.php" class="btn btn-secondary btn-link">
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
      <div class="form-group">
        <label class="form-label" for="af-name">Nama Fasilitas</label>
        <input class="form-input" 
               id="af-name" name="name" placeholder="misal: Dapur Bersama, WiFi Utama" value="<?= htmlspecialchars($_POST['name'] ?? ($facility['name'] ?? '')) ?>" required autofocus />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="af-floor">Lantai</label>
          <input class="form-input" 
                 id="af-floor" name="floor" placeholder="1" value="<?= htmlspecialchars($_POST['floor'] ?? ($facility['floor'] ?? '1')) ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="af-status">Status</label>
          <select class="filter-select-full" id="af-status" name="status" required>
            <option value="ok" <?= ($_POST['status'] ?? ($facility['status'] ?? 'ok')) === 'ok' ? 'selected' : '' ?>>Normal (OK)</option>
            <option value="pending" <?= ($_POST['status'] ?? ($facility['status'] ?? '')) === 'pending' ? 'selected' : '' ?>>Butuh Perbaikan</option>
            <option value="repairing" <?= ($_POST['status'] ?? ($facility['status'] ?? '')) === 'repairing' ? 'selected' : '' ?>>Sedang Perbaikan</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="af-desc">Deskripsi</label>
        <textarea class="form-input" 
                  rows="3" id="af-desc" name="desc" placeholder="Detail lokasi, kapasitas, atau kelengkapan fasilitas..."><?= htmlspecialchars($_POST['desc'] ?? ($facility['desc'] ?? '')) ?></textarea>
      </div>

      <div class="form-actions">
        <a href="facilities.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
