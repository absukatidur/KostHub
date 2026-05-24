<!-- ─── FASILITAS USER (read-only) ─── -->
<template id="tpl-user-facilities">
  <div>
    <div class="section-header">
      <div>
        <h2>Fasilitas Kos</h2>
        <p>Informasi fasilitas yang tersedia</p>
      </div>
    </div>
    <div class="three-col" data-slot="facility-cards"></div>
  </div>
</template>

<template id="tpl-user-facility-card">
  <div class="card user-facility-card">
    <div class="ufc-status"></div>
    <div class="ufc-icon-wrapper">
      <i class="bi bi-buildings"></i>
    </div>
    <div class="ufc-name"></div>
    <div class="ufc-meta"></div>
    <div class="ufc-desc"></div>
  </div>
</template>
