<!-- ══════════════════════════ MAIN ══════════════════════════ -->
<div id="main">
  <!-- TOPBAR -->
  <header id="topbar">
    <div class="topbar-left">
      <button class="btn-icon" id="menu-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list" style="font-size:16px"></i>
      </button>
      <span class="topbar-title" id="page-title"><?= htmlspecialchars($pageTitleShort ?? 'Dashboard') ?></span>
    </div>
    <div class="topbar-right">
      <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'AD', 0, 2)) ?></div>
    </div>
  </header>

  <!-- PAGE CONTENT -->
  <main id="page-content">
