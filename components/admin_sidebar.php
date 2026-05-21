<nav id="sidebar">
  <div class="sidebar-logo">
    <h1>Kos<span>Manager</span></h1>
    <p>v1.0.0 · admin</p>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-label">Utama</div>
      <button class="nav-item active" onclick="navigate('dashboard')">
        <i data-lucide="layout-dashboard"></i> Dashboard
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Master Data</div>
      <button class="nav-item" onclick="navigate('master-kamar')">
        <i data-lucide="door-open"></i> Tipe Kamar
      </button>
      <button class="nav-item" onclick="navigate('master-customer')">
        <i data-lucide="users"></i> Customer
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Operasional</div>
      <button class="nav-item" onclick="navigate('manajemen-kamar')">
        <i data-lucide="grid-2x2"></i> Manajemen Kamar
      </button>
      <button class="nav-item" onclick="navigate('order')">
        <i data-lucide="file-text"></i> Order / Sewa <span class="badge" id="order-badge"></span>
      </button>
      <button class="nav-item" onclick="navigate('pindah-kamar')">
        <i data-lucide="arrow-right-left"></i> Pindah Kamar
      </button>
      <button class="nav-item" onclick="navigate('perbaikan')">
        <i data-lucide="wrench"></i> Perbaikan <span class="badge" id="repair-badge"></span>
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Lainnya</div>
      <button class="nav-item" onclick="navigate('fasilitas')">
        <i data-lucide="building-2"></i> Fasilitas Umum
      </button>
      <button class="nav-item" onclick="navigate('log')">
        <i data-lucide="scroll-text"></i> Log Aktivitas
      </button>
      <button class="nav-item" onclick="navigate('permintaan')">
        <i data-lucide="inbox"></i> Permintaan User <span class="badge" id="req-badge"></span>
      </button>
    </div>
  </div>
  <div class="sidebar-footer">
    <button class="nav-item" style="color:var(--slate-500)" onclick="openSettingsModal()">
      <i data-lucide="settings"></i> Pengaturan
    </button>
    <button class="nav-item" style="color:var(--slate-500)" onclick="resetAllData()">
      <i data-lucide="rotate-ccw"></i> Reset Data
    </button>
    <button class="nav-item" style="color:#ef4444" onclick="doLogout()">
      <i data-lucide="log-out"></i> Keluar
    </button>
  </div>
</nav>