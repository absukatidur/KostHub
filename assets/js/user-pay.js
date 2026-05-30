// user/pay.php — Payment method selection and detail display
function selectPaymentMethod(element, methodId, logoText) {
  // Clear previous selections
  document.querySelectorAll('.pay-method-btn').forEach(function (btn) {
    btn.classList.remove('selected');
  });

  // Mark current selected
  element.classList.add('selected');
  document.getElementById('selected-method').value = methodId;

  // Show submit button
  document.getElementById('submit-pay-btn').style.display = 'inline-flex';

  // Generate simulated details
  var detailsCard = document.getElementById('payment-details-card');
  var detailsTitle = document.getElementById('details-title');
  var detailsBody = document.getElementById('details-body');

  // Read formatted total from data attribute on the form container
  var totalFormatted = document.getElementById('pay-page-data').getAttribute('data-total-formatted');

  detailsCard.style.display = 'block';

  var isVA = logoText !== 'GOP' && logoText !== 'OVO' && logoText !== 'SPP' && logoText !== 'DAN';

  if (isVA) {
    detailsTitle.textContent = 'Nomor Virtual Account ' + methodId;
    var vaNum = '8800' + Math.floor(1000000000 + Math.random() * 9000000000);
    var formattedVA = vaNum.match(/.{1,4}/g).join(' ');
    detailsBody.innerHTML =
      '<div style="font-size: 20px; font-weight: 700; color: var(--brand-accent); letter-spacing: 0.1em; margin: 10px 0; font-family: \'DM Mono\', monospace;">' + formattedVA + '</div>' +
      '<div style="font-size: 12.5px; color: var(--slate-muted);">Transfer nominal tepat sebesar <b>' + totalFormatted + '</b> ke nomor VA di atas sebelum masa tagihan berakhir.</div>';
  } else {
    detailsTitle.textContent = 'Pembayaran via ' + methodId;
    detailsBody.innerHTML =
      '<div style="font-size: 14.5px; color: var(--slate-bright); margin: 8px 0;">Konfirmasi akan dialihkan ke sistem aplikasi ' + methodId + ' di HP Anda.</div>' +
      '<div style="font-size: 12.5px; color: var(--slate-muted);">Pastikan saldo ' + methodId + ' Anda mencukupi nominal <b>' + totalFormatted + '</b>.</div>';
  }
}
