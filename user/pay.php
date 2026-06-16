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

<div class="form-container-md">
  <div class="section-header">
    <div>
      <h2>Pembayaran</h2>
      <p>Pilih metode pembayaran untuk menyelesaikan tagihan</p>
    </div>
    <a href="tagihan.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card pay-hero-card">
    <div class="pay-hero-label">TOTAL PEMBAYARAN</div>
    <div class="pay-hero-total"><?= fmtRupiah($order['total']) ?></div>
    <div class="pay-hero-meta">Order: <?= htmlspecialchars($order['id']) ?> · Kamar <?= htmlspecialchars($order['room']) ?> · <?= htmlspecialchars($order['type']) ?></div>
  </div>

  <div id="pay-page-data" data-total-formatted="<?= fmtRupiah($order['total']) ?>"></div>
  <form method="POST" autocomplete="off">
    <input type="hidden" id="selected-method" name="method" value="" required />
    
    <div class="card card-mb">
      <h3 class="booking-section-title">Pilih Metode Pembayaran</h3>
      
      <?php foreach ($payMethods as $group => $items): ?>
        <div class="pay-method-group">
          <div class="pay-group-title"><?= $group ?></div>
          <div class="pay-method-grid">
            <?php foreach ($items as $m): ?>
              <button type="button" class="pay-method-btn" onclick="selectPaymentMethod(this, '<?= $m['id'] ?>', '<?= htmlspecialchars($m['logo']) ?>')">
                <div class="pm-icon" style="background: <?= $m['color'] ?>">
                  <?= $m['logo'] ?>
                </div>
                <div>
                  <div class="pm-name"><?= htmlspecialchars($m['id']) ?></div>
                  <div class="pm-desc"><?= $group === 'e-Wallet' ? 'Saldo e-Wallet' : 'Virtual Account' ?></div>
                </div>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Instruction / Details Card -->
    <div id="payment-details-card" class="card card-mb" style="display: none;">
      <h3 id="details-title" class="booking-section-title">Detail Pembayaran</h3>
      <div id="details-body" class="pay-detail">
        <!-- Filled dynamically by JS -->
      </div>
    </div>

    <div class="form-actions">
      <a href="tagihan.php" class="btn btn-secondary btn-link">Batal</a>
      <button type="submit" id="submit-pay-btn" class="btn btn-primary" style="display: none;">
        <i class="bi bi-check-lg"></i> Konfirmasi Pembayaran
      </button>
    </div>
  </form>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/user-pay.js?v=<?= time() ?>"></script>

<?php require_once '../components/user_footer_scripts.php'; ?>
