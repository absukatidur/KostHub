<?php
$basePath = '../';
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
