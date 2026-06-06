<?php
$initial = 'U';
if (!empty($_SESSION['username'])) {
  $initial = strtoupper(substr($_SESSION['username'], 0, 2));
}
?>
<!--  MAIN  -->
<div id="main">
  <header id="topbar">
    <div class="topbar-left">
      <span class="topbar-title" id="page-title"><?= htmlspecialchars($pageTitleShort ?? 'Dashboard') ?></span>
    </div>
    <div class="topbar-right">
      <div class="avatar" id="user-avatar"><?= htmlspecialchars($initial) ?></div>
    </div>
  </header>
  <main id="page-content">