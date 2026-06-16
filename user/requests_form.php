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

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $type === 'pindah' ? 'Ajukan Pindah Kamar' : 'Pengajuan Checkout' ?></h2>
      <p><?= $type === 'pindah' ? 'Pilih unit kamar kosong dan sampaikan alasan pemindahan Anda' : 'Informasikan rencana tanggal kepindahan Anda dari kos' ?></p>
    </div>
    <a href="layanan.php" class="btn btn-secondary btn-link">
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
      <!-- Read-only current room info -->
      <div class="detail-row">
        <span class="detail-key">Kamar Anda Saat Ini</span>
        <span class="detail-val">Kamar <?= htmlspecialchars($customer['room']) ?></span>
      </div>

      <?php if ($type === 'pindah'): ?>
        <div class="form-group">
          <label class="form-label">Kamar Tujuan</label>
          <select class="filter-select w-full" name="to_room" required autofocus>
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
          <label class="form-label">Rencana Tanggal Checkout</label>
          <input class="form-input" 
                 type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d', strtotime('+30 days'))) ?>" required autofocus />
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label">Alasan Pengajuan</label>
        <textarea class="form-input" 
                  rows="4" name="reason" placeholder="Jelaskan alasan pengajuan Anda..." required><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>
      </div>

      <div class="form-actions">
        <a href="layanan.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
