// user/book.php — Calculate booking period and total
function calculatePeriod() {
  // Read room price from data attribute
  var price = parseInt(document.getElementById('book-page-data').getAttribute('data-room-price') || 0);
  var type = document.getElementById('bk-type').value;
  var startVal = document.getElementById('bk-start').value;

  // Calculate Total
  var total = price;
  if (type === 'Tahunan') {
    total = price * 12;
  }

  // Format total as Rupiah
  var formatted = 'Rp ' + total.toLocaleString('id-ID');
  document.getElementById('bk-total-display').textContent = formatted;
  document.getElementById('bk-total').value = total;

  // Calculate End Date
  if (startVal) {
    var d = new Date(startVal);
    if (isNaN(d.getTime())) return;

    if (type === 'Harian') {
      d.setDate(d.getDate() + 1);
    } else if (type === 'Bulanan') {
      d.setMonth(d.getMonth() + 1);
    } else if (type === 'Tahunan') {
      d.setFullYear(d.getFullYear() + 1);
    }

    // Format to yyyy-mm-dd
    var yyyy = d.getFullYear();
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var dd = String(d.getDate()).padStart(2, '0');
    document.getElementById('bk-end').value = yyyy + '-' + mm + '-' + dd;
  }
}

// Initial calculation on load
document.addEventListener('DOMContentLoaded', function () {
  calculatePeriod();
});
