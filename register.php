<?php
require_once 'includes/db.php';

// Redirect if already logged in
if (!empty($_SESSION['role'])) {
    if (in_array($_SESSION['role'], ['admin', 'owner'])) { header('Location: admin/dashboard.php'); exit; }
    if ($_SESSION['role'] === 'user')  { header('Location: user/dashboard.php');  exit; }
}

$error = '';
$username = '';
$name = '';
$email = '';
$wa = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $wa       = trim($_POST['wa'] ?? '');

    if (!$username || !$password || !$password_confirm || !$name || !$email || !$wa) {
        $error = 'Semua field wajib diisi';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        // Check username unique
        $chk = $db->prepare("SELECT id FROM users WHERE username = ?");
        $chk->bind_param('s', $username);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan';
        } else {
            // Check email unique in customers
            $chkEmail = $db->prepare("SELECT id FROM customers WHERE email = ?");
            $chkEmail->bind_param('s', $email);
            $chkEmail->execute();
            if ($chkEmail->get_result()->num_rows > 0) {
                $error = 'Email sudah terdaftar';
            } else {
                // Create customer record
                $custId = nextId($db, 'customers', 'C');
                $stmtC = $db->prepare("INSERT INTO customers (id, name, email, wa, room) VALUES (?, ?, ?, ?, '')");
                $stmtC->bind_param('ssss', $custId, $name, $email, $wa);
                
                if ($stmtC->execute()) {
                    // Create user record
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $role = 'user';
                    $stmtU = $db->prepare("INSERT INTO users (username, password, role, customer_id) VALUES (?, ?, ?, ?)");
                    $stmtU->bind_param('ssss', $username, $hash, $role, $custId);
                    $stmtU->execute();

                    addLog($db, 'User mendaftar', "$name mendaftar sebagai user baru ($custId)", 'customer');

                    flashMsg('Registrasi berhasil! Silakan masuk.', 'success');
                    header('Location: login.php');
                    exit;
                } else {
                    $error = 'Terjadi kesalahan saat menyimpan data.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Daftar akun baru di KostHub — Sistem manajemen kamar kos modern.">
  <title>Daftar — KostHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
  <div class="login-bg">
    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon"><i class="bi bi-building"></i></div>
        <h1>Kost<span>Hub</span></h1>
        <p>Buat akun baru untuk mulai menyewa</p>
      </div>
      
      <?php if (!empty($error)): ?>
        <div class="login-error" style="display:block"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form id="register-form" autocomplete="off" method="POST" action="register.php">
        <!-- Account Info -->
        <div class="form-row">
          <div class="input-group">
            <label for="reg-username">Username</label>
            <div class="input-wrap">
              <i class="bi bi-at input-icon"></i>
              <input type="text" id="reg-username" name="username" placeholder="Username unik" value="<?= htmlspecialchars($username) ?>" required autofocus />
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="input-group">
            <label for="reg-password">Password</label>
            <div class="input-wrap">
              <i class="bi bi-lock input-icon"></i>
              <input type="password" id="reg-password" name="password" placeholder="Min. 6 karakter" required />
            </div>
          </div>
          <div class="input-group">
            <label for="reg-password2">Konfirmasi Password</label>
            <div class="input-wrap">
              <i class="bi bi-lock input-icon"></i>
              <input type="password" id="reg-password2" name="password_confirm" placeholder="Ulangi password" required />
            </div>
          </div>
        </div>

        <div class="divider"><span>Data Diri</span></div>

        <div class="input-group">
          <label for="reg-name">Nama Lengkap</label>
          <div class="input-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="reg-name" name="name" placeholder="Nama lengkap Anda" value="<?= htmlspecialchars($name) ?>" required />
          </div>
        </div>
        <div class="form-row">
          <div class="input-group">
            <label for="reg-email">Email</label>
            <div class="input-wrap">
              <i class="bi bi-envelope input-icon"></i>
              <input type="email" id="reg-email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($email) ?>" required />
            </div>
          </div>
          <div class="input-group">
            <label for="reg-wa">WhatsApp</label>
            <div class="input-wrap">
              <i class="bi bi-telephone input-icon"></i>
              <input type="text" id="reg-wa" name="wa" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($wa) ?>" required />
            </div>
          </div>
        </div>

        <button type="submit" class="btn-login" id="btn-register">
          <span>Daftar Sekarang</span>
          <i class="bi bi-person-plus"></i>
        </button>
      </form>
      <div class="register-link">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
      </div>
    </div>
  </div>
</body>
</html>
