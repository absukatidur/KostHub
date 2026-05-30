// admin/orders_form.php — Calculate end date and total based on room, type, and start date
function calculateEndAndTotal() {
  var roomSelect = document.getElementById('no-room');
  var selectedOption = roomSelect.options[roomSelect.selectedIndex];
  if (!selectedOption || selectedOption.value === '') {
    document.getElementById('no-total-display').textContent = 'Rp 0';
    document.getElementById('no-total').value = 0;
    return;
  }

  var price = parseInt(selectedOption.getAttribute('data-price') || 0);
  var type = document.getElementById('no-type').value;
  var startVal = document.getElementById('no-start').value;

  // Calculate Total
  var total = price;
  if (type === 'Tahunan') {
    total = price * 12;
  }

  // Format total as Rupiah
  var formatted = 'Rp ' + total.toLocaleString('id-ID');
  document.getElementById('no-total-display').textContent = formatted;
  document.getElementById('no-total').value = total;

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
    document.getElementById('no-end').value = yyyy + '-' + mm + '-' + dd;
  }
}

// Initial calculation on load
document.addEventListener('DOMContentLoaded', function () {
  calculateEndAndTotal();
});
