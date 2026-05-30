<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Manajemen Kamar — KostHub';
$pageTitleShort = 'Manajemen Kamar';

$rooms = $db->query("SELECT * FROM rooms ORDER BY id")->fetch_all(MYSQLI_ASSOC);

$occupied = count(array_filter($rooms, fn($r) => $r['status'] === 'occupied'));
$empty = count(array_filter($rooms, fn($r) => $r['status'] === 'empty'));
$cleaning = count(array_filter($rooms, fn($r) => $r['status'] === 'cleaning'));
$maint = count(array_filter($rooms, fn($r) => $r['status'] === 'maintenance'));

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Manajemen Kamar</h2>
      <p>Pantau &amp; kelola semua unit kamar</p>
    </div>
  </div>

  <div class="page-tabs">
    <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
    <button class="tab-btn" onclick="switchTab('list')">List Kamar</button>
  </div>

  <?php showFlash(); ?>

  <!-- Tab Overview Content -->
  <div id="tab-overview">
    <!-- Legend -->
    <div class="legend" style="margin-bottom: 20px;">
      <div class="legend-item">
        <div class="legend-dot" style="background: var(--green-mid);"></div>
        <span class="legend-text">Terisi (<?= $occupied ?>)</span>
      </div>
      <div class="legend-item">
        <div class="legend-dot" style="background: var(--blue-muted);"></div>
        <span class="legend-text">Kosong (<?= $empty ?>)</span>
      </div>
      <div class="legend-item">
        <div class="legend-dot" style="background: var(--amber-mid);"></div>
        <span class="legend-text">Cleaning (<?= $cleaning ?>)</span>
      </div>
      <div class="legend-item">
        <div class="legend-dot" style="background: var(--red-mid);"></div>
        <span class="legend-text">Maintenance (<?= $maint ?>)</span>
      </div>
    </div>
    
    <!-- Room Grid -->
    <div class="room-grid">
      <?php foreach ($rooms as $r): ?>
        <div class="room-cell <?= htmlspecialchars($r['status']) ?>" onclick="location.href='rooms_detail.php?id=<?= urlencode($r['id']) ?>'">
          <div class="room-num"><?= htmlspecialchars($r['id']) ?></div>
          <div class="room-type"><?= htmlspecialchars($r['type']) ?></div>
          <div class="room-status-text" style="font-size:10px; margin-top:2px; opacity:.7">
            <?php
            if ($r['status'] === 'occupied') {
                $tenantParts = explode(' ', $r['tenant']);
                echo htmlspecialchars($tenantParts[0]);
            } elseif ($r['status'] === 'empty') {
                echo 'Kosong';
            } elseif ($r['status'] === 'cleaning') {
                echo 'Cleaning';
            } else {
                echo 'Perbaikan';
            }
            ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Tab List Content -->
  <div id="tab-list" style="display: none;">
    <div class="card" style="padding: 0;">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Kamar</th>
              <th>Tipe</th>
              <th>Penghuni</th>
              <th>Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($rooms)): ?>
              <tr><td colspan="6" style="text-align:center; color:var(--slate-muted)">Tidak ada data kamar</td></tr>
            <?php else: ?>
              <?php foreach ($rooms as $r): ?>
                <tr>
                  <td>
                    <b class="kl-id"><?= htmlspecialchars($r['id']) ?></b>
                    <div style="font-size:11px;color:var(--slate-muted)">Lantai <?= htmlspecialchars($r['floor']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($r['type']) ?></td>
                  <td>
                    <?php if ($r['tenant'] !== '-'): ?>
                      <span style="font-weight:500"><?= htmlspecialchars($r['tenant']) ?></span>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td><?= fmtRupiah($r['price']) ?></td>
                  <td><?= statusBadge($r['status']) ?></td>
                  <td>
                    <a href="rooms_detail.php?id=<?= urlencode($r['id']) ?>" class="btn btn-secondary btn-sm">Edit Status</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function switchTab(tabName) {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.remove('active');
    if (btn.getAttribute('onclick').includes("'" + tabName + "'")) {
      btn.classList.add('active');
    }
  });

  if (tabName === 'overview') {
    document.getElementById('tab-overview').style.display = 'block';
    document.getElementById('tab-list').style.display = 'none';
  } else {
    document.getElementById('tab-overview').style.display = 'none';
    document.getElementById('tab-list').style.display = 'block';
  }
}
</script>

<?php require_once '../components/footer_scripts.php'; ?>
