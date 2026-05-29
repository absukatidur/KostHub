<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div style="max-width: 700px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Detail Customer</h2>
      <p>Data lengkap dan riwayat sewa penghuni</p>
    </div>
    <div style="display:flex; gap:8px">
      <a href="customers.php" class="btn btn-secondary" style="text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
      <a href="customers_form.php?id=<?= urlencode($customer['id']) ?>" class="btn btn-primary" style="text-decoration: none;">
        <i class="bi bi-pencil"></i> Edit Customer
      </a>
    </div>
  </div>

  <?php showFlash(); ?>

  <div class="two-col" style="margin-bottom: 20px;">
    <!-- Profile & Contact Card -->
    <div class="card" style="display:flex; flex-direction:column; gap:16px">
      <div style="display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--border-soft); padding-bottom:14px">
        <div class="avatar" style="width:48px; height:48px; font-size:18px; border-radius:50%"><?= strtoupper(substr($customer['name'], 0, 2)) ?></div>
        <div>
          <h3 style="margin:0; font-size:16px; color:var(--slate-white)"><?= htmlspecialchars($customer['name']) ?></h3>
          <span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($customer['id']) ?></span>
        </div>
      </div>
      <div class="detail-row" style="display:flex; justify-content:space-between;"><span style="color:var(--slate-muted)">Email</span><span style="color:var(--brand-accent)"><?= htmlspecialchars($customer['email']) ?></span></div>
      <div class="detail-row" style="display:flex; justify-content:space-between;"><span style="color:var(--slate-muted)">WhatsApp</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($customer['wa']) ?></span></div>
    </div>

    <!-- Room Status Card -->
    <div class="card" style="display:flex; flex-direction:column; gap:16px">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-soft); padding-bottom:14px">
        <h3 style="margin:0; font-size:16px; color:var(--slate-white)">Status Kamar</h3>
        <?php if ($room): ?>
          <?= statusBadge($room['status']) ?>
        <?php else: ?>
          <span class="badge badge-gray">Tidak Ada</span>
        <?php endif; ?>
      </div>
      <?php if ($room): ?>
        <div class="detail-row" style="display:flex; justify-content:space-between;"><span style="color:var(--slate-muted)">Kamar</span><a href="rooms_detail.php?id=<?= urlencode($room['id']) ?>" style="color:var(--brand-accent); font-weight:600; text-decoration:none"><?= htmlspecialchars($room['id']) ?></a></div>
        <div class="detail-row" style="display:flex; justify-content:space-between;"><span style="color:var(--slate-muted)">Tipe / Lantai</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($room['type']) ?> (Lantai <?= htmlspecialchars($room['floor']) ?>)</span></div>
        <div class="detail-row" style="display:flex; justify-content:space-between;"><span style="color:var(--slate-muted)">Sewa Hingga</span><span style="color:var(--slate-bright)"><?= htmlspecialchars($room['until']) ?></span></div>
      <?php else: ?>
        <div style="text-align:center; padding:15px; color:var(--slate-muted)">Customer belum menempati kamar manapun.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Rent/Orders History -->
  <div class="card">
    <div style="margin-bottom:16px"><h3 style="margin:0; font-size:16px; color:var(--slate-white)">Riwayat Transaksi / Order</h3></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Kamar</th>
            <th>Tipe Sewa</th>
            <th>Total Tagihan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="5" style="text-align:center; color:var(--slate-muted)">Belum ada riwayat transaksi</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><b style="font-family:'DM Mono',monospace; font-size:12px"><?= htmlspecialchars($o['id']) ?></b></td>
                <td><?= htmlspecialchars($o['room']) ?></td>
                <td><?= htmlspecialchars($o['type']) ?> (<?= htmlspecialchars($o['start']) ?> s/d <?= htmlspecialchars($o['end']) ?>)</td>
                <td><?= fmtRupiah($o['total']) ?></td>
                <td><?= statusBadge($o['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
