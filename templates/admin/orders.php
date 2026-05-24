<!-- ─── ORDER PAGE TEMPLATES ─── -->
<template id="tpl-order-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Order / Penyewaan</h2>
        <p>Kelola transaksi sewa kamar</p>
      </div>
      <button class="btn btn-primary" data-action="add-order"><i class="bi bi-plus-lg" style="font-size:14px"></i> Buat Order</button>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-wrap"><i class="bi bi-search search-icon" style="font-size:14px"></i><input
            id="ord-search" placeholder="Cari order..." /></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Order</th>
              <th>Customer</th>
              <th>Kamar</th>
              <th>Periode</th>
              <th>Tipe</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="ord-tbody" data-slot="order-rows"></tbody>
        </table>
      </div>
      <div class="pagination"><span class="info" data-bind="count"></span></div>
    </div>
  </div>
</template>

<template id="tpl-order-row">
  <tr>
    <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--brand-accent)" class="o-id"></span></td>
    <td>
      <div style="font-weight:600" class="o-customer"></div>
    </td>
    <td><b class="o-room"></b></td>
    <td>
      <div style="font-size:12px" class="o-start"></div>
      <div style="font-size:12px;color:var(--slate-muted)" class="o-end"></div>
    </td>
    <td class="o-type"></td>
    <td style="font-weight:600" class="o-total"></td>
    <td class="o-status"></td>
    <td>
      <div style="display:flex;gap:6px" class="o-actions"></div>
    </td>
  </tr>
</template>

<template id="tpl-modal-add-order">
  <div class="modal modal-lg">
    <div class="modal-header"><span class="modal-title">Buat Order Baru</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div
        style="background:var(--slate-faint);border-radius:8px;padding:12px;margin-bottom:16px;font-size:12px;color:var(--slate-base)">
        <i class="bi bi-info-circle" style="font-size:13px;display:inline;vertical-align:middle"></i> Pilih customer
        dan kamar kosong
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Customer</label><select class="form-input" id="no-cust">
            <option value="">-- Pilih Customer --</option>
          </select></div>
        <div class="form-group"><label class="form-label">Kamar</label><select class="form-input" id="no-room">
            <option value="">-- Pilih Kamar --</option>
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tipe Sewa</label><select class="form-input" id="no-type">
            <option>Harian</option>
            <option selected>Bulanan</option>
            <option>Tahunan</option>
          </select></div>
        <div class="form-group"><label class="form-label">Mulai</label><input type="date" class="form-input"
            id="no-start" /></div>
      </div>
      <div class="form-group"><label class="form-label">Akhir</label><input type="date" class="form-input"
          id="no-end" /></div>
      <div
        style="background:var(--blue-faded);border:1px solid var(--blue-soft);border-radius:8px;padding:14px;margin-top:8px">
        <div style="font-size:12px;font-weight:600;color:var(--brand-accent-hover);margin-bottom:8px">RINGKASAN ORDER</div>
        <div class="detail-row"><span class="detail-key">Total</span><span class="detail-val" id="no-total-display"
            style="color:var(--brand-accent-hover);font-size:16px">Rp 0</span></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save"><i class="bi bi-check-lg" style="font-size:13px"></i> Simpan
        Order</button></div>
  </div>
</template>

<template id="tpl-modal-order-detail">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div style="margin-bottom:14px" class="od-badge"></div>
      <div class="detail-row"><span class="detail-key">Customer</span><span class="detail-val od-customer"></span>
      </div>
      <div class="detail-row"><span class="detail-key">Kamar</span><span class="detail-val od-room"></span></div>
      <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val od-type"></span></div>
      <div class="detail-row"><span class="detail-key">Mulai</span><span class="detail-val od-start"></span></div>
      <div class="detail-row"><span class="detail-key">Selesai</span><span class="detail-val od-end"></span></div>
      <div class="detail-row"><span class="detail-key">Total</span><span class="detail-val od-total"
          style="color:var(--brand-accent)"></span></div>
    </div>
    <div class="modal-footer" data-slot="detail-footer"></div>
  </div>
</template>
