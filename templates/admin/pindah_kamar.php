<!-- ─── PINDAH KAMAR TEMPLATES ─── -->
<template id="tpl-pindah-kamar-page">
  <div>
    <div class="section-header">
      <div>
        <h2>Pindah Kamar</h2>
        <p>Pindahkan penghuni antar kamar</p>
      </div>
    </div>
    <div class="two-col">
      <div class="card">
        <div class="card-title" style="margin-bottom:14px">Kamar Terisi</div>
        <div data-slot="occupied" style="display:flex;flex-direction:column;gap:8px"></div>
      </div>
      <div class="card">
        <div class="card-title pk-avail-title" style="margin-bottom:14px"></div>
        <div data-slot="empty" style="display:flex;flex-direction:column;gap:8px"></div>
      </div>
    </div>
  </div>
</template>

<template id="tpl-occupied-room-item">
  <div
    style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid var(--slate-soft);border-radius:8px;cursor:pointer">
    <div>
      <div style="font-weight:600" class="pk-room-id"></div>
      <div style="font-size:12px;color:var(--slate-muted)" class="pk-tenant"></div>
    </div>
    <button class="btn btn-primary btn-sm">Pindah <i class="bi bi-arrow-right" style="font-size:12px"></i></button>
  </div>
</template>

<template id="tpl-empty-room-item">
  <div
    style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--green-faded);border:1px solid var(--green-soft);border-radius:8px">
    <div>
      <div style="font-weight:600;color:var(--green-pale)" class="er-id"></div>
      <div style="font-size:12px;color:var(--green-vivid)" class="er-info"></div>
    </div>
    <span class="badge badge-green">Kosong</span>
  </div>
</template>

<template id="tpl-modal-pindah-kamar">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">Form Pindah Kamar</span><button class="modal-close"
        data-action="close"><i class="bi bi-x-lg" style="font-size:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Penghuni</label><input class="form-input pk-tenant-name"
          readonly style="background:var(--slate-faint)" /></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Kamar Asal</label><input class="form-input" id="pk-from"
            readonly style="background:var(--slate-faint)" /></div>
        <div class="form-group"><label class="form-label">Kamar Tujuan</label><select class="form-input" id="pk-to">
            <option value="">-- Pilih --</option>
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Mulai Periode Baru</label><input type="date"
            class="form-input" id="pk-start" /></div>
        <div class="form-group"><label class="form-label">Akhir Periode</label><input type="date" class="form-input"
            id="pk-end" /></div>
      </div>
      <div class="form-group"><label class="form-label">Catatan</label><textarea class="form-input" rows="2"
          id="pk-note" placeholder="Alasan pindah..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-action="close">Batal</button><button
        class="btn btn-primary" data-action="confirm">Konfirmasi Pindah</button></div>
  </div>
</template>
