<!-- ─── LOG TEMPLATES ─── -->
<template id="tpl-log-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Log Aktivitas</h2>
        <p>Riwayat semua aksi admin</p>
      </div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-wrap"><i class="bi bi-search search-icon" style="font-size:14px"></i><input
            id="log-search" placeholder="Cari aktivitas..." /></div>
      </div>
      <div class="activity-list" id="log-list" data-slot="log-items"></div>
      <div class="pagination" style="margin-top:12px"><span class="info" data-bind="count"></span></div>
    </div>
  </div>
</template>

<template id="tpl-log-item">
  <div class="activity-item">
    <div class="act-dot"><i style="font-size:14px"></i></div>
    <div class="act-content" style="flex:1">
      <div class="act-title"></div>
      <div class="act-detail act-meta"></div>
    </div>
    <div class="log-time" style="font-size:11px;color:var(--slate-muted);white-space:nowrap;padding-left:12px"></div>
  </div>
</template>
