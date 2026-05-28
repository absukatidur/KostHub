<?php 
  include 'includes/db.php';
  $basePath = '../';

  $price1 = mysqli_fetch_assoc(
    $db->query("SELECT price FROM rooms WHERE type = 'Standar'")
  );

  $price2 = mysqli_fetch_assoc(
    $db->query("SELECT price FROM rooms WHERE type = 'VIP'")
  );

  $price3 = mysqli_fetch_assoc(
    $db->query("SELECT price FROM rooms WHERE type = 'Executive'")
  );

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="KostHub — Temukan dan pesan kamar kos impian Anda. Sistem manajemen kos modern dengan booking real-time.">
  <title>KostHub — Cari & Booking Kos Bulanan Praktis</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

  <!-- NAV -->
  <nav class="landing-nav">
    <div class="container nav-container">
      <div class="nav-logo">
        <h1>Kost<span>Hub</span></h1>
      </div>
      <div class="nav-links">
        <a href="#features">Fitur</a>
        <a href="#rooms">Pilihan Kamar</a>
        <a href="#faq">FAQ</a>
        <a href="login.php" class="nav-login-btn">Masuk</a>
        <a href="register.php" class="nav-cta">Daftar Sekarang</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="container hero-container">
      <div class="hero-left">
        <div class="hero-badge">
          <span class="badge-line"></span>
          <span>Platform Booking Kos</span>
        </div>
        <h1>Temukan <span class="grad">Kos Impian</span> Anda Sekarang.</h1>
        <p>Cari, bandingkan, dan pesan kamar kos secara langsung. Tanpa perantara, tanpa ribet — cepat, transparan, dan terpercaya.</p>
        <div class="hero-buttons">
          <a href="register.php" class="btn-cari-kamar">Cari Kamar</a>
          <a href="#rooms" class="btn-hero-secondary">
            <span class="play-icon"><i class="bi bi-door-open"></i></span>
            Lihat Kamar
          </a>
        </div>
      </div>
      <div class="hero-right">
        <img src="assets/img/Landing.png" alt="Interior Kos Premium" class="hero-img">
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="section" id="features">
    <div class="section-center">
      <div class="section-header">
        <div class="section-label"><i class="bi bi-lightning-charge"></i> Layanan Kami</div>
        <h2 class="section-title">Semua Fitur Utama untuk Anda</h2>
        <p class="section-desc">Nikmati kemudahan mengelola sewa kos dengan sistem digital yang transparan dan praktis.</p>
      </div>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="fc-icon-container blue">
            <i class="bi bi-search"></i>
          </div>
          <h3>Pencarian Real-time</h3>
          <p>Cari unit kamar kos yang tersedia lengkap dengan foto asli, rincian harga sewa, dan kelengkapan fasilitas kamar.</p>
        </div>
        <div class="feature-card">
          <div class="fc-icon-container green">
            <i class="bi bi-credit-card"></i>
          </div>
          <h3>Pembayaran Mudah</h3>
          <p>Lakukan pembayaran tagihan sewa bulanan secara aman melalui Virtual Account atau dompet digital favorit Anda.</p>
        </div>
        <div class="feature-card">
          <div class="fc-icon-container amber">
            <i class="bi bi-wrench"></i>
          </div>
          <h3>Laporan Kerusakan</h3>
          <p>Ajukan permohonan perbaikan fasilitas kamar secara online dan pantau langsung penanganannya oleh staf kami.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ROOMS PREVIEW -->
  <section class="section rooms-section" id="rooms">
    <div class="section-center">
      <div class="section-header">
        <div class="section-label"><i class="bi bi-door-open"></i> Pilihan Hunian</div>
        <h2 class="section-title">Kamar Siap Huni</h2>
        <p class="section-desc">Pilih tipe kamar yang paling sesuai dengan kebutuhan harian dan budget Anda.</p>
      </div>

      <div class="rooms-grid">
        <!-- Standar -->
        <div class="room-preview-card">
          <div class="rpc-image-container">
            <img src="assets/img/KamarKost1 (Standar).jpg" alt="Kamar Standar" class="rpc-image">
          </div>
          <div class="rpc-body">
            <div class="rpc-type">Tipe Standar</div>
            <h3 class="rpc-name">Kamar Standar Cozy</h3>
            <div class="rpc-facs">
              <span>AC</span>
              <span>WiFi</span>
              <span>KM Luar</span>
            </div>
            <div class="rpc-price-row">
              <span class="rpc-price"><?= fmtRupiah($price1['price']); ?><span class="rpc-rent">/bln</span></span>
              <a href="register.php" class="rpc-action-btn">Pesan Kamar</a>
            </div>
          </div>
        </div>
        
        <!-- VIP -->
        <div class="room-preview-card">
          <div class="rpc-image-container">
            <img src="assets/img/KamarKost4(VIP).jpg" alt="Kamar VIP" class="rpc-image">
          </div>
          <div class="rpc-body">
            <div class="rpc-type">Tipe VIP</div>
            <h3 class="rpc-name">Kamar VIP Premium</h3>
            <div class="rpc-facs">
              <span>AC</span>
              <span>WiFi</span>
              <span>Smart TV</span>
              <span>KM Dalam</span>
            </div>
            <div class="rpc-price-row">
              <span class="rpc-price"><?= fmtRupiah($price2['price']); ?> <span class="rpc-rent">/bln</span></span>
              <a href="register.php" class="rpc-action-btn">Pesan Kamar</a>
            </div>
          </div>
        </div>
        
        <!-- Executive -->
        <div class="room-preview-card">
          <div class="rpc-image-container">
            <img src="assets/img/KamarKost6 (Executive).jpg" alt="Kamar Executive" class="rpc-image">
          </div>
          <div class="rpc-body">
            <div class="rpc-type">Tipe Executive</div>
            <h3 class="rpc-name">Kamar Executive Suite</h3>
            <div class="rpc-facs">
              <span>AC</span>
              <span>WiFi</span>
              <span>Smart TV</span>
              <span>KM Dalam</span>
              <span>Mini Bar</span>
            </div>
            <div class="rpc-price-row">
              <span class="rpc-price"><?= fmtRupiah($price3['price']); ?> <span class="rpc-rent">/bln</span></span>
              <a href="register.php" class="rpc-action-btn">Pesan Kamar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section faq-section" id="faq">
    <div class="section-center">
      <div class="section-header">
        <div class="section-label"><i class="bi bi-question-circle"></i> Tanya Jawab</div>
        <h2 class="section-title">Pertanyaan Umum</h2>
        <p class="section-desc">Butuh informasi lebih cepat? Silakan lihat rangkuman jawaban kami berikut ini.</p>
      </div>
      
      <div class="faq-container">
        <details class="faq-item" open>
          <summary class="faq-question">Bagaimana cara memesan kamar di KostHub? <i class="bi bi-chevron-down"></i></summary>
          <div class="faq-answer">
            <p>Anda cukup membuat akun melalui halaman pendaftaran, pilih kamar yang Anda inginkan pada dashboard atau halaman utama, lakukan pembayaran booking fee, dan kamar akan langsung tereservasi secara otomatis untuk Anda.</p>
          </div>
        </details>
        
        <details class="faq-item">
          <summary class="faq-question">Apakah harga sewa bulanan sudah termasuk listrik? <i class="bi bi-chevron-down"></i></summary>
          <div class="faq-answer">
            <p>Untuk tipe kamar VIP dan Executive, harga sewa bulanan sudah termasuk seluruh biaya pemakaian listrik AC dan alat elektronik dasar. Sedangkan untuk tipe Standar, menggunakan token listrik mandiri per kamar yang diisi oleh masing-masing penghuni.</p>
          </div>
        </details>
        
        <details class="faq-item">
          <summary class="faq-question">Bagaimana jika terjadi kerusakan fasilitas di dalam kamar? <i class="bi bi-chevron-down"></i></summary>
          <div class="faq-answer">
            <p>Penghuni dapat menggunakan menu "Lapor Kerusakan" di dashboard pengguna. Masukkan keterangan fasilitas yang rusak dan unggah fotonya. Staf maintenance kami akan menjadwalkan pemeriksaan dan perbaikan tanpa biaya tambahan.</p>
          </div>
        </details>
        
        <details class="faq-item">
          <summary class="faq-question">Apakah ada biaya tambahan pendaftaran saat membuat akun? <i class="bi bi-chevron-down"></i></summary>
          <div class="faq-answer">
            <p>Pendaftaran akun di KostHub gratis dan bebas biaya admin bulanan. Anda hanya perlu membayar tagihan sewa kamar sesuai kamar yang Anda pesan.</p>
          </div>
        </details>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="cta-card">
      <h2>Mulai Langkah Baru Anda</h2>
      <p>Bergabunglah dengan KostHub sekarang. Pendaftaran 100% gratis, mudah, dan seluruh proses dapat dilakukan secara online.</p>
      <div class="cta-buttons">
        <a href="register.php" class="btn-hero primary">Daftar Sekarang</a>
        <a href="login.php" class="btn-hero secondary">Masuk Ke Akun</a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="landing-footer">
    <div class="footer-content">
      <p>&copy; 2026 KostHub. Hak Cipta Dilindungi Undang-Undang.</p>
    </div>
  </footer>

  <!-- <script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href.startsWith('#')) {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    });
  </script> -->
</body>
</html>
