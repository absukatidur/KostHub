// user/browse_rooms.php — Room filter by type and floor
document.addEventListener('DOMContentLoaded', function () {
  var typeFilter = document.getElementById('bf-type');
  var floorFilter = document.getElementById('bf-floor');
  var emptyMsg = document.getElementById('empty-msg');

  function filterRooms() {
    var selectedType = typeFilter.value;
    var selectedFloor = floorFilter.value;

    var visibleCount = 0;
    var cards = document.querySelectorAll('.room-browse-card');
    cards.forEach(function (card) {
      var cType = card.getAttribute('data-type');
      var cFloor = card.getAttribute('data-floor');

      var typeMatch = selectedType === '' || cType === selectedType;
      var floorMatch = selectedFloor === '' || cFloor === selectedFloor;

      if (typeMatch && floorMatch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (visibleCount === 0 && cards.length > 0) {
      emptyMsg.style.display = 'block';
    } else {
      emptyMsg.style.display = 'none';
    }
  }

  typeFilter.onchange = filterRooms;
  floorFilter.onchange = filterRooms;
});
