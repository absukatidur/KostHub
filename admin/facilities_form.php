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

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas' ?></h2>
      <p><?= $isEdit ? 'Perbarui data fasilitas ' . htmlspecialchars($facility['name']) : 'Tambahkan fasilitas bersama baru' ?></p>
    </div>
    <a href="facilities.php" class="btn btn-secondary" style="text-decoration: none;">
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
      <div class="form-group">
        <label class="form-label" for="af-name" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Nama Fasilitas</label>
        <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
               id="af-name" name="name" placeholder="misal: Dapur Bersama, WiFi Utama" value="<?= htmlspecialchars($_POST['name'] ?? ($facility['name'] ?? '')) ?>" required autofocus />
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" for="af-floor" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Lantai</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 id="af-floor" name="floor" placeholder="1" value="<?= htmlspecialchars($_POST['floor'] ?? ($facility['floor'] ?? '1')) ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="af-status" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Status</label>
          <select class="filter-select" style="width:100%" id="af-status" name="status" required>
            <option value="ok" <?= ($_POST['status'] ?? ($facility['status'] ?? 'ok')) === 'ok' ? 'selected' : '' ?>>Normal (OK)</option>
            <option value="pending" <?= ($_POST['status'] ?? ($facility['status'] ?? '')) === 'pending' ? 'selected' : '' ?>>Butuh Perbaikan</option>
            <option value="maintenance" <?= ($_POST['status'] ?? ($facility['status'] ?? '')) === 'maintenance' ? 'selected' : '' ?>>Sedang Perbaikan</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="af-desc" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Deskripsi</label>
        <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                  rows="3" id="af-desc" name="desc" placeholder="Detail lokasi, kapasitas, atau kelengkapan fasilitas..."><?= htmlspecialchars($_POST['desc'] ?? ($facility['desc'] ?? '')) ?></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="facilities.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
