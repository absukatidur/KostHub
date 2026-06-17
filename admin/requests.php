<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Permintaan User — KostHub';
$pageTitleShort = 'Permintaan User';

// Fetch all requests
$requests = $db->query("
    SELECT r.*, c.name as customer_name 
    FROM requests r 
    LEFT JOIN customers c ON r.customer_id = c.id 
    ORDER BY r.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Permintaan User</h2>
      <p>Kelola pengajuan pindah kamar &amp; checkout</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Penghuni</th>
            <th>Tipe</th>
            <th>Kamar Asal</th>
            <th>Detail</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Deskripsi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests)): ?>
            <tr><td colspan="8" class="td-empty" class="p-40">Tidak ada permintaan</td></tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><span class="td-mono"><?= htmlspecialchars($r['id']) ?></span></td>
                <td class="td-bold"><?= htmlspecialchars($r['customer_name'] ?: $r['customer_id']) ?></td>
                <td>
                  <?php if ($r['type'] === 'pindah'): ?>
                    <span class="badge badge-blue">Pindah</span>
                  <?php else: ?>
                    <span class="badge badge-amber">Checkout</span>
                  <?php endif; ?>
                </td>
                <td><span class="td-mono"><?= htmlspecialchars($r['from_room'] ?: '-') ?></span></td>
                <td class="text-sm" class="mw-220">
                  <?php 
                  $detail = json_decode($r['detail'] ?: '{}', true);
                  if ($r['type'] === 'pindah') {
                      echo 'Ke kamar ' . htmlspecialchars($detail['toRoom'] ?? '?') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
                  } else {
                      echo 'Tgl: ' . htmlspecialchars($detail['date'] ?? '-') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
                  }
                  ?>
                </td>
                <td class="text-sm text-muted"><?= htmlspecialchars(substr($r['created_at'], 0, 10)) ?></td>
                <td>
                  <?php
                  $statusMap = ['pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-red'];
                  $statusText = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                  $badgeCls = $statusMap[$r['status']] ?? 'badge-gray';
                  $text = $statusText[$r['status']] ?? $r['status'];
                  ?>
                  <span class="badge <?= $badgeCls ?>"><?= $text ?></span>
                </td>
                <td>
                  <div class="action-group">
                    <?php if ($r['status'] === 'pending'): ?>
                      <a href="requests_resolve.php?id=<?= urlencode($r['id']) ?>&status=approved" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg" class="text-sm"></i> Setujui
                      </a>
                      <a href="requests_resolve.php?id=<?= urlencode($r['id']) ?>&status=rejected" class="btn btn-danger btn-sm">
                        <i class="bi bi-x-lg" class="text-sm"></i> Tolak
                      </a>
                    <?php else: ?>
                      <span class="text-sm text-muted"><?= htmlspecialchars($r['admin_note'] ?: '-') ?></span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
