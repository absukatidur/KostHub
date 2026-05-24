<!-- ─── FASILITAS TEMPLATES ─── -->
<template id="tpl-fasilitas-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Fasilitas Umum</h2>
        <p>Kelola fasilitas bersama di kos</p>
      </div>
      <button class="btn btn-primary" data-action="add-facility"><i class="bi bi-plus-lg" style="font-size:14px"></i> Tambah Fasilitas</button>
    </div>
    <div class="three-col" style="margin-bottom:14px" data-slot="facility-cards"></div>
  </div>
</template>

<template id="tpl-facility-card">
  <div class="card" style="position:relative">
    <div style="position:absolute;top:14px;right:14px" class="fc-status"></div>
    <div
      style="font-size:40px;border-radius:10px;background:var(--blue-faded);display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:var(--brand-accent)">
      <i class="bi bi-buildings" style="font-size:18px"></i>
    </div>
    <div style="font-weight:700;font-size:15px;margin-bottom:4px" class="fc-name"></div>
    <div style="font-size:12px;color:var(--slate-muted);margin-bottom:8px" class="fc-meta"></div>
    <div style="font-size:13px;color:var(--slate-mid);margin-bottom:14px" class="fc-desc"></div>
    <div style="display:flex;gap:6px">
      <button class="btn btn-secondary btn-sm" data-action="edit"><i class="bi bi-pencil" style="font-size:12px"></i></button>
      <button class="btn btn-danger btn-sm" data-action="delete"><i class="bi bi-trash" style="font-size:12px"></i></button>
    </div>
  </div>
</template>

<template id="tpl-modal-add-facility">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Fasilitas</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Nama Fasilitas</label><input class="form-input" id="af-name"
          placeholder="Nama fasilitas" /></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Lantai</label><input class="form-input" id="af-floor"
            placeholder="1" /></div>
        <div class="form-group"><label class="form-label">Status</label><select class="form-input" id="af-status">
            <option value="ok">Normal</option>
            <option value="repair">Butuh Perbaikan</option>
            <option value="repairing">Sedang Perbaikan</option>
          </select></div>
      </div>
      <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-input" rows="2"
          id="af-desc" placeholder="Deskripsi..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>

<template id="tpl-modal-edit-facility">
  <div class="modal">
    <div class="modal-header"><span class="modal-title"></span><button class="modal-close" data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Nama</label><input class="form-input" id="ef-name" /></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Lantai</label><input class="form-input" id="ef-floor" />
        </div>
        <div class="form-group"><label class="form-label">Status</label><select class="form-input" id="ef-status">
            <option value="ok">Normal</option>
            <option value="repair">Butuh Perbaikan</option>
            <option value="repairing">Sedang Perbaikan</option>
          </select></div>
      </div>
      <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-input" rows="2"
          id="ef-desc"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="save">Simpan</button></div>
  </div>
</template>
