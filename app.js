// ─── DATA ───
const rooms = [
  { id:'A101', floor:1, type:'Standar', rent:'Bulanan', price:800000, status:'occupied', tenant:'Andi Pratama', until:'2025-08-31' },
  { id:'A102', floor:1, type:'Standar', rent:'Bulanan', price:800000, status:'empty', tenant:'-', until:'-' },
  { id:'A103', floor:1, type:'VIP', rent:'Bulanan', price:1500000, status:'cleaning', tenant:'-', until:'-' },
  { id:'A201', floor:2, type:'Standar', rent:'Bulanan', price:800000, status:'occupied', tenant:'Budi Santoso', until:'2025-09-15' },
  { id:'A202', floor:2, type:'VIP', rent:'Bulanan', price:1500000, status:'maintenance', tenant:'-', until:'-' },
  { id:'A203', floor:2, type:'Standar', rent:'Bulanan', price:800000, status:'occupied', tenant:'Cici Marlina', until:'2025-07-31' },
  { id:'B101', floor:1, type:'Standar', rent:'Harian', price:150000, status:'empty', tenant:'-', until:'-' },
  { id:'B102', floor:1, type:'Standar', rent:'Harian', price:150000, status:'occupied', tenant:'Dika Rahman', until:'2025-07-20' },
  { id:'B201', floor:2, type:'Executive', rent:'Bulanan', price:2000000, status:'occupied', tenant:'Eka Putri', until:'2025-10-31' },
  { id:'B202', floor:2, type:'Executive', rent:'Bulanan', price:2000000, status:'empty', tenant:'-', until:'-' },
  { id:'C101', floor:1, type:'Standar', rent:'Bulanan', price:800000, status:'cleaning', tenant:'-', until:'-' },
  { id:'C201', floor:2, type:'VIP', rent:'Tahunan', price:15000000, status:'occupied', tenant:'Fajar Nugroho', until:'2026-01-15' },
];
const customers = [
  { id:'C001', name:'Andi Pratama', email:'andi@gmail.com', wa:'081234567890', ktp:'3374010101990001', room:'A101' },
  { id:'C002', name:'Budi Santoso', email:'budi@gmail.com', wa:'081234567891', ktp:'3374010101990002', room:'A201' },
  { id:'C003', name:'Cici Marlina', email:'cici@gmail.com', wa:'081234567892', ktp:'3374010101990003', room:'A203' },
  { id:'C004', name:'Dika Rahman', email:'dika@gmail.com', wa:'081234567893', ktp:'3374010101990004', room:'B102' },
  { id:'C005', name:'Eka Putri', email:'eka@gmail.com', wa:'081234567894', ktp:'3374010101990005', room:'B201' },
  { id:'C006', name:'Fajar Nugroho', email:'fajar@gmail.com', wa:'081234567895', ktp:'3374010101990006', room:'C201' },
];
const facilities = [
  { id:'F001', name:'Parkir Motor', floor:'B1', desc:'Area parkir 30 motor', status:'ok' },
  { id:'F002', name:'Dapur Bersama', floor:'1', desc:'Dapur lengkap dengan kompor gas', status:'repair' },
  { id:'F003', name:'Ruang Laundry', floor:'1', desc:'3 mesin cuci + 2 pengering', status:'ok' },
  { id:'F004', name:'Lobi & CCTV', floor:'1', desc:'CCTV 24 jam + resepsionis', status:'ok' },
  { id:'F005', name:'Rooftop Garden', floor:'3', desc:'Taman rooftop dengan kursi santai', status:'repairing' },
];
const orders = [
  { id:'ORD-001', customer:'Andi Pratama', room:'A101', type:'Bulanan', start:'2025-07-01', end:'2025-07-31', total:800000, status:'paid' },
  { id:'ORD-002', customer:'Budi Santoso', room:'A201', type:'Bulanan', start:'2025-07-01', end:'2025-07-31', total:800000, status:'pending' },
  { id:'ORD-003', customer:'Eka Putri', room:'B201', type:'Bulanan', start:'2025-07-01', end:'2025-07-31', total:2000000, status:'pending' },
  { id:'ORD-004', customer:'Fajar Nugroho', room:'C201', type:'Tahunan', start:'2025-01-15', end:'2026-01-15', total:15000000, status:'paid' },
  { id:'ORD-005', customer:'Dika Rahman', room:'B102', type:'Harian', start:'2025-07-15', end:'2025-07-20', total:750000, status:'pending' },
];
const repairs = [
  { id:'REP-001', target:'Kamar A202', type:'kamar', issue:'AC rusak', reported:'2025-07-10', status:'repairing', tech:'Pak Slamet' },
  { id:'REP-002', target:'Dapur Bersama', type:'fasum', issue:'Kompor gas bocor', reported:'2025-07-12', status:'pending', tech:'-' },
  { id:'REP-003', target:'Rooftop Garden', type:'fasum', issue:'Lampu mati', reported:'2025-07-08', status:'repairing', tech:'Pak Joko' },
  { id:'REP-004', target:'Kamar B102', type:'kamar', issue:'WC tersumbat', reported:'2025-07-05', status:'done', tech:'Pak Slamet' },
];
const logs = [
  { time:'2025-07-15 14:32', action:'Order dibuat', detail:'ORD-005 oleh Dika Rahman – Kamar B102', type:'order' },
  { time:'2025-07-15 11:10', action:'Status kamar diubah', detail:'A103 → Need Cleaning', type:'room' },
  { time:'2025-07-14 09:45', action:'Customer ditambah', detail:'Fajar Nugroho terdaftar', type:'customer' },
  { time:'2025-07-13 16:20', action:'Invoice dikirim', detail:'ORD-003 ke Eka Putri via WhatsApp', type:'invoice' },
  { time:'2025-07-13 10:00', action:'Laporan perbaikan', detail:'REP-001: AC Kamar A202 dalam perbaikan', type:'repair' },
  { time:'2025-07-12 08:30', action:'Order lunas', detail:'ORD-004 oleh Fajar Nugroho', type:'order' },
];

