<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$cid = $_SESSION['customer_id'];

// Get customer info
$stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param('s', $cid);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'kamar';
    $facilityName = $_POST['facility'] ?? '';
    $issue = trim($_POST['issue'] ?? '');

    $target = '';
    if ($type === 'kamar') {
        if (empty($customer['room'])) {
            $error = 'Anda belum memiliki kamar untuk dilaporkan';
        } else {
            $target = 'Kamar ' . $customer['room'];
        }
    } else {
        if (empty($facilityName)) {
            $error = 'Fasilitas umum harus dipilih';
        } else {
            $target = $facilityName;
        }
    }

    if (empty($error)) {
        if (empty($issue)) {
            $error = 'Deskripsi masalah harus diisi';
        } else {
            $nid = nextId($db, 'repairs', 'REP-');
            $today = date('Y-m-d');
            $tech = '-';
            $voted_by = json_encode([$cid]);

            $stmtRep = $db->prepare("INSERT INTO repairs (id, target, type, issue, reported, status, tech, votes, voted_by) VALUES (?, ?, ?, ?, ?, 'pending', ?, 1, ?)");
            $stmtRep->bind_param('sssssss', $nid, $target, $type, $issue, $today, $tech, $voted_by);
            if ($stmtRep->execute()) {
                if ($type === 'fasum') {
                    $facStmt = $db->prepare("UPDATE facilities SET status = 'pending' WHERE name = ?");
                    $facStmt->bind_param('s', $target);
                    $facStmt->execute();
                }
                addLog($db, 'Laporan perbaikan', "$nid: $issue – $target", 'repair');
                flashMsg("Laporan kerusakan $nid berhasil dikirim.", 'success');
                header('Location: perbaikan.php');
                exit;
            } else {
                $error = 'Gagal menyimpan laporan: ' . $db->error;
            }
        }
    }
}

// Fetch facilities for select dropdown
$facilities = $db->query("SELECT name FROM facilities ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Lapor Kerusakan Baru — KostHub';
$pageTitleShort = 'Perbaikan';

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Lapor Kerusakan Baru</h2>
      <p>Laporkan masalah kerusakan di kamar Anda atau fasilitas bersama</p>
    </div>
    <a href="perbaikan.php" class="btn btn-secondary btn-link">
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
        <label class="form-label">Jenis Kerusakan</label>
        <select class="filter-select w-full" id="urp-type" name="type" onchange="toggleFacilityGroup()" required>
          <?php if (!empty($customer['room'])): ?>
            <option value="kamar" <?= (($_POST['type'] ?? '') === 'kamar') ? 'selected' : '' ?>>Kamar Saya (Kamar <?= htmlspecialchars($customer['room']) ?>)</option>
          <?php endif; ?>
          <option value="fasum" <?= (($_POST['type'] ?? '') === 'fasum' || empty($customer['room'])) ? 'selected' : '' ?>>Fasilitas Umum</option>
        </select>
      </div>

      <div class="form-group" id="urp-fasum-group" style="display: <?= (($_POST['type'] ?? '') === 'fasum' || empty($customer['room'])) ? 'block' : 'none' ?>">
        <label class="form-label">Pilih Fasilitas</label>
        <select class="filter-select w-full" id="urp-fasum" name="facility">
          <option value="">-- Pilih Fasilitas --</option>
          <?php foreach ($facilities as $f): ?>
            <option value="<?= htmlspecialchars($f['name']) ?>" <?= (($_POST['facility'] ?? '') === $f['name']) ? 'selected' : '' ?>><?= htmlspecialchars($f['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi Masalah</label>
        <textarea class="form-input" 
                  rows="4" name="issue" placeholder="Jelaskan detail kerusakan (misal: AC tidak dingin, air tersumbat, wifi lambat)..." required autofocus><?= htmlspecialchars($_POST['issue'] ?? '') ?></textarea>
      </div>

      <div class="form-actions">
        <a href="perbaikan.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleFacilityGroup() {
  const type = document.getElementById('urp-type').value;
  const group = document.getElementById('urp-fasum-group');
  if (type === 'fasum') {
    group.style.display = 'block';
  } else {
    group.style.display = 'none';
  }
}
</script>

<?php require_once '../components/user_footer_scripts.php'; ?>
