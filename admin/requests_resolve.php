<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$status = $_GET['status'] ?? ''; // approved or rejected

if (!$id || !in_array($status, ['approved', 'rejected'])) {
    flashMsg("Parameter tidak valid.", 'error');
    header('Location: requests.php');
    exit;
}

$stmt = $db->prepare("
    SELECT r.*, c.name as customer_name, c.room as current_room 
    FROM requests r 
    LEFT JOIN customers c ON r.customer_id = c.id 
    WHERE r.id = ?
");
$stmt->bind_param('s', $id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    flashMsg("Permintaan tidak ditemukan.", 'error');
    header('Location: requests.php');
    exit;
}

if ($req['status'] !== 'pending') {
    flashMsg("Permintaan ini sudah diproses sebelumnya.", 'error');
    header('Location: requests.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['note'] ?? '');

    $db->begin_transaction();
    try {
        $stmt_upd = $db->prepare("UPDATE requests SET status = ?, admin_note = ?, resolved_at = NOW() WHERE id = ?");
        $stmt_upd->bind_param('sss', $status, $note, $id);
        $stmt_upd->execute();

        if ($status === 'approved') {
            if ($req['type'] === 'pindah') {
                $detail = json_decode($req['detail'], true);
                $toRoom = $detail['toRoom'] ?? '';
                if ($toRoom) {
                    $fromRoom = $req['current_room'];
                    // Free old room
                    $db->query("UPDATE rooms SET status='cleaning', tenant='-', `until`='-' WHERE id='$fromRoom'");
                    // Occupy new room
                    $stmt2 = $db->prepare("UPDATE rooms SET status='occupied', tenant=? WHERE id=?");
                    $stmt2->bind_param('ss', $req['customer_name'], $toRoom);
                    $stmt2->execute();
                    // Update customer
                    $db->query("UPDATE customers SET room='$toRoom' WHERE id='{$req['customer_id']}'");
                    
                    addLog($db, 'Pindah kamar (disetujui)', "{$req['customer_name']}: $fromRoom → $toRoom", 'room');
                }
            } elseif ($req['type'] === 'checkout') {
                $roomToClear = $req['current_room'];
                if ($roomToClear) {
                    $stmtRoom = $db->prepare("UPDATE rooms SET status='empty', tenant='-', `until`='-' WHERE id=?");
                    $stmtRoom->bind_param('s', $roomToClear);
                    $stmtRoom->execute();
                }
                $stmtCust = $db->prepare("UPDATE customers SET room='' WHERE id=?");
                $stmtCust->bind_param('s', $req['customer_id']);
                $stmtCust->execute();

                addLog($db, 'Checkout disetujui', "{$req['customer_name']} telah checkout dari $roomToClear", 'customer');
            }
        }

        addLog($db, 'Request ' . $status, "$id $status" . ($note ? " – $note" : ''), 'customer');
        $db->commit();
        
        flashMsg("Permintaan " . ($status === 'approved' ? 'disetujui' : 'ditolak') . " dan diproses.", 'success');
        header('Location: requests.php');
        exit;
    } catch (Exception $e) {
        $db->rollback();
        $error = 'Gagal memproses permintaan: ' . $e->getMessage();
    }
}

$pageTitle = 'Proses Permintaan ' . htmlspecialchars($id) . ' — KostHub';
$pageTitleShort = 'Permintaan User';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2>Konfirmasi Tindakan</h2>
      <p>Proses permintaan user: <b><?= $status === 'approved' ? 'Setujui' : 'Tolak' ?></b></p>
    </div>
    <a href="requests.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card card-mb">
    <div style="margin-bottom: 12px; border-bottom: 1px solid var(--border-soft); padding-bottom: 8px;">
      <h3 class="card-section-title" class="fs-15">Rincian Pengajuan</h3>
    </div>
    <div class="detail-row"><span class="detail-key">Penghuni</span><span class="detail-val"><?= htmlspecialchars($req['customer_name']) ?></span></div>
    <div class="detail-row"><span class="detail-key">Tipe Permintaan</span><span class="detail-val"><?= $req['type'] === 'pindah' ? 'Pindah Kamar' : 'Checkout' ?></span></div>
    <div class="detail-row"><span class="detail-key">Detail</span><span class="text-accent">
      <?php 
      $detail = json_decode($req['detail'] ?: '{}', true);
      if ($req['type'] === 'pindah') {
          echo 'Pindah ke Kamar ' . htmlspecialchars($detail['toRoom'] ?? '?') . ' (Kamar asal: ' . htmlspecialchars($req['current_room'] ?: '-') . ')';
      } else {
          echo 'Rencana Checkout: ' . htmlspecialchars($detail['date'] ?? '-');
      }
      ?>
    </span></div>
    <div class="detail-row"><span class="detail-key">Alasan</span><span class="detail-val" style="max-width: 60%; text-align: right;"><?= htmlspecialchars($detail['reason'] ?? '-') ?></span></div>
  </div>

  <div class="card">
    <form method="POST" autocomplete="off" class="form-stack">
      <div class="form-group">
        <label class="form-label">Catatan Admin (Opsional)</label>
        <textarea class="form-input" 
                  rows="3" name="note" placeholder="Tulis alasan penyetujuan atau penolakan..." autofocus></textarea>
      </div>

      <div class="form-actions" style="margin-top:0">
        <a href="requests.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn <?= $status === 'approved' ? 'btn-success' : 'btn-danger' ?>">
          <?= $status === 'approved' ? 'Konfirmasi Setujui' : 'Konfirmasi Tolak' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