function fmtRupiah(n) { return 'Rp '+n.toLocaleString('id-ID'); }
function statusBadge(s) {
  const map = {
    occupied:'<span class="badge badge-green">Terisi</span>',
    empty:'<span class="badge badge-blue">Kosong</span>',
    cleaning:'<span class="badge badge-amber">Need Cleaning</span>',
    maintenance:'<span class="badge badge-red">Perbaikan</span>',
    paid:'<span class="badge badge-green">Lunas</span>',
    pending:'<span class="badge badge-amber">Belum Bayar</span>',
    ok:'<span class="badge badge-green">Normal</span>',
    repair:'<span class="badge badge-amber">Butuh Perbaikan</span>',
    repairing:'<span class="badge badge-red">Sedang Perbaikan</span>',
    done:'<span class="badge badge-gray">Selesai</span>',
  };
  return map[s] || s;
}

// ─── NAVIGATION ───
const pageTitles = {
  'dashboard':'Dashboard','master-kamar':'Tipe Kamar','master-customer':'Customer',
  'manajemen-kamar':'Manajemen Kamar','order':'Order / Penyewaan','pindah-kamar':'Pindah Kamar',
  'perbaikan':'Perbaikan','fasilitas':'Fasilitas Umum','log':'Log Aktivitas'
};
function navigate(page) {
  document.getElementById('page-title').textContent = pageTitles[page] || page;
  document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
  const btns = document.querySelectorAll('.nav-item');
  btns.forEach(b => { if(b.getAttribute('onclick') && b.getAttribute('onclick').includes(page)) b.classList.add('active'); });
  const renders = { dashboard, 'master-kamar': masterKamar, 'master-customer': masterCustomer,
    'manajemen-kamar': manajemenKamar, order: orderPage, 'pindah-kamar': pindahKamar,
    perbaikan: perbaikanPage, fasilitas: fasilitasPage, log: logPage };
  const fn = renders[page];
  if(fn) document.getElementById('page-content').innerHTML = fn();
  lucide.createIcons();
}
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// ─── MODAL ───
function openModal(html) {
  document.getElementById('modal-container').innerHTML = `<div class="modal-overlay" onclick="if(event.target===this)closeModal()">${html}</div>`;
  lucide.createIcons();
}
function closeModal() { document.getElementById('modal-container').innerHTML = ''; }

