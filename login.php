<?php
require_once 'includes/db.php';

// Redirect if already logged in
if (!empty($_SESSION['role'])) {
    if (in_array($_SESSION['role'], ['admin', 'owner'])) { header('Location: admin/dashboard.php'); exit; }
    if ($_SESSION['role'] === 'user')  { header('Location: user/dashboard.php');  exit; }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Username atau password salah';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['customer_id'] = $user['customer_id'];

            if (in_array($user['role'], ['admin', 'owner'])) {
                header('Location: admin/dashboard.php');
                exit;
            } else {
                header('Location: user/dashboard.php');
                exit;
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
  <title>Login — KostHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
  <div class="login-bg">
    <div class="login-card">
      <div class="login-logo">
        <h1>Kost<span>Hub</span></h1>
        <p>Masuk ke akun Anda</p>
      </div>
      
      <?php if (!empty($error)): ?>
        <div class="login-error" style="display:block"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <?php showFlash(); ?>
      
      <form id="login-form" autocomplete="off" method="POST" action="login.php">
        <div class="input-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="username" name="username" placeholder="Masukkan username" value="<?= htmlspecialchars($username ?? '') ?>" required autofocus />
          </div>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required />
          </div>
        </div>
        <button type="submit" class="btn-login" id="btn-login">
          <span>Masuk</span>
          <i class="bi bi-arrow-right"></i>
        </button>
      </form>
      <div class="login-footer">
        <div class="demo-accounts">
          <p class="demo-title">Demo Accounts</p>
          <div class="demo-row"><span class="demo-badge owner">Owner</span><code>owner / owner123</code></div>
          <div class="demo-row"><span class="demo-badge admin">Admin</span><code>admin / admin123</code></div>
          <div class="demo-row"><span class="demo-badge user">User</span><code>andi / user123</code></div>
        </div>
        <div class="login-footer-text">
          Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
