<?php
$pageTitle = 'Login — KosManager';
$extraCss = ['assets/css/login.css'];
require_once 'components/header.php';
?>
  <div class="login-bg">
    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon"><i class="bi bi-building"></i></div>
        <h1>Kost<span>Hub</span></h1>
        <p>Masuk ke akun Anda</p>
      </div>
      <div id="login-error" class="login-error" style="display:none"></div>
      <form id="login-form" autocomplete="off">
        <div class="input-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="username" placeholder="Masukkan username" required autofocus />
          </div>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" id="password" placeholder="Masukkan password" required />
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
          <div class="demo-row"><span class="demo-badge admin">Admin</span><code>admin / admin123</code></div>
          <div class="demo-row"><span class="demo-badge user">User</span><code>andi / user123</code></div>
        </div>
        <div class="login-footer-text">
          Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/login.js?v=<?= time() ?>"></script>
</body>
</html>