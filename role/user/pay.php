<?php
$basePath = '../';
require_once '../includes/db.php';
requireUser();

$id = $_GET['id'] ?? '';
if (!$id) {
    flashMsg("ID Order tidak valid.", 'error');
    header('Location: tagihan.php');
    exit;
}

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

// Verify order belongs to this customer
$stmt_ord = $db->prepare("SELECT * FROM orders WHERE id = ? AND customer = ?");
$stmt_ord->bind_param('ss', $id, $customer['name']);
$stmt_ord->execute();
$order = $stmt_ord->get_result()->fetch_assoc();

if (!$order) {
    flashMsg("Order tidak ditemukan.", 'error');
    header('Location: tagihan.php');
    exit;
}

if ($order['status'] === 'paid') {
    flashMsg("Tagihan ini sudah lunas.", 'success');
    header('Location: tagihan.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'] ?? '';

    if (!$method) {
        $error = 'Pilih metode pembayaran terlebih dahulu';
    } else {
        $db->begin_transaction();
        try {
            // 1. Mark order paid
            $stmtPay = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
            $stmtPay->bind_param('s', $id);
            $stmtPay->execute();

            // 2. Update room status/until date
            $stmtRoom = $db->prepare("UPDATE rooms SET status = 'occupied', tenant = ?, `until` = ? WHERE id = ?");
            $stmtRoom->bind_param('sss', $order['customer'], $order['end'], $order['room']);
            $stmtRoom->execute();

            // 3. Update customer room
            $stmtCust = $db->prepare("UPDATE customers SET room = ? WHERE name = ?");
            $stmtCust->bind_param('ss', $order['room'], $order['customer']);
            $stmtCust->execute();

            addLog($db, 'Pembayaran diterima', "$id via $method oleh {$customer['name']} (Masa sewa diperpanjang hingga {$order['end']})", 'order');
            
            $db->commit();
            
            flashMsg("Pembayaran tagihan $id via $method berhasil dilakukan!", 'success');
            header('Location: tagihan.php');
            exit;
        } catch (Exception $e) {
            $db->rollback();
            $error = 'Gagal memproses pembayaran: ' . $e->getMessage();
        }
    }
}

// Payment Methods list
$payMethods = [
    'Virtual Account (VA)' => [
        ['id' => 'BCA', 'name' => 'BCA Virtual Account', 'color' => '#003B7B', 'logo' => 'BCA'],
        ['id' => 'Mandiri', 'name' => 'Mandiri Virtual Account', 'color' => '#003882', 'logo' => 'MAN'],
        ['id' => 'BNI', 'name' => 'BNI Virtual Account', 'color' => '#E35A14', 'logo' => 'BNI'],
        ['id' => 'BRI', 'name' => 'BRI Virtual Account', 'color' => '#00529C', 'logo' => 'BRI'],
    ],
    'e-Wallet' => [
        ['id' => 'GoPay', 'name' => 'GoPay', 'color' => '#00AED6', 'logo' => 'GOP'],
        ['id' => 'OVO', 'name' => 'OVO', 'color' => '#4C3494', 'logo' => 'OVO'],
        ['id' => 'ShopeePay', 'name' => 'ShopeePay', 'color' => '#EE4D2D', 'logo' => 'SPP'],
        ['id' => 'DANA', 'name' => 'DANA', 'color' => '#108EE9', 'logo' => 'DAN'],
    ]
];

$pageTitle = 'Pembayaran Tagihan — KostHub';
$pageTitleShort = 'Tagihan';

require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>
<div style="max-width: 650px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Pembayaran</h2>
      <p>Pilih metode pembayaran untuk menyelesaikan tagihan</p>
    </div>
    <a href="tagihan.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card" style="margin-bottom: 20px; background: var(--bg-card); border: 1px solid var(--border-dim); border-radius: 12px; padding: 20px; text-align: center;">
    <div style="font-size: 11px; font-weight: 600; color: var(--slate-muted); letter-spacing: 0.05em; text-transform: uppercase;">TOTAL PEMBAYARAN</div>
    <div style="font-size: 28px; font-weight: 700; color: var(--brand-accent); margin: 8px 0;"><?= fmtRupiah($order['total']) ?></div>
    <div style="font-size: 12.5px; color: var(--slate-mid);">Order: <?= htmlspecialchars($order['id']) ?> · Kamar <?= htmlspecialchars($order['room']) ?> · <?= htmlspecialchars($order['type']) ?></div>
  </div>

  <form method="POST" autocomplete="off">
    <input type="hidden" id="selected-method" name="method" value="" required />
    
    <div class="card" style="margin-bottom: 20px;">
      <div style="margin-bottom: 16px;"><h3 style="margin:0; font-size:15px; color:var(--slate-white)">Pilih Metode Pembayaran</h3></div>
      
      <?php foreach ($payMethods as $group => $items): ?>
        <div style="margin-bottom: 20px;">
          <div style="font-size: 11px; font-weight: 600; color: var(--slate-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;"><?= $group ?></div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <?php foreach ($items as $m): ?>
              <button type="button" class="pay-method-btn" onclick="selectPaymentMethod(this, '<?= $m['id'] ?>', '<?= htmlspecialchars($m['logo']) ?>')" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-dim); border-radius: 8px; background: var(--bg-card); cursor: pointer; text-align: left; width: 100%; transition: all 0.15s; outline: none;">
                <div style="width: 40px; height: 40px; border-radius: 6px; background: <?= $m['color'] ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13.5px; flex-shrink: 0;">
                  <?= $m['logo'] ?>
                </div>
                <div style="flex: 1">
                  <div style="font-size: 13.5px; font-weight: 600; color: var(--slate-bright);"><?= htmlspecialchars($m['id']) ?></div>
                  <div style="font-size: 11px; color: var(--slate-muted);"><?= $group === 'e-Wallet' ? 'Saldo e-Wallet' : 'Virtual Account' ?></div>
                </div>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Instruction / Details Card -->
    <div id="payment-details-card" class="card" style="display: none; margin-bottom: 20px; border: 1px solid var(--border-dim);">
      <div style="margin-bottom:12px"><h3 id="details-title" style="margin:0; font-size:15px; color:var(--slate-white)">Detail Pembayaran</h3></div>
      <div id="details-body" style="padding: 12px; background: var(--slate-faint); border-radius: 8px; text-align: center;">
        <!-- Filled dynamically by JS -->
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px;">
      <a href="tagihan.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
      <button type="submit" id="submit-pay-btn" class="btn btn-primary" style="display: none;">
        <i class="bi bi-check-lg" style="font-size: 13px;"></i> Konfirmasi Pembayaran
      </button>
    </div>
  </form>
</div>

<style>
.pay-method-btn.selected {
  border-color: var(--brand-accent) !important;
  background: var(--slate-thin) !important;
}
</style>

<script>
<script src="<?= $basePath ?? '' ?>assets/js/user-pay.js?v=<?= time() ?>"></script>
</script>

<?php require_once '../components/user_footer_scripts.php'; ?>