// ══════════════════════════════════════════════
// PAGE: DASHBOARD
// ══════════════════════════════════════════════
function dashboard() {
  const occupied = rooms.filter(r=>r.status==='occupied').length;
  const empty = rooms.filter(r=>r.status==='empty').length;
  const cleaning = rooms.filter(r=>r.status==='cleaning').length;
  const maint = rooms.filter(r=>r.status==='maintenance').length;
  const pendingInv = orders.filter(o=>o.status==='pending').reduce((a,o)=>a+o.total,0);
  const totalRev = orders.filter(o=>o.status==='paid').reduce((a,o)=>a+o.total,0);
  const months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul'];
  const revData=[12,18,14,22,19,25,28];
  const maxRev=Math.max(...revData);

  return `
  <div class="section-header">
    <div><h2>Selamat datang, Admin 👋</h2><p>Rabu, 15 Juli 2025 · Data diperbarui tadi</p></div>
    <button class="btn btn-primary" onclick="navigate('order')"><i data-lucide="plus" style="width:14px;height:14px"></i> Buat Order</button>
  </div>

  <!-- ROOM STATS -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="icon-wrap ic-blue"><i data-lucide="door-open" style="width:16px;height:16px"></i></div>
      <div class="label">Kamar Kosong</div>
      <div class="value">${empty}</div>
      <div class="sub">Siap disewa</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-green"><i data-lucide="check-circle" style="width:16px;height:16px"></i></div>
      <div class="label">Kamar Terisi</div>
      <div class="value">${occupied}</div>
      <div class="sub">dari ${rooms.length} kamar</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-amber"><i data-lucide="sparkles" style="width:16px;height:16px"></i></div>
      <div class="label">Need Cleaning</div>
      <div class="value">${cleaning}</div>
      <div class="sub">Perlu dibersihkan</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-red"><i data-lucide="wrench" style="width:16px;height:16px"></i></div>
      <div class="label">Perbaikan</div>
      <div class="value">${maint}</div>
      <div class="sub">Dalam proses</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-green"><i data-lucide="banknote" style="width:16px;height:16px"></i></div>
      <div class="label">Total Pendapatan</div>
      <div class="value" style="font-size:18px">${fmtRupiah(totalRev)}</div>
      <div class="sub">Bulan ini</div>
    </div>
    <div class="stat-card">
      <div class="icon-wrap ic-amber"><i data-lucide="clock" style="width:16px;height:16px"></i></div>
      <div class="label">Invoice Pending</div>
      <div class="value" style="font-size:18px">${fmtRupiah(pendingInv)}</div>
      <div class="sub">${orders.filter(o=>o.status==='pending').length} invoice</div>
    </div>
  </div>

  <div class="two-col" style="margin-bottom:14px">
    <!-- REVENUE CHART -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Pendapatan Penyewaan</span>
        <select style="font-size:12px;padding:4px 8px;border-radius:6px;border:1px solid var(--slate-200)">
          <option>Bulanan</option><option>Harian</option><option>Tahunan</option>
        </select>
      </div>
      <div class="chart-bars">
        ${revData.map((v,i)=>`
          <div class="chart-bar-wrap">
            <div class="chart-bar" style="height:${Math.round(v/maxRev*100)}%" title="${fmtRupiah(v*1000000)}"></div>
            <span class="chart-bar-label">${months[i]}</span>
          </div>`).join('')}
      </div>
      <div style="font-size:11px;color:var(--slate-400);text-align:center;margin-top:6px">Dalam jutaan Rp</div>
    </div>

    <!-- RECENT ORDERS -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Order Terbaru</span>
        <button class="btn btn-secondary btn-sm" onclick="navigate('order')">Lihat Semua</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Customer</th><th>Kamar</th><th>Status</th></tr></thead>
          <tbody>
            ${orders.slice(0,4).map(o=>`<tr>
              <td><div style="font-weight:600">${o.customer}</div><div style="font-size:11px;color:var(--slate-400)">${o.id}</div></td>
              <td>${o.room}</td>
              <td>${statusBadge(o.status)}</td>
            </tr>`).join('')}
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ACTIVITY + ROOM OVERVIEW -->
  <div class="two-col">
    <div class="card">
      <div class="card-header"><span class="card-title">Aktivitas Terkini</span></div>
      <div class="activity-list">
        ${logs.slice(0,5).map(l=>{
          const icons={order:'file-text',room:'door-open',customer:'user',invoice:'send',repair:'wrench'};
          const colors={order:'ic-blue',room:'ic-green',customer:'ic-purple',invoice:'ic-amber',repair:'ic-red'};
          return `<div class="activity-item">
            <div class="act-dot ${colors[l.type]||'ic-gray'}"><i data-lucide="${icons[l.type]||'circle'}" style="width:14px;height:14px"></i></div>
            <div class="act-content">
              <div class="act-title">${l.action}</div>
              <div class="act-meta">${l.detail}</div>
              <div class="act-meta">${l.time}</div>
            </div>
          </div>`;
        }).join('')}
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <span class="card-title">Overview Kamar</span>
        <button class="btn btn-secondary btn-sm" onclick="navigate('manajemen-kamar')">Detail</button>
      </div>
      <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:var(--green-400)"></div>Terisi (${occupied})</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--blue-400)"></div>Kosong (${empty})</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--amber-400)"></div>Cleaning (${cleaning})</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--red-400)"></div>Perbaikan (${maint})</div>
      </div>
      <div class="room-grid">
        ${rooms.map(r=>`
          <div class="room-cell ${r.status}" onclick="showRoomDetail('${r.id}')">
            <div class="room-num">${r.id}</div>
            <div class="room-type">${r.type}</div>
          </div>`).join('')}
      </div>
    </div>
  </div>`;
}

// ══════════════════════════════════════════════
// PAGE: MASTER KAMAR
// ══════════════════════════════════════════════
function masterKamar() {
  return `
  <div class="section-header">
    <div><h2>Tipe Kamar</h2><p>Kelola data master kamar kos</p></div>
    <button class="btn btn-primary" onclick="openAddKamarModal()"><i data-lucide="plus" style="width:14px;height:14px"></i> Tambah Kamar</button>
  </div>
  <div class="card">
    <div class="toolbar">
      <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari kamar..." /></div>
      <select><option>Semua Lantai</option><option>Lantai 1</option><option>Lantai 2</option></select>
      <select><option>Semua Tipe</option><option>Standar</option><option>VIP</option><option>Executive</option></select>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID Kamar</th><th>Tipe</th><th>Lantai</th><th>Tipe Sewa</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          ${rooms.map(r=>`<tr>
            <td><b>${r.id}</b></td>
            <td>${r.type}</td>
            <td>Lantai ${r.floor}</td>
            <td>${r.rent}</td>
            <td>${fmtRupiah(r.price)}</td>
            <td>${statusBadge(r.status)}</td>
            <td>
              <div style="display:flex;gap:6px">
                <button class="btn btn-secondary btn-sm" onclick="showRoomDetail('${r.id}')"><i data-lucide="eye" style="width:12px;height:12px"></i></button>
                <button class="btn btn-secondary btn-sm"><i data-lucide="pencil" style="width:12px;height:12px"></i></button>
                <button class="btn btn-danger btn-sm"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
              </div>
            </td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
    <div class="pagination">
      <span class="info">Menampilkan 1–${rooms.length} dari ${rooms.length} kamar</span>
      <div class="page-btns"><button class="page-btn active">1</button><button class="page-btn">2</button></div>
    </div>
  </div>`;
}

function openAddKamarModal() {
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Kamar</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">ID Kamar</label><input class="form-input" placeholder="A101" /></div>
        <div class="form-group"><label class="form-label">Lantai</label><select class="form-input"><option>1</option><option>2</option><option>3</option></select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tipe Kamar</label><select class="form-input"><option>Standar</option><option>VIP</option><option>Executive</option></select></div>
        <div class="form-group"><label class="form-label">Tipe Sewa</label><select class="form-input"><option>Harian</option><option>Bulanan</option><option>Tahunan</option></select></div>
      </div>
      <div class="form-group"><label class="form-label">Harga Sewa</label><input class="form-input" type="number" placeholder="800000" /></div>
      <div class="form-group"><label class="form-label">Fasilitas Kamar</label><textarea class="form-input" rows="3" placeholder="AC, WiFi, Kamar Mandi Dalam..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal()">Batal</button><button class="btn btn-primary">Simpan</button></div>
  </div>`);
}

