<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$pageTitle = 'Layanan Pengajuan — KostHub';
$pageTitleShort = 'Layanan';

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

// Fetch requests history
$stmt_req = $db->prepare("SELECT * FROM requests WHERE customer_id = ? ORDER BY created_at DESC");
$stmt_req->bind_param('s', $cid);
$stmt_req->execute();
$requests = $stmt_req->get_result()->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Layanan Pengajuan</h2>
      <p>Ajukan pindah kamar atau checkout sewa</p>
    </div>
  </div>

  <?php showFlash(); ?>

  <!-- Actions Row -->
  <div class="two-col mb-16" style="margin-bottom: 20px;">
    <!-- Card Pindah Kamar -->
    <div class="card" style="display:flex; flex-direction:column; gap:12px; align-items:center; text-align:center;">
      <div class="icon-wrap ic-blue" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
        <i class="bi bi-arrow-left-right"></i>
      </div>
      <div style="font-weight: 700; font-size: 15px; color: var(--slate-white)">Pindah Kamar</div>
      <div style="font-size: 12.5px; color: var(--slate-muted); min-height: 38px;">Ajukan pindah ke unit kamar lain yang tersedia</div>
      
      <?php if (!empty($customer['room'])): ?>
        <a href="requests_form.php?type=pindah" class="btn btn-primary btn-sm" style="text-decoration: none;">Ajukan Pindah</a>
      <?php else: ?>
        <button class="btn btn-secondary btn-sm" disabled title="Anda harus menyewa kamar terlebih dahulu">Ajukan Pindah</button>
      <?php endif; ?>
    </div>

    <!-- Card Checkout -->
    <div class="card" style="display:flex; flex-direction:column; gap:12px; align-items:center; text-align:center;">
      <div class="icon-wrap ic-amber" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
        <i class="bi bi-box-arrow-right"></i>
      </div>
      <div style="font-weight: 700; font-size: 15px; color: var(--slate-white)">Pengajuan Checkout</div>
      <div style="font-size: 12.5px; color: var(--slate-muted); min-height: 38px;">Informasikan jika Anda tidak ingin memperpanjang sewa kamar</div>
      
      <?php if (!empty($customer['room'])): ?>
        <a href="requests_form.php?type=checkout" class="btn btn-primary btn-sm" style="text-decoration: none;">Ajukan Checkout</a>
      <?php else: ?>
        <button class="btn btn-secondary btn-sm" disabled title="Anda harus menyewa kamar terlebih dahulu">Ajukan Checkout</button>
      <?php endif; ?>
    </div>
  </div>

  <!-- History Card -->
  <div class="card">
    <div class="card-header"><span class="card-title">Riwayat Pengajuan</span></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipe</th>
            <th>Detail</th>
            <th>Tanggal Pengajuan</th>
            <th>Status</th>
            <th>Catatan Admin</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests)): ?>
            <tr><td colspan="6" style="text-align:center; color:var(--slate-muted); padding:40px">Belum ada riwayat pengajuan</td></tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($r['id']) ?></span></td>
                <td>
                  <?php if ($r['type'] === 'pindah'): ?>
                    <span class="badge badge-blue">Pindah</span>
                  <?php else: ?>
                    <span class="badge badge-amber">Checkout</span>
                  <?php endif; ?>
                </td>
                <td style="font-size: 12.5px; color: var(--slate-bright)">
                  <?php
                  $detail = json_decode($r['detail'] ?: '{}', true);
                  if ($r['type'] === 'pindah') {
                      echo 'Ke kamar ' . htmlspecialchars($detail['toRoom'] ?? '?') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
                  } else {
                      echo 'Checkout tgl: ' . htmlspecialchars($detail['date'] ?? '-') . ' — ' . htmlspecialchars($detail['reason'] ?? '-');
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
                <td style="font-size:12.5px; color:var(--slate-muted)">
                  <?= htmlspecialchars($r['admin_note'] ?: '-') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../components/user_footer_scripts.php'; ?>
