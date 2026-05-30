<?php
$initial = 'U';
if (!empty($_SESSION['username'])) {
    $initial = strtoupper(substr($_SESSION['username'], 0, 2));
}
?>
<!-- ══════════════════════════ MAIN ══════════════════════════ -->
<div id="main">
  <header id="topbar">
    <div class="topbar-left">
      <button class="btn-icon" id="menu-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list" style="font-size:16px"></i>
      </button>
      <span class="topbar-title" id="page-title"><?= htmlspecialchars($pageTitleShort ?? 'Dashboard') ?></span>
    </div>
    <div class="topbar-right">
      <div class="avatar" id="user-avatar"><?= htmlspecialchars($initial) ?></div>
    </div>
  </header>
  <main id="page-content">
