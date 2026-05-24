<!-- ══════════════════════════ SHARED TEMPLATES ══════════════════════════ -->

<!-- Confirm Dialog -->
<template id="tpl-modal-confirm">
  <div class="confirm-overlay">
    <div class="confirm-box">
      <div class="confirm-icon ic-red"><i class="bi bi-exclamation-triangle"></i></div>
      <div class="confirm-title"></div>
      <div class="confirm-msg"></div>
      <div class="confirm-actions">
        <button class="btn btn-secondary" data-action="cancel">Batal</button>
        <button class="btn btn-danger" data-action="confirm">Hapus</button>
      </div>
    </div>
  </div>
</template>

<!-- Stat Card -->
<template id="tpl-stat-card">
  <div class="stat-card">
    <div class="icon-wrap"><i></i></div>
    <div class="label"></div>
    <div class="value"></div>
    <div class="sub"></div>
  </div>
</template>
