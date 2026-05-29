<?php
$basePath = '../';
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
            <th>Customer</th>
            <th>Tipe</th>
            <th>Detail</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests)): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--slate-muted); padding:40px">Tidak ada permintaan</td></tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['id']) ?></span></td>
                <td style="font-weight:600"><?= htmlspecialchars($r['customer_name'] ?: $r['customer_id']) ?></td>
                <td>
                  <?php if ($r['type'] === 'pindah'): ?>
                    <span class="badge badge-blue">Pindah</span>
                  <?php else: ?>
                    <span class="badge badge-amber">Checkout</span>
                  <?php endif; ?>
                </td>
                <td style="font-size:12px; max-width:220px">
                  <?php 
                  $detail = json_decode($r['detail'] ?: '{}', true);
                  if ($r['type'] === 'pindah') {
                      echo 'Ke kamar ' . htmlspecialchars($detail['toRoom'] ?? '?') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
                  } else {
                      echo 'Tgl: ' . htmlspecialchars($detail['date'] ?? '-') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
                  }
                  ?>
                </td>
                <td style="font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars(substr($r['created_at'], 0, 10)) ?></td>
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
                  <div style="display:flex; gap:6px">
                    <?php if ($r['status'] === 'pending'): ?>
                      <a href="requests_resolve.php?id=<?= urlencode($r['id']) ?>&status=approved" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg" style="font-size:12px"></i> Setujui
                      </a>
                      <a href="requests_resolve.php?id=<?= urlencode($r['id']) ?>&status=rejected" class="btn btn-danger btn-sm">
                        <i class="bi bi-x-lg" style="font-size:12px"></i> Tolak
                      </a>
                    <?php else: ?>
                      <span style="font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['admin_note'] ?: '-') ?></span>
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
