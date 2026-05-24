<!-- ─── ROOMS PAGE TEMPLATES ─── -->

<!-- Master Kamar Page -->
<template id="tpl-master-kamar-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Tipe Kamar</h2>
        <p>Kelola data master kamar kos</p>
      </div>
      <button class="btn btn-primary" data-action="add-room"><i class="bi bi-plus-lg" style="font-size:14px"></i>
        Tambah Kamar</button>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-wrap"><i class="bi bi-search search-icon" style="font-size:14px"></i><input
            id="room-search" placeholder="Cari kamar..." /></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Kamar</th>
              <th>Tipe</th>
              <th>Lantai</th>
              <th>Tipe Sewa</th>
              <th>Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="room-tbody" data-slot="room-rows"></tbody>
        </table>
      </div>
      <div class="pagination"><span class="info" data-bind="count"></span></div>
    </div>
  </div>
</template>

<template id="tpl-room-table-row">
  <tr>
    <td><b class="r-id"></b></td>
    <td class="r-type"></td>
    <td class="r-floor"></td>
    <td class="r-rent"></td>
    <td class="r-price"></td>
    <td class="r-status"></td>
    <td>
      <div style="display:flex;gap:6px">
        <button class="btn btn-secondary btn-sm" data-action="view"><i class="bi bi-eye" style="font-size:12px"></i></button>
        <button class="btn btn-secondary btn-sm" data-action="edit"><i class="bi bi-pencil" style="font-size:12px"></i></button>
        <button class="btn btn-danger btn-sm" data-action="delete"><i class="bi bi-trash" style="font-size:12px"></i></button>
      </div>
    </td>
  </tr>
</template>

<!-- Manajemen Kamar Page -->
<template id="tpl-manajemen-kamar-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Manajemen Kamar</h2>
        <p>Pantau &amp; kelola semua unit kamar</p>
      </div>
    </div>
    <div class="page-tabs">
      <button class="tab-btn active" data-tab="overview">Overview</button>
      <button class="tab-btn" data-tab="list">List Kamar</button>
    </div>
    <div id="kamar-tab-content"></div>
  </div>
</template>

<template id="tpl-kamar-list-row">
  <tr>
    <td><b class="kl-id"></b>
      <div style="font-size:11px;color:var(--slate-muted)" class="kl-floor"></div>
    </td>
    <td class="kl-type"></td>
    <td class="kl-tenant"></td>
    <td class="kl-price"></td>
    <td class="kl-status"></td>
    <td><button class="btn btn-secondary btn-sm" data-action="edit-status">Edit Status</button></td>
  </tr>
</template>

<!-- Room Modals -->
<template id="tpl-modal-add-room">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Kamar</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">ID Kamar</label><input class="form-input" id="mk-id"
            placeholder="A101" /></div>
        <div class="form-group"><label class="form-label">Lantai</label><select class="form-input" id="mk-floor">
            <option>1</option>
            <option>2</option>
            <option>3</option>
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tipe Kamar</label><select class="form-input" id="mk-type">
            <option>Standar</option>
            <option>VIP</option>
            <option>Executive</option>
          </select></div>
        <div class="form-group"><label class="form-label">Tipe Sewa</label><select class="form-input" id="mk-rent">
            <option>Harian</option>
            <option>Bulanan</option>
            <option>Tahunan</option>
          </select></div>
      </div>
      <div class="form-group"><label class="form-label">Harga Sewa</label><input class="form-input" type="number"
          id="mk-price" placeholder="800000" /></div>
      <div class="form-group"><label class="form-label">Fasilitas Kamar</label><textarea class="form-input" rows="2"
          id="mk-fac" placeholder="AC, WiFi, Kamar Mandi Dalam..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>

<template id="tpl-modal-edit-room">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">ID Kamar</label><input class="form-input" id="ek-id" placeholder="A101" /></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Lantai</label><select class="form-input" id="ek-floor">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
          </select></div>
        <div class="form-group"><label class="form-label">Tipe</label><select class="form-input" id="ek-type">
            <option value="Standar">Standar</option>
            <option value="VIP">VIP</option>
            <option value="Executive">Executive</option>
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tipe Sewa</label><select class="form-input" id="ek-rent">
            <option value="Harian">Harian</option>
            <option value="Bulanan">Bulanan</option>
            <option value="Tahunan">Tahunan</option>
          </select></div>
        <div class="form-group"><label class="form-label">Harga</label><input class="form-input" type="number"
            id="ek-price" /></div>
      </div>
      <div class="form-group"><label class="form-label">Fasilitas</label><textarea class="form-input" rows="2"
          id="ek-fac"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>

<template id="tpl-modal-room-detail">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div class="rd-badge"></div>
        <select class="filter-select" id="rd-status" style="font-size:12px">
          <option value="empty">Kosong</option>
          <option value="occupied">Terisi</option>
          <option value="maintenance">Perbaikan</option>
        </select>
      </div>
      <div class="detail-row"><span class="detail-key">ID Kamar</span><span class="detail-val rd-id"></span></div>
      <div class="detail-row"><span class="detail-key">Lantai</span><span class="detail-val rd-floor"></span></div>
      <div class="detail-row"><span class="detail-key">Tipe</span><span class="detail-val rd-type"></span></div>
      <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val rd-rent"></span></div>
      <div class="detail-row"><span class="detail-key">Harga</span><span class="detail-val rd-price"></span></div>
      <div class="detail-row"><span class="detail-key">Penghuni</span><span class="detail-val rd-tenant"></span></div>
      <div class="detail-row"><span class="detail-key">Sewa Hingga</span><span class="detail-val rd-until"></span>
      </div>
      <div class="detail-row"><span class="detail-key">Fasilitas</span><span class="detail-val rd-fac"></span></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Tutup</button><button
        class="btn btn-primary" data-action="save-status">Simpan Status</button></div>
  </div>
</template>
