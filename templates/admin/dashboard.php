<!-- ─── DASHBOARD PAGE TEMPLATES ─── -->

<!-- Dashboard Page -->
<template id="tpl-dashboard-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Selamat datang, Admin</h2>
        <p data-bind="dateText"></p>
      </div>
      <button class="btn btn-primary" data-action="create-order"><i class="bi bi-plus-lg" style="font-size:14px"></i> Buat Order</button>
    </div>
    <div class="stats-grid" data-slot="stats"></div>
    <div class="two-col" style="margin-bottom:14px">
      <div class="card">
        <div class="card-header"><span class="card-title">Pendapatan Penyewaan</span></div>
        <div class="chart-bars" data-slot="chart"></div>
        <div style="font-size:11px;color:var(--slate-muted);text-align:center;margin-top:6px">Dalam jutaan Rp</div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">Order Terbaru</span><button class="btn btn-secondary btn-sm"
            data-action="view-orders">Lihat Semua</button></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Customer</th>
                <th>Kamar</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody data-slot="recent-orders"></tbody>
          </table>
        </div>
      </div>
    </div>  
    <div class="two-col">
      <div class="card">
        <div class="card-header"><span class="card-title">Aktivitas Terkini</span></div>
        <div class="activity-list" data-slot="recent-logs"></div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">Overview Kamar</span><button
            class="btn btn-secondary btn-sm" data-action="view-rooms">Detail</button></div>
        <div class="legend" data-slot="legend"></div>
        <div class="room-grid" data-slot="room-overview"></div>
      </div>
    </div>
  </div>
</template>

<!-- Item: Stat Card -->
<template id="tpl-stat-card">
  <div class="stat-card">
    <div class="icon-wrap"><i style="font-size:16px"></i></div>
    <div class="label"></div>
    <div class="value"></div>
    <div class="sub"></div>
  </div>
</template>

<!-- Item: Chart Bar -->
<template id="tpl-chart-bar">
  <div class="chart-bar-wrap">
    <div class="chart-bar"></div><span class="chart-bar-label"></span>
  </div>
</template>

<!-- Item: Recent Order Row -->
<template id="tpl-recent-order-row">
  <tr>
    <td>
      <div class="ro-customer" style="font-weight:600"></div>
      <div class="ro-id" style="font-size:11px;color:var(--slate-muted)"></div>
    </td>
    <td class="ro-room"></td>
    <td class="ro-status"></td>
  </tr>
</template>

<!-- Item: Activity Item -->
<template id="tpl-activity-item">
  <div class="activity-item">
    <div class="act-dot"><i style="font-size:14px"></i></div>
    <div class="act-content">
      <div class="act-title"></div>
      <div class="act-detail act-meta"></div>
      <div class="act-time act-meta"></div>
    </div>
  </div>
</template>

<!-- Item: Room Cell -->
<template id="tpl-room-cell">
  <div class="room-cell">
    <div class="room-num"></div>
    <div class="room-type"></div>
    <div class="room-status-text" style="font-size:10px;margin-top:2px;opacity:.7"></div>
  </div>
</template>

<!-- Item: Legend Item -->
<template id="tpl-legend-item">
  <div class="legend-item">
    <div class="legend-dot"></div><span class="legend-text"></span>
  </div>
</template>