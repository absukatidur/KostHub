<!-- ─── CUSTOMER PAGE TEMPLATES ─── -->
<template id="tpl-customer-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Customer</h2>
        <p>Data penghuni kos</p>
      </div>
      <button class="btn btn-primary" data-action="add-customer"><i class="bi bi-person-plus" style="font-size:14px"></i> Tambah Customer</button>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-wrap"><i class="bi bi-search search-icon" style="font-size:14px"></i><input
            id="cust-search" placeholder="Cari nama, email, WA..." /></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Email / WA</th>

              <th>Kamar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="cust-tbody" data-slot="customer-rows"></tbody>
        </table>
      </div>
      <div class="pagination"><span class="info" data-bind="count"></span></div>
    </div>
  </div>
</template>

<template id="tpl-customer-row">
  <tr>
    <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--slate-muted)" class="c-id"></span></td>
    <td>
      <div style="font-weight:600" class="c-name"></div>
    </td>
    <td>
      <div style="color:var(--brand-accent)" class="c-email"></div>
      <div style="font-size:12px;color:var(--slate-muted)" class="c-wa"></div>
    </td>

    <td class="c-room"></td>
    <td>
      <div style="display:flex;gap:6px">
        <button class="btn btn-secondary btn-sm" data-action="view"><i class="bi bi-eye" style="font-size:12px"></i></button>
        <button class="btn btn-secondary btn-sm" data-action="edit"><i class="bi bi-pencil" style="font-size:12px"></i></button>
        <button class="btn btn-danger btn-sm" data-action="delete"><i class="bi bi-trash" style="font-size:12px"></i></button>
      </div>
    </td>
  </tr>
</template>

<template id="tpl-modal-add-customer">
  <div class="modal modal-lg">
    <div class="modal-header"><span class="modal-title">Tambah Customer</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" id="ac-name"
            placeholder="Nama penghuni" /></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email"
            id="ac-email" placeholder="email@domain.com" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">WhatsApp</label><input class="form-input" id="ac-wa"
            placeholder="08xxxxxxxxxx" /></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan Customer</button></div>
  </div>
</template>

<template id="tpl-modal-edit-customer">
  <div class="modal modal-lg">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nama</label><input class="form-input" id="ec-name" /></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-input" id="ec-email" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">WhatsApp</label><input class="form-input" id="ec-wa" />
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>

<template id="tpl-modal-customer-detail">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="detail-row"><span class="detail-key">ID</span><span class="detail-val cd-id"></span></div>
      <div class="detail-row"><span class="detail-key">Email</span><span class="detail-val cd-email"></span></div>
      <div class="detail-row"><span class="detail-key">Kamar</span><span class="detail-val cd-room"></span></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Tutup</button></div>
  </div>
</template>
