<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$type = $_GET['type'] ?? 'pindah';
if (!in_array($type, ['pindah', 'checkout'])) {
    flashMsg("Tipe pengajuan tidak valid.", 'error');
    header('Location: layanan.php');
    exit;
}

$cid = $_SESSION['customer_id'];

// Get customer info
$stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param('s', $cid);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer || empty($customer['room'])) {
    flashMsg("Anda harus menempati kamar untuk melakukan pengajuan.", 'error');
    header('Location: layanan.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $detailData = [];

    if ($type === 'pindah') {
        $toRoom = $_POST['to_room'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        
        if (!$toRoom || !$reason) {
            $error = 'Kamar tujuan dan alasan pindah harus diisi';
        } else {
            $detailData = ['toRoom' => $toRoom, 'reason' => $reason];
        }
    } else {
        $date = $_POST['date'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        
        if (!$date || !$reason) {
            $error = 'Tanggal checkout dan alasan checkout harus diisi';
        } else {
            $detailData = ['date' => $date, 'reason' => $reason];
        }
    }

    if (empty($error)) {
        $nid = nextId($db, 'requests', 'REQ-');
        $detailJson = json_encode($detailData);

        $stmtReq = $db->prepare("INSERT INTO requests (id, customer_id, type, detail, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmtReq->bind_param('ssss', $nid, $cid, $type, $detailJson);
        
        if ($stmtReq->execute()) {
            $typeLabel = $type === 'pindah' ? 'Pindah Kamar' : 'Checkout';
            addLog($db, "Pengajuan $typeLabel", "$nid oleh {$customer['name']}", 'customer');
            flashMsg("Pengajuan $typeLabel berhasil dikirim.", 'success');
            header('Location: layanan.php');
            exit;
        } else {
            $error = 'Gagal menyimpan pengajuan: ' . $db->error;
        }
    }
}

// Fetch all empty/cleaning rooms for pindah dropdown
$rooms = $db->query("SELECT id, type, price FROM rooms WHERE status IN ('empty', 'cleaning') ORDER BY id")->fetch_all(MYSQLI_ASSOC);

$pageTitle = ($type === 'pindah' ? 'Ajukan Pindah Kamar' : 'Pengajuan Checkout') . ' — KostHub';
$pageTitleShort = 'Layanan';

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div style="max-width: 600px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2><?= $type === 'pindah' ? 'Ajukan Pindah Kamar' : 'Pengajuan Checkout' ?></h2>
      <p><?= $type === 'pindah' ? 'Pilih unit kamar kosong dan sampaikan alasan pemindahan Anda' : 'Informasikan rencana tanggal kepindahan Anda dari kos' ?></p>
    </div>
    <a href="layanan.php" class="btn btn-secondary" style="text-decoration: none;">
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
      <!-- Read-only current room info -->
      <div class="detail-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-soft)">
        <span style="color:var(--slate-muted)">Kamar Anda Saat Ini</span>
        <span style="font-weight:600; color:var(--slate-bright)">Kamar <?= htmlspecialchars($customer['room']) ?></span>
      </div>

      <?php if ($type === 'pindah'): ?>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Kamar Tujuan</label>
          <select class="filter-select" style="width:100%" name="to_room" required autofocus>
            <option value="">-- Pilih Kamar Tujuan --</option>
            <?php foreach ($rooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>" <?= (($_POST['to_room'] ?? '') === $r['id']) ? 'selected' : '' ?>>
                Kamar <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['type']) ?> (<?= fmtRupiah($r['price']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php else: ?>
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Rencana Tanggal Checkout</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d', strtotime('+30 days'))) ?>" required autofocus />
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Alasan Pengajuan</label>
        <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                  rows="4" name="reason" placeholder="Jelaskan alasan pengajuan Anda..." required><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px">
        <a href="layanan.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
