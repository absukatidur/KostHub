<!-- ─── LAYANAN / REQUESTS ─── -->
<template id="tpl-user-requests">
  <div>
    <div class="section-header">
      <div>
        <h2>Layanan Pengajuan</h2>
        <p>Ajukan pindah kamar atau checkout</p>
      </div>
    </div>
    <div class="two-col mb-16">
      <div class="card request-action-card" data-type="pindah">
        <div class="icon-wrap ic-blue">
          <i class="bi bi-arrow-left-right"></i>
        </div>
        <div class="rac-title">Pindah Kamar</div>
        <div class="rac-desc">Ajukan pindah ke kamar yang tersedia</div>
        <button class="btn btn-primary btn-sm" data-action="req-pindah">Ajukan Pindah</button>
      </div>
      <div class="card request-action-card" data-type="checkout">
        <div class="icon-wrap ic-amber">
          <i class="bi bi-box-arrow-right"></i>
        </div>
        <div class="rac-title">Pengajuan Checkout</div>
        <div class="rac-desc">Informasikan jika tidak memperpanjang sewa</div>
        <button class="btn btn-primary btn-sm" data-action="req-checkout">Ajukan Checkout</button>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">Riwayat Pengajuan</span></div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipe</th>
              <th>Detail</th>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody data-slot="request-rows"></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<template id="tpl-user-request-row">
  <tr>
    <td><span class="rq-id"></span></td>
    <td class="rq-type"></td>
    <td class="rq-detail"></td>
    <td class="rq-date"></td>
    <td class="rq-status"></td>
  </tr>
</template>

<template id="tpl-modal-req-pindah">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Ajukan Pindah Kamar</span>
      <button class="modal-close" data-action="close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Kamar Tujuan</label>
        <select class="form-input" id="rp-room">
          <option value="">-- Pilih Kamar --</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Alasan Pindah</label>
        <textarea class="form-input" rows="3" id="rp-reason" placeholder="Alasan pindah kamar..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-action="close">Batal</button>
      <button class="btn btn-primary" data-action="submit">Kirim Pengajuan</button>
    </div>
  </div>
</template>

<template id="tpl-modal-req-checkout">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Pengajuan Checkout</span>
      <button class="modal-close" data-action="close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Tanggal Checkout</label>
        <input type="date" class="form-input" id="rc-date" />
      </div>
      <div class="form-group">
        <label class="form-label">Alasan</label>
        <textarea class="form-input" rows="3" id="rc-reason" placeholder="Alasan tidak memperpanjang..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-action="close">Batal</button>
      <button class="btn btn-primary" data-action="submit">Kirim Pengajuan</button>
    </div>
  </div>
</template>