function showRoomDetail(id) {
  const r = rooms.find(x=>x.id===id);
  if(!r) return;
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">Detail Kamar ${r.id}</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div>${statusBadge(r.status)}</div>
        <select class="filter-select" style="font-size:12px">
          <option ${r.status==='empty'?'selected':''}>Kosong</option>
          <option ${r.status==='occupied'?'selected':''}>Terisi</option>
          <option ${r.status==='cleaning'?'selected':''}>Need Cleaning</option>
          <option ${r.status==='maintenance'?'selected':''}>Perbaikan</option>
        </select>
      </div>
      <div class="detail-row"><span class="detail-key">ID Kamar</span><span class="detail-val">${r.id}</span></div>
      <div class="detail-row"><span class="detail-key">Lantai</span><span class="detail-val">Lantai ${r.floor}</span></div>
      <div class="detail-row"><span class="detail-key">Tipe</span><span class="detail-val">${r.type}</span></div>
      <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val">${r.rent}</span></div>
      <div class="detail-row"><span class="detail-key">Harga</span><span class="detail-val">${fmtRupiah(r.price)}</span></div>
      <div class="detail-row"><span class="detail-key">Penghuni</span><span class="detail-val">${r.tenant}</span></div>
      <div class="detail-row"><span class="detail-key">Sewa Hingga</span><span class="detail-val">${r.until}</span></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
      <button class="btn btn-primary">Simpan Status</button>
    </div>
  </div>`);
}

// ══════════════════════════════════════════════
// PAGE: MASTER CUSTOMER
// ══════════════════════════════════════════════
function masterCustomer() {
  return `
  <div class="section-header">
    <div><h2>Customer</h2><p>Data penghuni kos</p></div>
    <button class="btn btn-primary" onclick="openAddCustomerModal()"><i data-lucide="user-plus" style="width:14px;height:14px"></i> Tambah Customer</button>
  </div>
  <div class="card">
    <div class="toolbar">
      <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari nama, email, WA..." /></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Nama</th><th>Email / WA</th><th>KTP</th><th>Kamar</th><th>Aksi</th></tr></thead>
        <tbody>
          ${customers.map(c=>`<tr>
            <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--slate-400)">${c.id}</span></td>
            <td><div style="font-weight:600">${c.name}</div></td>
            <td><div style="color:var(--blue-600)">${c.email}</div><div style="font-size:12px;color:var(--slate-400)">${c.wa}</div></td>
            <td><span style="font-family:'DM Mono',monospace;font-size:12px">${c.ktp}</span></td>
            <td><span class="badge badge-green">${c.room}</span></td>
            <td>
              <div style="display:flex;gap:6px">
                <button class="btn btn-secondary btn-sm" onclick="openCustomerDetail('${c.id}')"><i data-lucide="eye" style="width:12px;height:12px"></i></button>
                <button class="btn btn-secondary btn-sm"><i data-lucide="pencil" style="width:12px;height:12px"></i></button>
                <button class="btn btn-danger btn-sm"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
              </div>
            </td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
    <div class="pagination">
      <span class="info">1–${customers.length} dari ${customers.length} customer</span>
      <div class="page-btns"><button class="page-btn active">1</button></div>
    </div>
  </div>`;
}
function openAddCustomerModal() {
  openModal(`<div class="modal modal-lg">
    <div class="modal-header"><span class="modal-title">Tambah Customer</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" placeholder="Nama penghuni" /></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" placeholder="email@domain.com" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">WhatsApp</label><input class="form-input" placeholder="08xxxxxxxxxx" /></div>
        <div class="form-group"><label class="form-label">No. KTP</label><input class="form-input" placeholder="16 digit NIK" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Kontak Darurat 1</label><input class="form-input" placeholder="Nama – 08xxx" /></div>
        <div class="form-group"><label class="form-label">Kontak Darurat 2</label><input class="form-input" placeholder="Nama – 08xxx" /></div>
      </div>
      <div class="form-group"><label class="form-label">Foto KTP</label>
        <div style="border:2px dashed var(--slate-200);border-radius:8px;padding:24px;text-align:center;color:var(--slate-400);font-size:13px">
          <i data-lucide="upload" style="width:20px;height:20px;margin-bottom:6px"></i><br>Klik atau drag file KTP di sini
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal()">Batal</button><button class="btn btn-primary">Simpan Customer</button></div>
  </div>`);
}
function openCustomerDetail(id) {
  const c = customers.find(x=>x.id===id);
  if(!c) return;
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">${c.name}</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="detail-row"><span class="detail-key">ID</span><span class="detail-val">${c.id}</span></div>
      <div class="detail-row"><span class="detail-key">Email</span><span class="detail-val">${c.email}</span></div>
      <div class="detail-row"><span class="detail-key">WhatsApp</span><span class="detail-val">${c.wa}</span></div>
      <div class="detail-row"><span class="detail-key">KTP</span><span class="detail-val" style="font-family:'DM Mono',monospace">${c.ktp}</span></div>
      <div class="detail-row"><span class="detail-key">Kamar</span><span class="detail-val">${c.room}</span></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal()">Tutup</button><button class="btn btn-primary"><i data-lucide="message-circle" style="width:13px;height:13px"></i> WA Customer</button></div>
  </div>`);
}

// ══════════════════════════════════════════════
// PAGE: MANAJEMEN KAMAR
// ══════════════════════════════════════════════
function manajemenKamar() {
  const tabs = ['Overview','List Kamar','Gantt Chart'];
  return `
  <div class="section-header">
    <div><h2>Manajemen Kamar</h2><p>Pantau & kelola semua unit kamar</p></div>
  </div>
  <div class="page-tabs">
    ${tabs.map((t,i)=>`<button class="tab-btn ${i===0?'active':''}" onclick="switchKamarTab(this,'${t}')">${t}</button>`).join('')}
  </div>
  <div id="kamar-tab-content">${kamarOverview()}</div>`;
}
function switchKamarTab(el, tab) {
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  const fn = { 'Overview':kamarOverview, 'List Kamar':kamarList, 'Gantt Chart':kamarGantt };
  document.getElementById('kamar-tab-content').innerHTML = fn[tab]?fn[tab]():'';
  lucide.createIcons();
}
function kamarOverview() {
  return `
  <div class="legend">
    <div class="legend-item"><div class="legend-dot" style="background:var(--green-400)"></div>Terisi</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--blue-400)"></div>Kosong</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--amber-400)"></div>Need Cleaning</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--red-400)"></div>Maintenance</div>
  </div>
  <div class="room-grid">
    ${rooms.map(r=>`<div class="room-cell ${r.status}" onclick="showRoomDetail('${r.id}')">
      <div class="room-num">${r.id}</div>
      <div class="room-type">${r.type}</div>
      <div style="font-size:10px;margin-top:2px;opacity:.7">${r.status==='occupied'?r.tenant.split(' ')[0]:r.status==='empty'?'Kosong':r.status==='cleaning'?'Cleaning':'Perbaikan'}</div>
    </div>`).join('')}
  </div>`;
}
function kamarList() {
  return `
  <div class="toolbar">
    <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari kamar..." /></div>
    <select><option>Semua Status</option><option>Kosong</option><option>Terisi</option><option>Cleaning</option><option>Maintenance</option></select>
    <button class="btn btn-primary btn-sm" onclick="openAddKamarModal()"><i data-lucide="plus" style="width:12px;height:12px"></i> Tambah</button>
  </div>
  <div class="card" style="padding:0">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Kamar</th><th>Tipe</th><th>Penghuni</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>${rooms.map(r=>`<tr>
          <td><b>${r.id}</b><div style="font-size:11px;color:var(--slate-400)">Lantai ${r.floor}</div></td>
          <td>${r.type}</td>
          <td>${r.tenant!=='-'?`<span style="font-weight:500">${r.tenant}</span>`:'-'}</td>
          <td>${fmtRupiah(r.price)}</td>
          <td>${statusBadge(r.status)}</td>
          <td><div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="showRoomDetail('${r.id}')">Edit Status</button>
          </div></td>
        </tr>`).join('')}</tbody>
      </table>
    </div>
  </div>`;
}
function kamarGantt() {
  const days = ['1','3','5','7','9','11','13','15','17','19','21','23','25','27','29','31'];
  const ganttData = [
    { room:'A101', bars:[{s:0,w:100,name:'Andi',color:'#22c55e'}] },
    { room:'A201', bars:[{s:0,w:100,name:'Budi',color:'#22c55e'}] },
    { room:'A203', bars:[{s:0,w:65,name:'Cici',color:'#22c55e'}] },
    { room:'B102', bars:[{s:45,w:16,name:'Dika',color:'#3b82f6'}] },
    { room:'B201', bars:[{s:0,w:100,name:'Eka',color:'#22c55e'}] },
    { room:'C201', bars:[{s:0,w:100,name:'Fajar',color:'#22c55e'}] },
  ];
  return `
  <div class="card">
    <div class="card-header"><span class="card-title">Gantt Chart Penyewaan — Juli 2025</span>
      <div style="display:flex;gap:8px">
        <input type="date" class="filter-select" value="2025-07-01" style="font-size:12px">
        <input type="date" class="filter-select" value="2025-07-31" style="font-size:12px">
      </div>
    </div>
    <div class="gantt-wrap"><div class="gantt">
      <div class="gantt-header">
        <div class="gantt-room-col">Kamar</div>
        <div class="gantt-days">${days.map(d=>`<div class="gantt-day">${d}</div>`).join('')}</div>
      </div>
      ${ganttData.map(g=>`
        <div class="gantt-row">
          <div class="gantt-label">${g.room}</div>
          <div class="gantt-timeline">
            ${g.bars.map(b=>`<div class="gantt-bar" style="left:${b.s}%;width:${b.w}%;background:${b.color}">${b.name}</div>`).join('')}
          </div>
        </div>`).join('')}
    </div></div>
    <div style="display:flex;gap:16px;margin-top:12px;font-size:12px;color:var(--slate-500)">
      <div style="display:flex;align-items:center;gap:6px"><div style="width:12px;height:12px;border-radius:2px;background:#22c55e"></div>Bulanan/Tahunan</div>
      <div style="display:flex;align-items:center;gap:6px"><div style="width:12px;height:12px;border-radius:2px;background:#3b82f6"></div>Harian</div>
    </div>
  </div>`;
}

// ══════════════════════════════════════════════
// PAGE: ORDER
// ══════════════════════════════════════════════
function orderPage() {
  return `
  <div class="section-header">
    <div><h2>Order / Penyewaan</h2><p>Kelola transaksi sewa kamar</p></div>
    <button class="btn btn-primary" onclick="openOrderModal()"><i data-lucide="plus" style="width:14px;height:14px"></i> Buat Order</button>
  </div>
  <div class="card">
    <div class="toolbar">
      <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari order, customer..." /></div>
      <select><option>Semua Status</option><option>Lunas</option><option>Belum Bayar</option></select>
      <select><option>Semua Tipe</option><option>Harian</option><option>Bulanan</option><option>Tahunan</option></select>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID Order</th><th>Customer</th><th>Kamar</th><th>Periode</th><th>Tipe</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>${orders.map(o=>`<tr>
          <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--blue-600)">${o.id}</span></td>
          <td><div style="font-weight:600">${o.customer}</div></td>
          <td><b>${o.room}</b></td>
          <td><div style="font-size:12px">${o.start}</div><div style="font-size:12px;color:var(--slate-400)">s/d ${o.end}</div></td>
          <td>${o.type}</td>
          <td style="font-weight:600">${fmtRupiah(o.total)}</td>
          <td>${statusBadge(o.status)}</td>
          <td><div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="openOrderDetail('${o.id}')"><i data-lucide="eye" style="width:12px;height:12px"></i></button>
            ${o.status==='pending'?`<button class="btn btn-primary btn-sm"><i data-lucide="send" style="width:12px;height:12px"></i> Invoice</button>`:''}
          </div></td>
        </tr>`).join('')}</tbody>
      </table>
    </div>
    <div class="pagination">
      <span class="info">1–${orders.length} dari ${orders.length} order</span>
      <div class="page-btns"><button class="page-btn active">1</button></div>
    </div>
  </div>`;
}

function openOrderModal() {
  const emptyRooms = rooms.filter(r=>r.status==='empty');
  openModal(`<div class="modal modal-lg">
    <div class="modal-header"><span class="modal-title">Buat Order Baru</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div style="background:var(--slate-50);border-radius:8px;padding:12px;margin-bottom:16px;font-size:12px;color:var(--slate-500)">
        <i data-lucide="info" style="width:13px;height:13px;display:inline;vertical-align:middle"></i> Pilih customer dari master data atau input manual
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Customer</label>
          <select class="form-input"><option>-- Pilih dari Master --</option>${customers.map(c=>`<option>${c.name}</option>`).join('')}</select>
        </div>
        <div class="form-group"><label class="form-label">Kamar</label>
          <select class="form-input"><option>-- Pilih Kamar --</option>${emptyRooms.map(r=>`<option value="${r.id}">${r.id} - ${r.type} (${fmtRupiah(r.price)})</option>`).join('')}</select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tipe Sewa</label>
          <select class="form-input" onchange="updateOrderSummary(this)"><option>Harian</option><option selected>Bulanan</option><option>Tahunan</option></select>
        </div>
        <div class="form-group"><label class="form-label">Mulai Sewa</label><input type="date" class="form-input" value="2025-07-15" /></div>
      </div>
      <div class="form-group"><label class="form-label">Akhir Sewa (otomatis / manual)</label><input type="date" class="form-input" value="2025-08-14" /></div>
      <div style="background:var(--blue-50);border:1px solid var(--blue-200);border-radius:8px;padding:14px;margin-top:8px" id="order-summary">
        <div style="font-size:12px;font-weight:600;color:var(--blue-700);margin-bottom:8px">RINGKASAN ORDER</div>
        <div class="detail-row"><span class="detail-key">Durasi</span><span class="detail-val">30 hari</span></div>
        <div class="detail-row"><span class="detail-key">Harga/bulan</span><span class="detail-val">Rp 800.000</span></div>
        <div class="detail-row" style="border-bottom:none"><span class="detail-key">Total</span><span class="detail-val" style="color:var(--blue-700);font-size:16px">Rp 800.000</span></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
      <button class="btn btn-secondary"><i data-lucide="download" style="width:13px;height:13px"></i> Preview Invoice</button>
      <button class="btn btn-primary"><i data-lucide="send" style="width:13px;height:13px"></i> Kirim Invoice WA</button>
    </div>
  </div>`);
}
function openOrderDetail(id) {
  const o = orders.find(x=>x.id===id);
  if(!o) return;
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">Detail Order ${o.id}</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div style="margin-bottom:14px">${statusBadge(o.status)}</div>
      <div class="detail-row"><span class="detail-key">Customer</span><span class="detail-val">${o.customer}</span></div>
      <div class="detail-row"><span class="detail-key">Kamar</span><span class="detail-val">${o.room}</span></div>
      <div class="detail-row"><span class="detail-key">Tipe Sewa</span><span class="detail-val">${o.type}</span></div>
      <div class="detail-row"><span class="detail-key">Mulai</span><span class="detail-val">${o.start}</span></div>
      <div class="detail-row"><span class="detail-key">Selesai</span><span class="detail-val">${o.end}</span></div>
      <div class="detail-row"><span class="detail-key">Total</span><span class="detail-val" style="color:var(--blue-600)">${fmtRupiah(o.total)}</span></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
      ${o.status==='pending'?`<button class="btn btn-primary"><i data-lucide="send" style="width:13px;height:13px"></i> Kirim Invoice</button>`:''}
    </div>
  </div>`);
}

