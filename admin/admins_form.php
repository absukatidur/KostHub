<?php
$basePath = '../';
require_once '../includes/db.php';
requireOwner();

$id = intval($_GET['id'] ?? 0);
$isEdit = !empty($id);
$adminUser = null;

if ($isEdit) {
    // Only allow editing admin accounts (cannot edit owner accounts here for safety)
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $adminUser = $stmt->get_result()->fetch_assoc();
    if (!$adminUser) {
        flashMsg("Akun admin tidak ditemukan atau tidak dapat diedit.", 'error');
        header('Location: admins.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$username) {
        $error = 'Username wajib diisi';
    } elseif (!$isEdit && !$password) {
        $error = 'Password wajib diisi untuk akun baru';
    } elseif ($password && strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password && $password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        // Check username unique
        if ($isEdit) {
            $chk = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $chk->bind_param('si', $username, $id);
        } else {
            $chk = $db->prepare("SELECT id FROM users WHERE username = ?");
            $chk->bind_param('s', $username);
        }
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan oleh akun lain';
        } else {
            if ($isEdit) {
                if ($password) {
                    // Update username and password
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET username = ?, password = ? WHERE id = ? AND role = 'admin'");
                    $stmt->bind_param('ssi', $username, $hash, $id);
                } else {
                    // Update username only
                    $stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ? AND role = 'admin'");
                    $stmt->bind_param('si', $username, $id);
                }
                
                if ($stmt->execute()) {
                    addLog($db, 'Admin diperbarui', "Akun admin $username (ID: $id) diperbarui", 'customer');
                    flashMsg("Akun admin $username berhasil diperbarui.", 'success');
                    header('Location: admins.php');
                    exit;
                } else {
                    $error = 'Gagal memperbarui data: ' . $db->error;
                }
            } else {
                // Insert new admin
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 'admin';
                $stmt = $db->prepare("INSERT INTO users (username, password, role, customer_id) VALUES (?, ?, ?, NULL)");
                $stmt->bind_param('sss', $username, $hash, $role);
                
                if ($stmt->execute()) {
                    $nid = $db->insert_id;
                    addLog($db, 'Admin ditambah', "Akun admin baru $username (ID: $nid) ditambahkan", 'customer');
                    flashMsg("Akun admin $username berhasil ditambahkan.", 'success');
                    header('Location: admins.php');
                    exit;
                } else {
                    $error = 'Gagal menambahkan admin: ' . $db->error;
                }
            }
        }
    }
}

$pageTitle = ($isEdit ? 'Edit Admin' : 'Tambah Admin') . ' — KostHub';
$pageTitleShort = 'Kelola Admin';

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div class="form-container">
  <div class="section-header">
    <div>
      <h2><?= $isEdit ? 'Edit Admin' : 'Tambah Admin' ?></h2>
      <p><?= $isEdit ? 'Perbarui username atau password untuk staff ' . htmlspecialchars($adminUser['username']) : 'Tambahkan akun staff operator baru' ?></p>
    </div>
    <a href="admins.php" class="btn btn-secondary btn-link">
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
        <label class="form-label" for="username">Username</label>
        <input class="form-input" 
               id="username" name="username" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? ($adminUser['username'] ?? '')) ?>" required autofocus />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input class="form-input" 
                 type="password" id="password" name="password" placeholder="<?= $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' ?>" <?= $isEdit ? '' : 'required' ?> />
        </div>
        <div class="form-group">
          <label class="form-label" for="password_confirm">Konfirmasi Password</label>
          <input class="form-input" 
                 type="password" id="password_confirm" name="password_confirm" placeholder="<?= $isEdit ? 'Kosongkan jika tidak diubah' : 'Ketik ulang password' ?>" <?= $isEdit ? '' : 'required' ?> />
        </div>
      </div>

      <div class="form-actions">
        <a href="admins.php" class="btn btn-secondary btn-link">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Akun</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../components/footer_scripts.php'; ?>
