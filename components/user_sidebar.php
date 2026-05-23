<!-- ══════════════════════════ USER SIDEBAR ══════════════════════════ -->
<nav id="sidebar">
  <div class="sidebar-logo">
    <h1>Kost<span>Hub</span></h1>
    <p id="user-subtitle">Portal Penghuni</p>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-label">Menu</div>
      <button class="nav-item active" onclick="navigate('dashboard')">
        <i class="bi bi-speedometer2"></i> Dashboard
      </button>
      <button class="nav-item" onclick="navigate('tagihan')">
        <i class="bi bi-receipt"></i> Tagihan <span class="badge" id="tagihan-badge"></span>
      </button>
      <button class="nav-item" onclick="navigate('perbaikan')">
        <i class="bi bi-wrench"></i> Perbaikan
      </button>
      <button class="nav-item" onclick="navigate('fasilitas')">
        <i class="bi bi-buildings"></i> Fasilitas
      </button>
      <button class="nav-item" onclick="navigate('cari-kamar')">
        <i class="bi bi-search"></i> Cari Kamar
      </button>
    </div>
    <div class="nav-section">
      <div class="nav-label">Akun</div>
      <button class="nav-item" onclick="navigate('profil')">
        <i class="bi bi-person"></i> Profil
      </button>
      <button class="nav-item" onclick="navigate('layanan')">
        <i class="bi bi-file-text"></i> Layanan
      </button>
    </div>
  </div>
  <div class="sidebar-footer">
    <button class="nav-item" style="color:#ef4444" onclick="doLogout()">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </button>
  </div>
</nav>
