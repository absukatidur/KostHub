<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Perbaikan — KostHub';
$pageTitleShort = 'Perbaikan';

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

$roomTarget = 'Kamar ' . ($customer['room'] ?? '');

// Handle vote upvote POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'vote') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM repairs WHERE id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $repair = $stmt->get_result()->fetch_assoc();

        if ($repair) {
            $voters = json_decode($repair['voted_by'] ?? '[]', true);
            if (!is_array($voters)) $voters = [];

            if (!in_array($cid, $voters)) {
                $voters[] = $cid;
                $votes = count($voters);
                $voters_json = json_encode($voters);

                $stmt_upd = $db->prepare("UPDATE repairs SET votes = ?, voted_by = ? WHERE id = ?");
                $stmt_upd->bind_param('iss', $votes, $voters_json, $id);
                if ($stmt_upd->execute()) {
                    addLog($db, 'Dukungan laporan perbaikan', "Tenant $cid mendukung perbaikan $id", 'repair');
                    flashMsg("Dukungan Anda berhasil ditambahkan!", 'success');
                } else {
                    flashMsg("Gagal memproses dukungan.", 'error');
                }
            } else {
                flashMsg("Anda sudah mendukung laporan perbaikan ini.", 'warning');
            }
        }
    }
    header('Location: perbaikan.php');
    exit;
}

// Fetch general facility repairs (type = fasum, status !== done)
$publicRepairs = $db->query("SELECT * FROM repairs WHERE type = 'fasum' AND status != 'done' ORDER BY reported DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch all repairs to filter my reports
$allRepairs = $db->query("SELECT * FROM repairs ORDER BY reported DESC")->fetch_all(MYSQLI_ASSOC);
$myRepairs = array_filter($allRepairs, function($r) use ($roomTarget, $cid) {
    $voters = json_decode($r['voted_by'] ?? '[]', true);
    if (!is_array($voters)) $voters = [];
    return $r['target'] === $roomTarget || in_array($cid, $voters);
});

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Perbaikan</h2>
      <p>Laporkan &amp; lacak perbaikan fasilitas</p>
    </div>
    <a href="repairs_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-plus-lg"></i> Lapor Kerusakan Baru
    </a>
  </div>

  <?php showFlash(); ?>

  <!-- PUBLIC REPAIRS CARD -->
  <div class="card mb-16" style="margin-bottom: 20px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:16px; color:var(--slate-bright); display:flex; align-items:center; gap:8px">
      <i class="bi bi-info-circle-fill" style="color:var(--brand-accent)"></i> Laporan Fasilitas Umum Terbuka
    </h3>
    
    <div style="display:flex; flex-direction:column; gap:12px">
      <?php if (empty($publicRepairs)): ?>
        <div style="text-align:center; color:var(--slate-muted); padding:20px 0;">Tidak ada laporan fasilitas umum yang terbuka</div>
      <?php else: ?>
        <?php foreach ($publicRepairs as $pr): ?>
          <?php 
          $voters = json_decode($pr['voted_by'] ?? '[]', true);
          if (!is_array($voters)) $voters = [];
          $hasVoted = in_array($cid, $voters);
          $votesCount = count($voters) ?: $pr['votes'] ?: 1;
          ?>
          <div style="display:flex; align-items:center; justify-content:space-between; padding:12px; border:1px solid var(--border-soft); border-radius:8px; background:var(--slate-faint)">
            <div>
              <div style="font-weight:700; color:var(--slate-bright)"><?= htmlspecialchars($pr['target']) ?></div>
              <div style="font-size:13.5px; color:var(--slate-mid); margin:4px 0"><?= htmlspecialchars($pr['issue']) ?></div>
              <div style="font-size:12px; color:var(--slate-muted)">
                <i class="bi bi-people-fill" style="margin-right:4px"></i> <span><?= $votesCount ?></span> orang telah melaporkan
              </div>
            </div>
            
            <?php if ($hasVoted): ?>
              <button class="btn btn-secondary btn-sm" disabled style="background:var(--slate-thin); color:var(--green-vivid)">
                <i class="bi bi-check-lg"></i> Sudah Didukung
              </button>
            <?php else: ?>
              <form method="POST" action="perbaikan.php" style="display:inline;">
                <input type="hidden" name="action" value="vote">
                <input type="hidden" name="id" value="<?= htmlspecialchars($pr['id']) ?>">
                <button type="submit" class="btn btn-secondary btn-sm">
                  <i class="bi bi-hand-thumbs-up"></i> Saya juga mengalami ini
                </button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- MY REPORTS CARD -->
  <div class="card">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:16px; color:var(--slate-bright); display:flex; align-items:center; gap:8px">
      <i class="bi bi-person-fill" style="color:var(--brand-accent)"></i> Riwayat Laporan Saya
    </h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Target</th>
            <th>Masalah</th>
            <th>Status</th>
            <th>Teknisi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($myRepairs)): ?>
            <tr><td colspan="5" style="text-align:center; color:var(--slate-muted); padding:40px">Tidak ada laporan</td></tr>
          <?php else: ?>
            <?php foreach ($myRepairs as $mr): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($mr['id']) ?></span></td>
                <td><b><?= htmlspecialchars($mr['target']) ?></b></td>
                <td><?= htmlspecialchars($mr['issue']) ?></td>
                <td><?= repairStatusBadge($mr['status']) ?></td>
                <td><?= htmlspecialchars($mr['tech']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
