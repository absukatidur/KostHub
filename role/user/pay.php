<?php
$basePath = '../';
require_once '../components/header.php';
require_once '../components/user_sidebar.php';
require_once '../components/user_topbar.php';
?>

<div style="max-width: 650px; margin: 0 auto;">
  <div class="section-header">
    <div>
      <h2>Pembayaran</h2>
      <p>Pilih metode pembayaran untuk menyelesaikan tagihan</p>
    </div>
    <a href="tagihan.php" class="btn btn-secondary" style="text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; font-weight: 500; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="card" style="margin-bottom: 20px; background: var(--bg-card); border: 1px solid var(--border-dim); border-radius: 12px; padding: 20px; text-align: center;">
    <div style="font-size: 11px; font-weight: 600; color: var(--slate-muted); letter-spacing: 0.05em; text-transform: uppercase;">TOTAL PEMBAYARAN</div>
    <div style="font-size: 28px; font-weight: 700; color: var(--brand-accent); margin: 8px 0;"><?= fmtRupiah($order['total']) ?></div>
    <div style="font-size: 12.5px; color: var(--slate-mid);">Order: <?= htmlspecialchars($order['id']) ?> · Kamar <?= htmlspecialchars($order['room']) ?> · <?= htmlspecialchars($order['type']) ?></div>
  </div>

  <form method="POST" autocomplete="off">
    <input type="hidden" id="selected-method" name="method" value="" required />
    
    <div class="card" style="margin-bottom: 20px;">
      <div style="margin-bottom: 16px;"><h3 style="margin:0; font-size:15px; color:var(--slate-white)">Pilih Metode Pembayaran</h3></div>
      
      <?php foreach ($payMethods as $group => $items): ?>
        <div style="margin-bottom: 20px;">
          <div style="font-size: 11px; font-weight: 600; color: var(--slate-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;"><?= $group ?></div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <?php foreach ($items as $m): ?>
              <button type="button" class="pay-method-btn" onclick="selectPaymentMethod(this, '<?= $m['id'] ?>', '<?= htmlspecialchars($m['logo']) ?>')" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-dim); border-radius: 8px; background: var(--bg-card); cursor: pointer; text-align: left; width: 100%; transition: all 0.15s; outline: none;">
                <div style="width: 40px; height: 40px; border-radius: 6px; background: <?= $m['color'] ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13.5px; flex-shrink: 0;">
                  <?= $m['logo'] ?>
                </div>
                <div style="flex: 1">
                  <div style="font-size: 13.5px; font-weight: 600; color: var(--slate-bright);"><?= htmlspecialchars($m['id']) ?></div>
                  <div style="font-size: 11px; color: var(--slate-muted);"><?= $group === 'e-Wallet' ? 'Saldo e-Wallet' : 'Virtual Account' ?></div>
                </div>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Instruction / Details Card -->
    <div id="payment-details-card" class="card" style="display: none; margin-bottom: 20px; border: 1px solid var(--border-dim);">
      <div style="margin-bottom:12px"><h3 id="details-title" style="margin:0; font-size:15px; color:var(--slate-white)">Detail Pembayaran</h3></div>
      <div id="details-body" style="padding: 12px; background: var(--slate-faint); border-radius: 8px; text-align: center;">
        <!-- Filled dynamically by JS -->
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px;">
      <a href="tagihan.php" class="btn btn-secondary" style="text-decoration:none">Batal</a>
      <button type="submit" id="submit-pay-btn" class="btn btn-primary" style="display: none;">
        <i class="bi bi-check-lg" style="font-size: 13px;"></i> Konfirmasi Pembayaran
      </button>
    </div>
  </form>
</div>

<style>
.pay-method-btn.selected {
  border-color: var(--brand-accent) !important;
  background: var(--slate-thin) !important;
}
</style>

<script>
function selectPaymentMethod(element, methodId, logoText) {
  // Clear previous selections
  document.querySelectorAll('.pay-method-btn').forEach(btn => btn.classList.remove('selected'));
  
  // Mark current selected
  element.classList.add('selected');
  document.getElementById('selected-method').value = methodId;

  // Show submit button
  document.getElementById('submit-pay-btn').style.display = 'inline-flex';

  // Generate simulated details
  const detailsCard = document.getElementById('payment-details-card');
  const detailsTitle = document.getElementById('details-title');
  const detailsBody = document.getElementById('details-body');
  
  detailsCard.style.display = 'block';
  
  const isVA = logoText !== 'GOP' && logoText !== 'OVO' && logoText !== 'SPP' && logoText !== 'DAN';
  
  if (isVA) {
    detailsTitle.textContent = `Nomor Virtual Account ${methodId}`;
    const vaNum = '8800' + Math.floor(1000000000 + Math.random() * 9000000000);
    const formattedVA = vaNum.match(/.{1,4}/g).join(' ');
    detailsBody.innerHTML = `
      <div style="font-size: 20px; font-weight: 700; color: var(--brand-accent); letter-spacing: 0.1em; margin: 10px 0; font-family: 'DM Mono', monospace;">${formattedVA}</div>
      <div style="font-size: 12.5px; color: var(--slate-muted);">Transfer nominal tepat sebesar <b><?= fmtRupiah($order['total']) ?></b> ke nomor VA di atas sebelum masa tagihan berakhir.</div>
    `;
  } else {
    detailsTitle.textContent = `Pembayaran via ${methodId}`;
    detailsBody.innerHTML = `
      <div style="font-size: 14.5px; color: var(--slate-bright); margin: 8px 0;">Konfirmasi akan dialihkan ke sistem aplikasi ${methodId} di HP Anda.</div>
      <div style="font-size: 12.5px; color: var(--slate-muted);">Pastikan saldo ${methodId} Anda mencukupi nominal <b><?= fmtRupiah($order['total']) ?></b>.</div>
    `;
  }
}
</script>

<?php require_once '../components/user_footer_scripts.php'; ?>
