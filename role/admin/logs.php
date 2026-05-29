<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Log Aktivitas</h2>
      <p>Riwayat semua aksi admin</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
        <input id="log-search" placeholder="Cari aktivitas..." />
      </div>
    </div>
    
    <div class="activity-list" id="log-list">
      <?php if (empty($logs)): ?>
        <div style="text-align:center; padding:40px; color:var(--slate-muted)">Tidak ada riwayat aktivitas</div>
      <?php else: ?>
        <?php foreach ($logs as $l): ?>
          <?php 
          $icon = $logIcons[$l['type']] ?? 'circle';
          $color = $logColors[$l['type']] ?? 'ic-gray';
          ?>
          <div class="activity-item">
            <div class="act-dot <?= $color ?>"><i class="bi bi-<?= $icon ?>" style="font-size:14px"></i></div>
            <div class="act-content" style="flex:1">
              <div class="act-title" style="color:var(--slate-bright); font-weight:600"><?= htmlspecialchars($l['action']) ?></div>
              <div class="act-detail act-meta" style="color:var(--slate-muted); font-size:12px; margin-top:2px"><?= htmlspecialchars($l['detail']) ?></div>
            </div>
            <div class="log-time" style="font-size:11px; color:var(--slate-muted); white-space:nowrap; padding-left:12px">
              <?= htmlspecialchars($l['time']) ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <div class="pagination" style="margin-top:12px">
      <span class="info" id="log-count">Menampilkan <?= count($logs) ?> aktivitas</span>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('log-search');
  if (searchInput) {
    searchInput.oninput = function() {
      const q = this.value.toLowerCase();
      let visibleCount = 0;
      const items = document.querySelectorAll('#log-list .activity-item');
      items.forEach(el => {
        const text = el.textContent.toLowerCase();
        if (text.includes(q)) {
          el.style.display = 'flex';
          visibleCount++;
        } else {
          el.style.display = 'none';
        }
      });
      document.getElementById('log-count').textContent = `Menampilkan ${visibleCount} aktivitas`;
    };
  }
});
</script>

<?php require_once '../components/footer_scripts.php'; ?>
