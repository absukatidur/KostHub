// admin/repairs_form.php — Toggle target options based on type
function toggleTargetOptions() {
  var type = document.getElementById('nr-type').value;
  var targetSelect = document.getElementById('nr-target');
  var options = targetSelect.options;

  var firstVisibleIndex = -1;
  for (var i = 0; i < options.length; i++) {
    var opt = options[i];
    if (type === 'kamar') {
      if (opt.classList.contains('room-opt')) {
        opt.style.display = '';
        if (firstVisibleIndex === -1) firstVisibleIndex = i;
      } else {
        opt.style.display = 'none';
      }
    } else {
      if (opt.classList.contains('fac-opt')) {
        opt.style.display = '';
        if (firstVisibleIndex === -1) firstVisibleIndex = i;
      } else {
        opt.style.display = 'none';
      }
    }
  }
  targetSelect.selectedIndex = firstVisibleIndex;
}