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

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Update Laporan Perbaikan' : 'Laporkan Kerusakan' ?></h2>
      <p><?= $isEdit ? 'Ubah status perbaikan dan teknisi untuk ' . htmlspecialchars($id) : 'Buat laporan kerusakan unit atau fasilitas umum' ?></p>
    </div>
    <a href="repairs.php" class="btn btn-secondary btn-link">
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
      <?php if ($isEdit): ?>
        <!-- Read-only Details -->
        <div class="detail-row"><span class="detail-key">Target Kerusakan</span><span class="detail-val"><?= htmlspecialchars($repair['target']) ?></span></div>
        <div class="detail-row"><span class="detail-key">Deskripsi Masalah</span><span class="detail-val" style="max-width:70%; text-align:right"><?= htmlspecialchars($repair['issue']) ?></span></div>

        <!-- Form fields -->
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="filter-select-full" name="status" required>
            <option value="pending" <?= $repair['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="repairing" <?= $repair['status'] === 'repairing' ? 'selected' : '' ?>>Sedang Perbaikan</option>
            <option value="done" <?= $repair['status'] === 'done' ? 'selected' : '' ?>>Selesai</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Teknisi</label>
          <input class="form-input" 
                 id="ur-tech" name="tech" value="<?= htmlspecialchars($_POST['tech'] ?? ($repair['tech'] !== '-' ? $repair['tech'] : '')) ?>" placeholder="Nama teknisi" />
        </div>
      <?php else: ?>
        <!-- Add mode Form fields -->
        <div class="form-group">
          <label class="form-label">Jenis Kerusakan</label>
          <select class="filter-select-full" id="nr-type" name="type" onchange="toggleTargetOptions()" required>
            <option value="kamar">Kamar</option>
            <option value="fasum">Fasilitas Umum</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Target</label>
          <select class="filter-select-full" id="nr-target" name="target" required>
            <?php foreach ($rooms as $r): ?>
              <option value="Kamar <?= htmlspecialchars($r['id']) ?>" class="room-opt">Kamar <?= htmlspecialchars($r['id']) ?></option>
            <?php endforeach; ?>
            <?php foreach ($facilities as $f): ?>
              <option value="<?= htmlspecialchars($f['name']) ?>" class="fac-opt" class="d-none"><?= htmlspecialchars($f['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Masalah</label>
          <textarea class="form-input" 
                    rows="4" name="issue" placeholder="Jelaskan kerusakan yang terjadi secara lengkap..." required autofocus><?= htmlspecialchars($_POST['issue'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Teknisi</label>
          <input class="form-input" 
                 name="tech" value="<?= htmlspecialchars($_POST['tech'] ?? '') ?>" placeholder="Nama teknisi (opsional)" />
        </div>
      <?php endif; ?>

      <div class="form-actions">
        <a href="repairs.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Laporan</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/admin-repairs-form.js?v=<?= time() ?>"></script>

<?php require_once '../components/footer_scripts.php'; ?>
