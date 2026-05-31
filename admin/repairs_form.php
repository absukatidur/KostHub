<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$isEdit = !empty($id);
$repair = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM repairs WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $repair = $stmt->get_result()->fetch_assoc();
    if (!$repair) {
        flashMsg("Laporan perbaikan tidak ditemukan.", 'error');
        header('Location: repairs.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isEdit) {
        // Edit Mode (Updates status and tech)
        $status = $_POST['status'] ?? 'pending';
        $tech = trim($_POST['tech'] ?? '-');
        if (empty($tech)) $tech = '-';

        $stmt = $db->prepare("UPDATE repairs SET status = ?, tech = ? WHERE id = ?");
        $stmt->bind_param('sss', $status, $tech, $id);
        if ($stmt->execute()) {
            if ($repair['type'] === 'fasum') {
                $facStatus = 'ok';
                if ($status === 'pending') $facStatus = 'pending';
                if ($status === 'repairing') $facStatus = 'maintenance';
                $facTarget = $repair['target'];
                $facStmt = $db->prepare("UPDATE facilities SET status = ? WHERE name = ?");
                $facStmt->bind_param('ss', $facStatus, $facTarget);
                $facStmt->execute();
            }
            addLog($db, 'Perbaikan diperbarui', "$id status: $status", 'repair');
            flashMsg("Laporan $id berhasil diperbarui.", 'success');
            header('Location: repairs.php');
            exit;
        } else {
            $error = 'Gagal menyimpan pembaruan: ' . $db->error;
        }
    } else {
        // Add Mode
        $type = $_POST['type'] ?? 'kamar';
        $target = $_POST['target'] ?? '';
        $issue = trim($_POST['issue'] ?? '');
        $tech = trim($_POST['tech'] ?? '-');
        if (empty($tech)) $tech = '-';

        if (!$target || !$issue) {
            $error = 'Target dan deskripsi masalah harus diisi';
        } else {
            $nid = nextId($db, 'repairs', 'REP-');
            $today = date('Y-m-d');
            $reporter = $_SESSION['username'] ?? 'admin';
            $voted_by = json_encode([$reporter]);
            
            $stmt = $db->prepare("INSERT INTO repairs (id, target, type, issue, reported, status, tech, votes, voted_by) VALUES (?, ?, ?, ?, ?, 'pending', ?, 1, ?)");
            $stmt->bind_param('sssssss', $nid, $target, $type, $issue, $today, $tech, $voted_by);
            if ($stmt->execute()) {
                if ($type === 'fasum') {
                    $facStmt = $db->prepare("UPDATE facilities SET status = 'pending' WHERE name = ?");
                    $facStmt->bind_param('s', $target);
                    $facStmt->execute();
                }
                addLog($db, 'Laporan perbaikan', "$nid: $issue – $target", 'repair');
                flashMsg("Laporan perbaikan $nid berhasil dibuat.", 'success');
                header('Location: repairs.php');
                exit;
            } else {
                $error = 'Gagal membuat laporan: ' . $db->error;
            }
        }
    }
}

// Fetch rooms and facilities for add mode
$rooms = $db->query("SELECT id FROM rooms ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$facilities = $db->query("SELECT name FROM facilities ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$pageTitle = ($isEdit ? 'Update Laporan Perbaikan' : 'Laporkan Kerusakan') . ' — KostHub';
$pageTitleShort = 'Perbaikan';

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

<script src="<?= $basePath ?? '' ?>assets/js/admin-repairs-form.js?v=<?= time() ?>"></script>

<?php require_once '../components/footer_scripts.php'; ?>
