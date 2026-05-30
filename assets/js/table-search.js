/**
 * Reusable table/list search filter.
 * Usage: initTableSearch(inputId, bodySelector, countId, countTemplate, displayType)
 * 
 * @param {string} inputId        - The search input element ID
 * @param {string} bodySelector   - CSS selector for the rows/items container children (e.g., '#room-tbody tr')
 * @param {string} countId        - The counter element ID (optional, pass null to skip)
 * @param {string} countTemplate  - Template string with {count} placeholder (e.g., 'Menampilkan {count} kamar')
 * @param {string} displayType    - CSS display value when visible (default: '', use 'flex' for flex items)
 * @param {number} minCells       - Minimum cell count to consider row filterable (default: 2, set 0 for non-table)
 */
function initTableSearch(inputId, bodySelector, countId, countTemplate, displayType, minCells) {
  displayType = displayType || '';
  minCells = (typeof minCells !== 'undefined') ? minCells : 2;

  document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById(inputId);
    if (!searchInput) return;

    searchInput.oninput = function () {
      var q = this.value.toLowerCase();
      var visibleCount = 0;
      var items = document.querySelectorAll(bodySelector);

      items.forEach(function (el) {
        // For table rows, skip empty/placeholder rows
        if (minCells > 0 && el.cells && el.cells.length < minCells) return;

        var text = el.textContent.toLowerCase();
        if (text.includes(q)) {
          el.style.display = displayType;
          visibleCount++;
        } else {
          el.style.display = 'none';
        }
      });

      if (countId && countTemplate) {
        var countEl = document.getElementById(countId);
        if (countEl) {
          countEl.textContent = countTemplate.replace('{count}', visibleCount);
        }
      }
    };
  });
}
