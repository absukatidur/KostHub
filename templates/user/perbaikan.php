<!-- ─── PERBAIKAN USER ─── -->
<template id="tpl-user-repairs">
  <div>
    <div class="section-header">
      <div>
        <h2>Perbaikan</h2>
        <p>Laporkan &amp; lacak perbaikan fasilitas</p>
      </div>
      <button class="btn btn-primary" data-action="add-repair">
        <i class="bi bi-plus-lg"></i> Lapor Kerusakan Baru
      </button>
    </div>

    <!-- PUBLIC REPAIRS CARD -->
    <div class="card public-repairs-card">
      <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--slate-bright);display:flex;align-items:center;gap:8px">
        <i class="bi bi-info-circle-fill" style="color:var(--brand-accent)"></i> Laporan Fasilitas Umum Terbuka
      </h3>
      <div class="public-repairs-list" data-slot="public-repair-items"></div>
    </div>

    <!-- MY REPORTS CARD -->
    <div class="card">
      <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--slate-bright);display:flex;align-items:center;gap:8px">
        <i class="bi bi-person-fill" style="color:var(--brand-accent)"></i> Riwayat Laporan Saya
      </h3>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Target</th>
              <th>Masalah</th>
              <th>Status</th>
              <th>Teknisi</th>
            </tr>
          </thead>
          <tbody data-slot="repair-rows"></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<template id="tpl-user-repair-row">
  <tr>
    <td><span class="ur-id"></span></td>
    <td><b class="ur-target"></b></td>
    <td class="ur-issue"></td>
    <td class="ur-status"></td>
    <td class="ur-tech"></td>
  </tr>
</template>

<template id="tpl-public-repair-item">
  <div class="public-repair-item">
    <div>
      <div class="pr-target"></div>
      <div class="pr-issue"></div>
      <div class="pr-meta"><i class="bi bi-people-fill" style="margin-right:4px"></i> <span class="pr-votes">1</span> orang telah melaporkan</div>
    </div>
    <button class="btn btn-secondary btn-sm pr-vote-btn" data-action="vote">
      <i class="bi bi-hand-thumbs-up"></i> Saya juga mengalami ini
    </button>
  </div>
</template>

<template id="tpl-modal-user-repair">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Lapor Kerusakan</span>
      <button class="modal-close" data-action="close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis</label>
        <select class="form-input" id="urp-type">
          <option value="kamar">Kamar Saya</option>
          <option value="fasum">Fasilitas Umum</option>
        </select>
      </div>
      <div class="form-group" id="urp-fasum-group" style="display:none">
        <label class="form-label">Fasilitas</label>
        <select class="form-input" id="urp-fasum"></select>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi Masalah</label>
        <textarea class="form-input" rows="3" id="urp-issue" placeholder="Jelaskan kerusakan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-action="close">Batal</button>
      <button class="btn btn-primary" data-action="save">Kirim Laporan</button>
    </div>
  </div>
</template>
