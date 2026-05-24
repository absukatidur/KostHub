<!-- ─── ADMIN REQUESTS TEMPLATES ─── -->
<template id="tpl-admin-requests-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Permintaan User</h2>
        <p>Kelola pengajuan pindah kamar &amp; checkout</p>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Tipe</th>
              <th>Detail</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody data-slot="request-rows"></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<template id="tpl-admin-request-row">
  <tr>
    <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--slate-muted)" class="arq-id"></span>
    </td>
    <td style="font-weight:600" class="arq-customer"></td>
    <td class="arq-type"></td>
    <td class="arq-detail" style="font-size:12px;max-width:220px"></td>
    <td style="font-size:12px;color:var(--slate-muted)" class="arq-date"></td>
    <td class="arq-status"></td>
    <td>
      <div style="display:flex;gap:6px" class="arq-actions"></div>
    </td>
  </tr>
</template>
