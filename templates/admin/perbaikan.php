<!-- ─── PERBAIKAN TEMPLATES ─── -->
<template id="tpl-perbaikan-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Perbaikan</h2>
        <p>Monitor kerusakan kamar &amp; fasilitas umum</p>
      </div>
      <button class="btn btn-primary" data-action="add-repair"><i class="bi bi-plus-lg" style="font-size:14px"></i> Laporkan Kerusakan</button>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-wrap"><i class="bi bi-search search-icon" style="font-size:14px"></i><input
            id="rep-search" placeholder="Cari laporan..." /></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Target</th>
              <th>Masalah</th>
              <th>Prioritas</th>
              <th>Dilaporkan</th>
              <th>Teknisi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="rep-tbody" data-slot="repair-rows"></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<template id="tpl-repair-row">
  <tr>
    <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--slate-muted)" class="rp-id"></span></td>
    <td><b class="rp-target"></b></td>
    <td class="rp-issue"></td>
    <td class="rp-priority"></td>
    <td style="font-size:12px;color:var(--slate-muted)" class="rp-reported"></td>
    <td class="rp-tech"></td>
    <td class="rp-status"></td>
    <td>
      <div style="display:flex;gap:6px">
        <button class="btn btn-secondary btn-sm" data-action="update">Update</button>
        <button class="btn btn-danger btn-sm" data-action="delete"><i class="bi bi-trash" style="font-size:12px"></i></button>
      </div>
    </td>
  </tr>
</template>

<template id="tpl-modal-add-repair">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">Laporkan Kerusakan</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Jenis</label><select class="form-input" id="nr-type">
          <option value="kamar">Kamar</option>
          <option value="fasum">Fasilitas Umum</option>
        </select></div>
      <div class="form-group"><label class="form-label">Target</label><select class="form-input"
          id="nr-target"></select></div>
      <div class="form-group"><label class="form-label">Deskripsi Masalah</label><textarea class="form-input" rows="3"
          id="nr-issue" placeholder="Jelaskan kerusakan..."></textarea></div>
      <div class="form-group"><label class="form-label">Teknisi</label><input class="form-input" id="nr-tech"
          placeholder="Nama teknisi (opsional)" /></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan Laporan</button></div>
  </div>
</template>

<template id="tpl-modal-update-repair">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="detail-row"><span class="detail-key">Target</span><span class="detail-val ur-target"></span></div>
      <div class="detail-row"><span class="detail-key">Masalah</span><span class="detail-val ur-issue"></span></div>
      <div class="form-group" style="margin-top:16px"><label class="form-label">Status</label>
        <select class="form-input" id="ur-status">
          <option value="pending">Pending</option>
          <option value="repairing">Sedang Perbaikan</option>
          <option value="done">Selesai</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Teknisi</label><input class="form-input" id="ur-tech" /></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>
