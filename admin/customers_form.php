<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$isEdit = !empty($id);
$customer = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();
    if (!$customer) {
        flashMsg("Penghuni tidak ditemukan.", 'error');
        header('Location: customers.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $wa = trim($_POST['wa'] ?? '');
    $roomField = $_POST['room'] ?? '';

    if (!$name || !$email || !$wa) {
        $error = 'Semua field wajib diisi';
    } else {
        // Check email unique (except itself if editing)
        if ($isEdit) {
            $chk = $db->prepare("SELECT id FROM customers WHERE email = ? AND id != ?");
            $chk->bind_param('ss', $email, $id);
        } else {
            $chk = $db->prepare("SELECT id FROM customers WHERE email = ?");
            $chk->bind_param('s', $email);
        }
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Email sudah digunakan oleh customer lain';
        } else {
            if ($isEdit) {
                // Handle room changes
                if ($customer['room'] !== $roomField) {
                    if (!empty($customer['room'])) {
                        // Clear old room
                        $db->query("UPDATE rooms SET status='empty', tenant='-', `until`='-' WHERE id='" . $db->real_escape_string($customer['room']) . "'");
                    }
                    if (!empty($roomField)) {
                        // Set new room occupied by this tenant
                        $db->query("UPDATE rooms SET status='occupied', tenant='" . $db->real_escape_string($name) . "' WHERE id='" . $db->real_escape_string($roomField) . "'");
                    }
                } elseif (!empty($roomField) && $customer['name'] !== $name) {
                    // If name changed, update the tenant name in the room too
                    $db->query("UPDATE rooms SET tenant='" . $db->real_escape_string($name) . "' WHERE id='" . $db->real_escape_string($roomField) . "'");
                }

                $stmt = $db->prepare("UPDATE customers SET name = ?, email = ?, wa = ?, room = ? WHERE id = ?");
                $stmt->bind_param('sssss', $name, $email, $wa, $roomField, $id);
                if ($stmt->execute()) {
                    addLog($db, 'Penghuni diperbarui', "$name ($id) diperbarui", 'customer');
                    flashMsg("Penghuni $name berhasil diperbarui.", 'success');
                    header('Location: customers.php');
                    exit;
                } else {
                    $error = 'Gagal memperbarui data: ' . $db->error;
                }
            } else {
                $nid = nextId($db, 'customers', 'C');
                
                if (!empty($roomField)) {
                    // Set room occupied by new tenant
                    $db->query("UPDATE rooms SET status='occupied', tenant='" . $db->real_escape_string($name) . "' WHERE id='" . $db->real_escape_string($roomField) . "'");
                }

                $stmt = $db->prepare("INSERT INTO customers (id, name, email, wa, room) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $nid, $name, $email, $wa, $roomField);
                if ($stmt->execute()) {
                    addLog($db, 'Penghuni ditambah', "$name terdaftar ($nid)", 'customer');
                    flashMsg("Penghuni $name berhasil ditambahkan.", 'success');
                    header('Location: customers.php');
                    exit;
                } else {
                    $error = 'Gagal menambahkan customer: ' . $db->error;
                }
            }
        }
    }
}

// Fetch rooms that are empty or cleaning, plus the customer's current room if editing
$roomOptionsQuery = "SELECT id, status, type FROM rooms WHERE status IN ('empty', 'cleaning')";
if ($isEdit && !empty($customer['room'])) {
    $roomOptionsQuery .= " OR id = '" . $db->real_escape_string($customer['room']) . "'";
}
$roomOptionsQuery .= " ORDER BY id";
$rooms = $db->query($roomOptionsQuery)->fetch_all(MYSQLI_ASSOC);

$pageTitle = ($isEdit ? 'Edit Penghuni' : 'Tambah Penghuni') . ' — KostHub';
$pageTitleShort = 'Penghuni';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Penghuni' : 'Tambah Penghuni' ?></h2>
      <p><?= $isEdit ? 'Perbarui data diri penghuni ' . htmlspecialchars($id) : 'Tambahkan data diri penghuni kos baru' ?></p>
    </div>
    <a href="customers.php" class="btn btn-secondary btn-link">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <form method="POST" autocomplete="off" class="form-stack">
      <div class="form-group">
        <label class="form-label" for="ac-name">Nama Lengkap</label>
        <input class="form-input" 
               id="ac-name" name="name" placeholder="Nama penghuni" value="<?= htmlspecialchars($_POST['name'] ?? ($customer['name'] ?? '')) ?>" required autofocus />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="ac-email">Email</label>
          <input class="form-input" 
                 type="email" id="ac-email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($_POST['email'] ?? ($customer['email'] ?? '')) ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="ac-wa">WhatsApp</label>
          <input class="form-input" 
                 id="ac-wa" name="wa" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['wa'] ?? ($customer['wa'] ?? '')) ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="ac-room">Kamar yang Ditempati</label>
        <select class="filter-select-full" id="ac-room" name="room">
          <option value="" <?= (($_POST['room'] ?? ($customer['room'] ?? '')) === '') ? 'selected' : '' ?>>Belum Menyewa Kamar (Kosong)</option>
          <?php foreach ($rooms as $r): ?>
            <?php 
            $isSelected = ($_POST['room'] ?? ($customer['room'] ?? '')) === $r['id'];
            $roomLabel = $r['id'] . ' - ' . $r['type'] . ' (' . ($r['status'] === 'empty' ? 'Kosong' : 'Cleaning') . ')';
            if ($isEdit && $customer['room'] === $r['id']) {
                $roomLabel = $r['id'] . ' - ' . $r['type'] . ' (Kamar Sekarang)';
            }
            ?>
            <option value="<?= htmlspecialchars($r['id']) ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars($roomLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-actions">
        <a href="customers.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Penghuni</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
