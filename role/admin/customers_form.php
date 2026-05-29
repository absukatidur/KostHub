<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Customer' : 'Tambah Customer' ?></h2>
      <p><?= $isEdit ? 'Perbarui data diri penghuni ' . htmlspecialchars($id) : 'Tambahkan data diri penghuni kos baru' ?></p>
    </div>
    <a href="customers.php" class="btn btn-secondary" style="text-decoration: none;">
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
        <label class="form-label" for="ac-name" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Nama Lengkap</label>
        <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
               id="ac-name" name="name" placeholder="Nama penghuni" value="<?= htmlspecialchars($_POST['name'] ?? ($customer['name'] ?? '')) ?>" required autofocus />
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <div class="form-group">
          <label class="form-label" for="ac-email" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Email</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="email" id="ac-email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($_POST['email'] ?? ($customer['email'] ?? '')) ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="ac-wa" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">WhatsApp</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 id="ac-wa" name="wa" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['wa'] ?? ($customer['wa'] ?? '')) ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="ac-room" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Kamar yang Ditempati</label>
        <select class="filter-select" style="width:100%" id="ac-room" name="room">
          <option value="" <?= (($_POST['room'] ?? ($customer['room'] ?? '')) === '') ? 'selected' : '' ?>>Belum Menyewa Kamar (Kosong)</option>
          <?php foreach ($rooms as $r): ?>
            <?php 
            $isSelected = ($_POST['room'] ?? ($customer['room'] ?? '')) === $r['id'];
            $roomLabel = $r['id'] . ' - ' . $r['type'] . ' (' . ($r['status'] === 'empty' ? 'Kosong' : 'Cleaning') . ')';
            if ($isEdit && $customer['room'] === $r['id']) {
                $roomLabel = $r['id'] . ' - ' . $r['type'] . ' (Kamar Sekarang)';
            }
            ?>
            <option value="<?= htmlspecialchars($r['id']) ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars($roomLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="customers.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Customer</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
