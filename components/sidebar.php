<nav id="sidebar">
  <div class="sidebar-logo">
    <h1>Kost<span>Hub</span></h1>
    <p>v1.0.0 · admin</p>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-label">Utama</div>
      <button class="nav-item active" onclick="navigate('dashboard')">
        <i class="bi bi-speedometer2"></i> Dashboard
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Master Data</div>
      <button class="nav-item" onclick="navigate('master-kamar')">
        <i class="bi bi-door-open"></i> Tipe Kamar
      </button>
      <button class="nav-item" onclick="navigate('master-customer')">
        <i class="bi bi-people"></i> Customer
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Operasional</div>
      <button class="nav-item" onclick="navigate('manajemen-kamar')">
        <i class="bi bi-grid"></i> Manajemen Kamar
      </button>
      <button class="nav-item" onclick="navigate('order')">
        <i class="bi bi-file-text"></i> Order / Sewa <span class="badge" id="order-badge"></span>
      </button>
      <button class="nav-item" onclick="navigate('pindah-kamar')">
        <i class="bi bi-arrow-left-right"></i> Pindah Kamar
      </button>
      <button class="nav-item" onclick="navigate('perbaikan')">
        <i class="bi bi-wrench"></i> Perbaikan <span class="badge" id="repair-badge"></span>
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Lainnya</div>
      <button class="nav-item" onclick="navigate('fasilitas')">
        <i class="bi bi-buildings"></i> Fasilitas Umum
      </button>
      <button class="nav-item" onclick="navigate('log')">
        <i class="bi bi-card-text"></i> Log Aktivitas
      </button>
      <button class="nav-item" onclick="navigate('permintaan')">
        <i class="bi bi-inbox"></i> Permintaan User <span class="badge" id="req-badge"></span>
      </button>
    </div>
  </div>
  <div class="sidebar-footer">
    <button class="nav-item" style="color:var(--slate-base)" onclick="openSettingsModal()">
      <i class="bi bi-gear"></i> Pengaturan
    </button>
    <button class="nav-item" style="color:var(--slate-base)" onclick="resetAllData()">
      <i class="bi bi-arrow-counterclockwise"></i> Reset Data
    </button>
    <button class="nav-item" style="color:#ef4444" onclick="doLogout()">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </button>
  </div>
</nav>