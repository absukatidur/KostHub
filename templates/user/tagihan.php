<!-- ─── TAGIHAN / BILLING ─── -->
<template id="tpl-user-tagihan">
  <div>
    <div class="section-header">
      <div>
        <h2>Tagihan & Pembayaran</h2>
        <p>Riwayat transaksi sewa kamar</p>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Order</th>
              <th>Periode</th>
              <th>Tipe</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody data-slot="order-rows"></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<template id="tpl-user-order-row">
  <tr>
    <td><span class="uo-id"></span></td>
    <td>
      <div class="uo-start"></div>
      <div class="uo-end"></div>
    </td>
    <td class="uo-type"></td>
    <td class="uo-total"></td>
    <td class="uo-status"></td>
    <td class="uo-action"></td>
  </tr>
</template>

<!-- Payment Modal -->
<template id="tpl-modal-payment">
  <div class="modal modal-lg">
    <div class="modal-header">
      <span class="modal-title">Pembayaran</span>
      <button class="modal-close" data-action="close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="payment-summary">
        <div class="payment-summary-title">TOTAL PEMBAYARAN</div>
        <div class="pay-total"></div>
        <div class="pay-order-id"></div>
      </div>  
      <div class="pay-methods-title">Pilih Metode Pembayaran</div>
      <div class="pay-methods" data-slot="methods"></div>
      <div class="pay-detail d-none" data-slot="pay-detail"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-action="close">Batal</button>
      <button class="btn btn-primary d-none" data-action="pay">
        <i class="bi bi-check-lg"></i> Konfirmasi Bayar
      </button>
    </div>
  </div>
</template>
