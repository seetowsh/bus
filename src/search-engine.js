// Private sandbox state for this module
let searchIndex = [];

/**
 * Normalizes the global bus_dict into a flat array once on launch
 */
function initializeSearchIndex() {
  const rawDict = window.bus_dict;
  
  if (!rawDict) {
    console.error("QA Failure: window.bus_dict was not found on the global scope.");
    return;
  }

  // Map the object key-value arrays into streamlined, searchable objects
  searchIndex = Object.entries(rawDict).map(([id, data]) => ({
    id: id,
    name: data[2],
    normalizedName: data[2].toLowerCase()
  }));
  
  console.log(`QA Success: Optimized search index built for ${searchIndex.length} bus stops.`);
}

/**
 * Loops through the cache index with a strict cap threshold
 */
// FIX: Renamed from queryStops to match your event listener invocation below
function searchBusStopsByName(queryString) {
  const cleanQuery = queryString.trim().toLowerCase();
  if (!cleanQuery) return [];

  const matches = [];
  
  for (const stop of searchIndex) {
    // Check if the query matches the 5-digit ID OR the text name
    if (stop.id.includes(cleanQuery) || stop.normalizedName.includes(cleanQuery)) {
      matches.push(stop);
    }
    
    // Keep protecting the DOM pipeline from rendering too many items
    if (matches.length >= 10) break;
  }
  
  return matches;
}

// Executed when the module mounts securely in the DOM
document.addEventListener('DOMContentLoaded', () => {
  initializeSearchIndex();

  const searchInput = document.getElementById('bus-search-input');
  const resultsContainer = document.getElementById('search-results-list');

  // This executes instantly on user keystroke input
  searchInput.addEventListener('input', (e) => {
    const query = e.target.value;
    const results = searchBusStopsByName(query); // Works perfectly now!
    
    // Clear container completely to prevent paint leakage
    resultsContainer.innerHTML = '';
    
    if (results.length === 0 && query.trim() !== '') {
      return;
    }

    // Generate your exact layout loop template sequentially
    results.forEach(stop => {
      const item = document.createElement('li');
      item.className = 'collection-item';
      
      // Clean flex properties with zero framework overrides needed
      item.style.cssText = 'cursor: pointer; display: flex; justify-content: space-between; align-items: center;';
      
      item.innerHTML = `
        <div>
          <i class="material-icons left" style="margin-right:10px;">place</i>
          <strong>${stop.name}</strong>
        </div>
        <span class="search-stop-id">${stop.id}</span>
      `;
      
      // Bind behavior trigger natively
      item.addEventListener('click', () => {
        window.stopno = stop.id;
        window.showResultsOverlay();
      });
      
      resultsContainer.appendChild(item);
    });
  });
});