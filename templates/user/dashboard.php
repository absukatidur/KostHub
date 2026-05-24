<!-- ─── USER DASHBOARD ─── -->
<template id="tpl-user-dashboard">
  <div>
    <div class="section-header">
      <div>
        <h2 class="ud-greeting"></h2>
        <p class="ud-date"></p>
      </div>
    </div>
    <!-- Room Info Card -->
    <div class="card user-room-card mb-16">
      <div class="user-room-card-header">
        <div class="icon-wrap ic-blue">
          <i class="bi bi-door-open"></i>
        </div>
        <div>
          <div class="ud-room-id"></div>
          <div class="ud-room-meta"></div>
        </div>
        <div class="ud-room-badge"></div>
      </div>
      <div class="detail-row"><span class="detail-key">Tipe</span><span class="detail-val ud-room-type"></span></div>
      <div class="detail-row"><span class="detail-key">Harga</span><span class="detail-val ud-room-price"></span>
      </div>
      <div class="detail-row"><span class="detail-key">Fasilitas</span><span class="detail-val ud-room-fac"></span>
      </div>
      <div class="detail-row"><span class="detail-key">Sewa Hingga</span><span
          class="detail-val ud-room-until"></span></div>
    </div>
    <!-- Alerts -->
    <div data-slot="alerts" class="mb-16"></div>
    <!-- Stats Row -->
    <div class="stats-grid mb-16" data-slot="stats"></div>
  </div>
</template>

<template id="tpl-user-alert">
  <div class="user-alert">
    <i class="bi bi-exclamation-circle"></i>
    <div class="alert-text"></div>
    <button class="btn btn-primary btn-sm alert-action"></button>
  </div>
</template>