// ══════════════════════════════════════════════
// PAGE: PINDAH KAMAR
// ══════════════════════════════════════════════
function pindahKamar() {
  const occupied = rooms.filter(r=>r.status==='occupied');
  const empty = rooms.filter(r=>r.status==='empty');
  return `
  <div class="section-header">
    <div><h2>Pindah Kamar</h2><p>Pindahkan penghuni antar kamar</p></div>
  </div>
  <div class="two-col">
    <div class="card">
      <div class="card-title" style="margin-bottom:14px">Kamar Terisi</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        ${occupied.map(r=>`<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid var(--slate-200);border-radius:8px;cursor:pointer" onclick="openPindahModal('${r.id}')">
          <div>
            <div style="font-weight:600">${r.id}</div>
            <div style="font-size:12px;color:var(--slate-400)">${r.tenant}</div>
          </div>
          <button class="btn btn-primary btn-sm">Pindah <i data-lucide="arrow-right" style="width:12px;height:12px"></i></button>
        </div>`).join('')}
      </div>
    </div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px">Kamar Tersedia (${empty.length})</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        ${empty.map(r=>`<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--green-50);border:1px solid var(--green-200);border-radius:8px">
          <div>
            <div style="font-weight:600;color:var(--green-700)">${r.id}</div>
            <div style="font-size:12px;color:var(--green-600)">${r.type} · ${fmtRupiah(r.price)}</div>
          </div>
          <span class="badge badge-green">Kosong</span>
        </div>`).join('')}
      </div>
    </div>
  </div>`;
}
function openPindahModal(fromId) {
  const r = rooms.find(x=>x.id===fromId);
  const empty = rooms.filter(r=>r.status==='empty');
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">Form Pindah Kamar</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Penghuni</label><input class="form-input" value="${r.tenant}" readonly style="background:var(--slate-50)" /></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Kamar Asal</label><input class="form-input" value="${r.id}" readonly style="background:var(--slate-50)" /></div>
        <div class="form-group"><label class="form-label">Kamar Tujuan</label>
          <select class="form-input"><option>-- Pilih Kamar Kosong --</option>${empty.map(e=>`<option>${e.id} – ${e.type} (${fmtRupiah(e.price)})</option>`).join('')}</select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Mulai Periode Baru</label><input type="date" class="form-input" /></div>
        <div class="form-group"><label class="form-label">Akhir Periode</label><input type="date" class="form-input" /></div>
      </div>
      <div class="form-group"><label class="form-label">Catatan</label><textarea class="form-input" rows="2" placeholder="Alasan pindah..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal()">Batal</button><button class="btn btn-primary">Konfirmasi Pindah</button></div>
  </div>`);
}

// ══════════════════════════════════════════════
// PAGE: PERBAIKAN
// ══════════════════════════════════════════════
function perbaikanPage() {
  return `
  <div class="section-header">
    <div><h2>Perbaikan</h2><p>Monitor kerusakan kamar & fasilitas umum</p></div>
    <button class="btn btn-primary" onclick="openRepairModal()"><i data-lucide="plus" style="width:14px;height:14px"></i> Laporkan Kerusakan</button>
  </div>
  <div class="page-tabs">
    <button class="tab-btn active">Kamar</button>
    <button class="tab-btn">Fasilitas Umum</button>
  </div>
  <div class="card">
    <div class="toolbar">
      <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari laporan..." /></div>
      <select><option>Semua Status</option><option>Pending</option><option>Sedang Perbaikan</option><option>Selesai</option></select>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Target</th><th>Masalah</th><th>Dilaporkan</th><th>Teknisi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>${repairs.map(r=>`<tr>
          <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--slate-400)">${r.id}</span></td>
          <td><b>${r.target}</b></td>
          <td>${r.issue}</td>
          <td style="font-size:12px;color:var(--slate-400)">${r.reported}</td>
          <td>${r.tech}</td>
          <td>${statusBadge(r.status)}</td>
          <td><button class="btn btn-secondary btn-sm">Update</button></td>
        </tr>`).join('')}</tbody>
      </table>
    </div>
  </div>`;
}
function openRepairModal() {
  openModal(`<div class="modal">
    <div class="modal-header"><span class="modal-title">Laporkan Kerusakan</span><button class="modal-close" onclick="closeModal()"><i data-lucide="x" style="width:14px;height:14px"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Jenis</label>
        <select class="form-input"><option>Kamar</option><option>Fasilitas Umum</option></select>
      </div>
      <div class="form-group"><label class="form-label">Kamar / Fasilitas</label>
        <select class="form-input"><option>-- Pilih --</option>${rooms.map(r=>`<option>${r.id}</option>`).join('')}</select>
      </div>
      <div class="form-group"><label class="form-label">Deskripsi Masalah</label><textarea class="form-input" rows="3" placeholder="Jelaskan kerusakan..."></textarea></div>
      <div class="form-group"><label class="form-label">Teknisi</label><input class="form-input" placeholder="Nama teknisi (opsional)" /></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal()">Batal</button><button class="btn btn-primary">Simpan Laporan</button></div>
  </div>`);
}

// ══════════════════════════════════════════════
// PAGE: FASILITAS
// ══════════════════════════════════════════════
function fasilitasPage() {
  return `
  <div class="section-header">
    <div><h2>Fasilitas Umum</h2><p>Kelola fasilitas bersama di kos</p></div>
    <button class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Tambah Fasilitas</button>
  </div>
  <div class="three-col" style="margin-bottom:14px">
    ${facilities.map(f=>`
    <div class="card" style="position:relative">
      <div style="position:absolute;top:14px;right:14px">${statusBadge(f.status)}</div>
      <div style="width:40px;height:40px;border-radius:10px;background:var(--blue-50);display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:var(--blue-600)">
        <i data-lucide="building-2" style="width:18px;height:18px"></i>
      </div>
      <div style="font-weight:700;font-size:15px;margin-bottom:4px">${f.name}</div>
      <div style="font-size:12px;color:var(--slate-400);margin-bottom:8px">Lantai ${f.floor} · ${f.id}</div>
      <div style="font-size:13px;color:var(--slate-600);margin-bottom:14px">${f.desc}</div>
      <div style="display:flex;gap:6px">
        <button class="btn btn-secondary btn-sm"><i data-lucide="pencil" style="width:12px;height:12px"></i></button>
        ${f.status!=='ok'?`<button class="btn btn-primary btn-sm">Update Status</button>`:`<button class="btn btn-secondary btn-sm">Detail</button>`}
      </div>
    </div>`).join('')}
  </div>`;
}

// ══════════════════════════════════════════════
// PAGE: LOG
// ══════════════════════════════════════════════
function logPage() {
  const icons={order:'file-text',room:'door-open',customer:'user',invoice:'send',repair:'wrench'};
  const colors={order:'ic-blue',room:'ic-green',customer:'ic-purple',invoice:'ic-amber',repair:'ic-red'};
  return `
  <div class="section-header">
    <div><h2>Log Aktivitas</h2><p>Riwayat semua aksi admin</p></div>
  </div>
  <div class="card">
    <div class="toolbar">
      <div class="search-wrap"><i data-lucide="search" class="search-icon" style="width:14px;height:14px"></i><input placeholder="Cari aktivitas..." /></div>
      <input type="date" class="filter-select" value="2025-07-01">
      <input type="date" class="filter-select" value="2025-07-15">
      <select><option>Semua Tipe</option><option>Order</option><option>Kamar</option><option>Customer</option><option>Invoice</option><option>Perbaikan</option></select>
    </div>
    <div class="activity-list">
      ${logs.map(l=>`<div class="activity-item">
        <div class="act-dot ${colors[l.type]||'ic-gray'}"><i data-lucide="${icons[l.type]||'circle'}" style="width:14px;height:14px"></i></div>
        <div class="act-content" style="flex:1">
          <div class="act-title">${l.action}</div>
          <div class="act-meta">${l.detail}</div>
        </div>
        <div style="font-size:11px;color:var(--slate-400);white-space:nowrap;padding-left:12px">${l.time}</div>
      </div>`).join('')}
    </div>
    <div class="pagination" style="margin-top:12px">
      <span class="info">Menampilkan ${logs.length} aktivitas</span>
      <div class="page-btns"><button class="page-btn active">1</button><button class="page-btn">2</button></div>
    </div>
  </div>`;
}

// ─── INIT ───
document.addEventListener('DOMContentLoaded', () => {
  navigate('dashboard');
});
