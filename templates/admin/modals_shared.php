<!-- ══════════════════════════ SHARED MODALS ══════════════════════════ -->

<!-- Confirm Dialog -->
<template id="tpl-modal-confirm">
  <div class="confirm-overlay">
    <div class="confirm-box">
      <div class="confirm-icon ic-red"><i data-lucide="alert-triangle" style="width:24px;height:24px"></i></div>
      <div class="confirm-title"></div>
      <div class="confirm-msg"></div>
      <div class="confirm-actions">
        <button class="btn btn-secondary" data-action="cancel">Batal</button>
        <button class="btn btn-danger" data-action="confirm">Hapus</button>
      </div>
    </div>
  </div>
</template>

<!-- Settings Modal -->
<template id="tpl-modal-settings">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">Pengaturan</span><button class="modal-close"
        data-action="close"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="detail-row"><span class="detail-key">Versi</span><span class="detail-val">1.0.0</span></div>
      <div class="detail-row"><span class="detail-key">Penyimpanan</span><span class="detail-val">MySQL
          Database</span></div>
      <div
        style="margin-top:16px;padding:12px;background:var(--green-50);border-radius:8px;font-size:12px;color:var(--green-700)">
        <b>Info:</b> Data tersimpan di MySQL database server.
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Tutup</button></div>
  </div>
</template>
