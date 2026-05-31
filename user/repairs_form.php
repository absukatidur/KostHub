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

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Lapor Kerusakan Baru</h2>
      <p>Laporkan masalah kerusakan di kamar Anda atau fasilitas bersama</p>
    </div>
    <a href="perbaikan.php" class="btn btn-secondary" style="text-decoration: none;">
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
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Jenis Kerusakan</label>
        <select class="filter-select" style="width:100%" id="urp-type" name="type" onchange="toggleFacilityGroup()" required>
          <?php if (!empty($customer['room'])): ?>
            <option value="kamar" <?= (($_POST['type'] ?? '') === 'kamar') ? 'selected' : '' ?>>Kamar Saya (Kamar <?= htmlspecialchars($customer['room']) ?>)</option>
          <?php endif; ?>
          <option value="fasum" <?= (($_POST['type'] ?? '') === 'fasum' || empty($customer['room'])) ? 'selected' : '' ?>>Fasilitas Umum</option>
        </select>
      </div>

      <div class="form-group" id="urp-fasum-group" style="display: <?= (($_POST['type'] ?? '') === 'fasum' || empty($customer['room'])) ? 'block' : 'none' ?>">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Pilih Fasilitas</label>
        <select class="filter-select" style="width:100%" id="urp-fasum" name="facility">
          <option value="">-- Pilih Fasilitas --</option>
          <?php foreach ($facilities as $f): ?>
            <option value="<?= htmlspecialchars($f['name']) ?>" <?= (($_POST['facility'] ?? '') === $f['name']) ? 'selected' : '' ?>><?= htmlspecialchars($f['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Deskripsi Masalah</label>
        <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                  rows="4" name="issue" placeholder="Jelaskan detail kerusakan (misal: AC tidak dingin, air tersumbat, wifi lambat)..." required autofocus><?= htmlspecialchars($_POST['issue'] ?? '') ?></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="perbaikan.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
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
