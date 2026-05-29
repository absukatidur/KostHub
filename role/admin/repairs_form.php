<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Update Laporan Perbaikan' : 'Laporkan Kerusakan' ?></h2>
      <p><?= $isEdit ? 'Ubah status perbaikan dan teknisi untuk ' . htmlspecialchars($id) : 'Buat laporan kerusakan unit atau fasilitas umum' ?></p>
    </div>
    <a href="repairs.php" class="btn btn-secondary" style="text-decoration: none;">
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
      <?php if ($isEdit): ?>
        <!-- Read-only Details -->
        <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-soft)">
          <span style="color:var(--slate-muted)">Target Kerusakan</span>
          <span style="font-weight:600; color:var(--slate-bright)"><?= htmlspecialchars($repair['target']) ?></span>
        </div>
        <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-soft)">
          <span style="color:var(--slate-muted)">Deskripsi Masalah</span>
          <span style="color:var(--slate-bright); max-width:70%; text-align:right"><?= htmlspecialchars($repair['issue']) ?></span>
        </div>

        <!-- Form fields -->
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Status</label>
          <select class="filter-select" style="width:100%" name="status" required>
            <option value="pending" <?= $repair['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="repairing" <?= $repair['status'] === 'repairing' ? 'selected' : '' ?>>Sedang Perbaikan</option>
            <option value="done" <?= $repair['status'] === 'done' ? 'selected' : '' ?>>Selesai</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Teknisi</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 id="ur-tech" name="tech" value="<?= htmlspecialchars($_POST['tech'] ?? ($repair['tech'] !== '-' ? $repair['tech'] : '')) ?>" placeholder="Nama teknisi" />
        </div>
      <?php else: ?>
        <!-- Add mode Form fields -->
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Jenis Kerusakan</label>
          <select class="filter-select" style="width:100%" id="nr-type" name="type" onchange="toggleTargetOptions()" required>
            <option value="kamar">Kamar</option>
            <option value="fasum">Fasilitas Umum</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Target</label>
          <select class="filter-select" style="width:100%" id="nr-target" name="target" required>
            <?php foreach ($rooms as $r): ?>
              <option value="Kamar <?= htmlspecialchars($r['id']) ?>" class="room-opt">Kamar <?= htmlspecialchars($r['id']) ?></option>
            <?php endforeach; ?>
            <?php foreach ($facilities as $f): ?>
              <option value="<?= htmlspecialchars($f['name']) ?>" class="fac-opt" style="display:none"><?= htmlspecialchars($f['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Deskripsi Masalah</label>
          <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                    rows="4" name="issue" placeholder="Jelaskan kerusakan yang terjadi secara lengkap..." required autofocus><?= htmlspecialchars($_POST['issue'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Teknisi</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 name="tech" value="<?= htmlspecialchars($_POST['tech'] ?? '') ?>" placeholder="Nama teknisi (opsional)" />
        </div>
      <?php endif; ?>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="repairs.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Laporan</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleTargetOptions() {
  const type = document.getElementById('nr-type').value;
  const targetSelect = document.getElementById('nr-target');
  const options = targetSelect.options;
  
  let firstVisibleIndex = -1;
  for (let i = 0; i < options.length; i++) {
    const opt = options[i];
    if (type === 'kamar') {
      if (opt.classList.contains('room-opt')) {
        opt.style.display = '';
        if (firstVisibleIndex === -1) firstVisibleIndex = i;
      } else {
        opt.style.display = 'none';
      }
    } else {
      if (opt.classList.contains('fac-opt')) {
        opt.style.display = '';
        if (firstVisibleIndex === -1) firstVisibleIndex = i;
      } else {
        opt.style.display = 'none';
      }
    }
  }
  targetSelect.selectedIndex = firstVisibleIndex;
}
</script>

<?php require_once '../components/footer_scripts.php'; ?>
