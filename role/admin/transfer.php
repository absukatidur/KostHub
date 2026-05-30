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
        } else {
            $tenant = $sourceRoom['tenant'];

            // 1. Set old room to cleaning
            $db->query("UPDATE rooms SET status='cleaning', tenant='-', `until`='-' WHERE id='" . $db->real_escape_string($fromRoom) . "'");

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
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="two-col">
    <!-- Transfer Form Card -->
    <div class="card">
      <div style="margin-bottom:16px"><h3 style="margin:0; font-size:16px; color:var(--slate-white)">Form Pindah Kamar</h3></div>
      
      <form method="POST" autocomplete="off" style="display:flex; flex-direction:column; gap:16px">
        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Pilih Kamar Asal (Penghuni)</label>
          <select class="filter-select" style="width:100%" name="from_room" required>
            <option value="">-- Pilih Kamar Asal --</option>
            <?php foreach ($occupiedRooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>">
                Kamar <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['tenant']) ?> (Sewa s/d <?= htmlspecialchars($r['until']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Pilih Kamar Tujuan</label>
          <select class="filter-select" style="width:100%" name="to_room" required>
            <option value="">-- Pilih Kamar Tujuan --</option>
            <?php foreach ($emptyRooms as $r): ?>
              <option value="<?= htmlspecialchars($r['id']) ?>">
                Kamar <?= htmlspecialchars($r['id']) ?> - <?= htmlspecialchars($r['type']) ?> (<?= $r['status'] === 'empty' ? 'Kosong' : 'Cleaning' ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Sewa Hingga di Kamar Baru</label>
          <input class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none" 
                 type="date" name="new_end" />
        </div>

        <div class="form-group">
          <label class="form-label" style="display:block; margin-bottom:6px; font-weight:500; color:var(--slate-text)">Catatan / Alasan Pindah</label>
          <textarea class="search-wrap" style="width:100%; padding:8px 12px; border:1px solid var(--border-dim); border-radius:8px; background:var(--slate-very-faint); color:var(--slate-bright); outline:none; font-family:inherit" 
                    rows="3" name="note" placeholder="Ingin kamar yang lebih luas, dll..."></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:10px">
          <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi memindahkan penghuni?');">
            <i class="bi bi-arrow-left-right" style="font-size:13px"></i> Pindahkan Penghuni
          </button>
        </div>
      </form>
    </div>

    <!-- Info Column -->
    <div style="display:flex; flex-direction:column; gap:14px">
      <!-- Occupied List -->
      <div class="card" style="max-height: 250px; overflow-y: auto;">
        <div class="card-title" style="margin-bottom:14px; position: sticky; top: 0; background: var(--bg-card); padding-bottom: 6px; z-index: 10;">Kamar Terisi</div>
        <div style="display:flex; flex-direction:column; gap:8px">
          <?php if (empty($occupiedRooms)): ?>
            <div style="text-align:center; color:var(--slate-muted); padding:10px;">Semua kamar kosong</div>
          <?php else: ?>
            <?php foreach ($occupiedRooms as $r): ?>
              <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border:1px solid var(--border-soft); border-radius:8px">
                <div>
                  <div style="font-weight:600; color:var(--slate-bright)">Kamar <?= htmlspecialchars($r['id']) ?></div>
                  <div style="font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['tenant']) ?></div>
                </div>
                <span class="badge badge-green">Terisi</span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Empty List -->
      <div class="card" style="max-height: 250px; overflow-y: auto;">
        <div class="card-title" style="margin-bottom:14px; position: sticky; top: 0; background: var(--bg-card); padding-bottom: 6px; z-index: 10;">Kamar Kosong</div>
        <div style="display:flex; flex-direction:column; gap:8px">
          <?php if (empty($emptyRooms)): ?>
            <div style="text-align:center; color:var(--slate-muted); padding:10px;">Tidak ada kamar kosong</div>
          <?php else: ?>
            <?php foreach ($emptyRooms as $r): ?>
              <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; background:var(--green-faded); border:1px solid var(--green-soft); border-radius:8px">
                <div>
                  <div style="font-weight:600; color:var(--green-pale)">Kamar <?= htmlspecialchars($r['id']) ?></div>
                  <div style="font-size:12px; color:var(--green-vivid)"><?= htmlspecialchars($r['type']) ?> (<?= htmlspecialchars($r['status']) ?>)</div>
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
