<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Pindah Kamar — KostHub';
$pageTitleShort = 'Pindah Kamar';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fromRoom = $_POST['from_room'] ?? '';
  $toRoom = $_POST['to_room'] ?? '';
  $newEnd = $_POST['new_end'] ?? '-';
  $note = trim($_POST['note'] ?? '');

  if (!$fromRoom || !$toRoom) {
    $error = 'Kamar asal dan kamar tujuan harus dipilih';
  } else {
    // Fetch tenant from source room
    $stmtSource = $db->prepare("SELECT tenant, status FROM rooms WHERE id = ?");
    $stmtSource->bind_param('s', $fromRoom);
    $stmtSource->execute();
    $sourceRoom = $stmtSource->get_result()->fetch_assoc();

    if (!$sourceRoom || $sourceRoom['status'] !== 'occupied') {
      $error = 'Kamar asal tidak terisi oleh penghuni';
    } else if ($fromRoom === $toRoom) {
      $error = 'Kamar asal dan tujuan tidak boleh sama';
    } else {
      // Fetch target room to ensure it's empty
      $stmtDestCheck = $db->prepare("SELECT status FROM rooms WHERE id = ?");
      $stmtDestCheck->bind_param('s', $toRoom);
      $stmtDestCheck->execute();
      $destRoom = $stmtDestCheck->get_result()->fetch_assoc();

      if (!$destRoom || !in_array($destRoom['status'], ['empty', 'cleaning'])) {
        $error = 'Kamar tujuan tidak tersedia (sedang terisi atau tidak ada)';
      } else {
        $tenant = $sourceRoom['tenant'];

        // 1. Set old room to empty
        $db->query("UPDATE rooms SET status='empty', tenant='-', `until`='-' WHERE id='" . $db->real_escape_string($fromRoom) . "'");

        // 2. Set new room to occupied
        $stmtDest = $db->prepare("UPDATE rooms SET status='occupied', tenant=?, `until`=? WHERE id=?");
        $stmtDest->bind_param('sss', $tenant, $newEnd, $toRoom);
        $stmtDest->execute();

        // 3. Update customer table
        $db->query("UPDATE customers SET room='" . $db->real_escape_string($toRoom) . "' WHERE name='" . $db->real_escape_string($tenant) . "'");

        // 4. Log transfer
        $detail = "$tenant: $fromRoom → $toRoom" . ($note ? " ($note)" : '');
        addLog($db, 'Pindah kamar', $detail, 'room');

        flashMsg("Berhasil memindahkan $tenant dari kamar $fromRoom ke kamar $toRoom.", 'success');
        header('Location: transfer.php');
        exit;
      }
    }
  }
}

$occupiedRooms = $db->query("SELECT id, tenant, `until` FROM rooms WHERE status = 'occupied' ORDER BY id")->fetch_all(MYSQLI_ASSOC);

$emptyRooms = $db->query("SELECT id, type, status FROM rooms WHERE status IN ('empty', 'cleaning') ORDER BY id")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Pindah Kamar</h2>
      <p>Pindahkan penghuni antar kamar</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="two-col">
    <!-- Transfer Form Card -->
    <div class="card">
      <div class="mb-16">
        <h3 class="card-section-title">Form Pindah Kamar</h3>
      </div>

      <form method="POST" autocomplete="off" class="form-stack">
        <div class="form-group">
          <label class="form-label">Pilih Kamar Asal (Penghuni)</label>
          <select class="filter-select-full" name="from_room" required>
            <option value="">-- Pilih Kamar Asal --</option>
            <?php foreach ($occupiedRooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>">
                Kamar <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['tenant']) ?> (Sewa s/d
                <?= htmlspecialchars($r['until']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Pilih Kamar Tujuan</label>
          <select class="filter-select-full" name="to_room" required>
            <option value="">-- Pilih Kamar Tujuan --</option>
            <?php foreach ($emptyRooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>">
                Kamar <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['type']) ?>
                (<?= $r['status'] === 'empty' ? 'Kosong' : 'Cleaning' ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Sewa Hingga di Kamar Baru</label>
          <input class="form-input" type="date" name="new_end" />
        </div>

        <div class="form-group">
          <label class="form-label">Catatan / Alasan Pindah</label>
          <textarea class="form-input"
            rows="3" name="note" placeholder="Ingin kamar yang lebih luas, dll..."></textarea>
        </div>

        <div class="form-actions" style="margin-top:0">
          <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi memindahkan penghuni?');">
            <i class="bi bi-arrow-left-right" style="font-size: 13px;"></i> Pindahkan Penghuni
          </button>
        </div>
      </form>
    </div>

    <!-- Info Column -->
    <div class="flex-col gap-14">
      <!-- Occupied List -->
      <div class="card" class="max-h-250">
        <div class="card-title"
          class="mb-14 pos-sticky">
          Kamar Terisi</div>
        <div class="flex-col gap-8">
          <?php if (empty($occupiedRooms)): ?>
            <div class="td-empty" style="padding:10px;">Semua kamar kosong</div>
          <?php else: ?>
            <?php foreach ($occupiedRooms as $r): ?>
              <div
                class="flex-between-center p-10">
                <div>
                  <div class="td-bold text-bright">Kamar <?= htmlspecialchars($r['id']) ?></div>
                  <div class="text-sm text-muted"><?= htmlspecialchars($r['tenant']) ?></div>
                </div>
                <span class="badge badge-green">Terisi</span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Empty List -->
      <div class="card" class="max-h-250">
        <div class="card-title"
          class="mb-14 pos-sticky">
          Kamar Kosong</div>
        <div class="flex-col gap-8">
          <?php if (empty($emptyRooms)): ?>
            <div class="td-empty" style="padding:10px;">Tidak ada kamar kosong</div>
          <?php else: ?>
            <?php foreach ($emptyRooms as $r): ?>
              <div
                style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; background:var(--green-faded); border:1px solid var(--green-soft); border-radius:8px">
                <div>
                  <div style="font-weight:600; color:var(--green-pale)">Kamar <?= htmlspecialchars($r['id']) ?></div>
                  <div style="font-size:12px; color:var(--green-vivid)"><?= htmlspecialchars($r['type']) ?>
                    (<?= htmlspecialchars($r['status']) ?>)</div>
                </div>
                <span class="badge badge-blue">Kosong</span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>