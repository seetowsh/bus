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
function queryStops(queryString) {
  const cleanQuery = queryString.trim().toLowerCase();
  if (!cleanQuery) return [];

  const matches = [];
  
  for (const stop of searchIndex) {
    if (stop.normalizedName.includes(cleanQuery)) {
      matches.push(stop);
    }
    // Hard break to protect the DOM rendering pipeline from choking
    if (matches.length >= 8) break;
  }
  
  return matches;
}

// Executed when the module mounts securely in the DOM
document.addEventListener('DOMContentLoaded', () => {
  initializeSearchIndex();

  const searchInput = document.getElementById('bus-search-input');
  const resultsContainer = document.getElementById('search-results-list');

  if (!searchInput || !resultsContainer) return;

  searchInput.addEventListener('input', (e) => {
    const query = e.target.value;
    const matchedStops = queryStops(query);

    // Wipe down previous canvas nodes
    resultsContainer.innerHTML = '';

    if (matchedStops.length === 0) {
      resultsContainer.innerHTML = query.trim() 
        ? '<li class="collection-item value-text">No matching bus stops found.</li>'
        : '<li class="collection-item muted-text">Type a location above to view matching stops...</li>';
      return;
    }

    // Build the interactive list items dynamically
    matchedStops.forEach(stop => {
      const li = document.createElement('li');
      li.className = 'collection-item';
      li.style.cursor = 'pointer';
      li.style.display = 'flex';
      li.style.justifyContent = 'space-between';
      li.style.alignItems = 'center';
      
      li.innerHTML = `
        <div>
          <i class="material-icons left" style="margin-right:10px; color: var(--md-sys-color-primary);">place</i>
          <strong>${stop.name}</strong>
        </div>
        <span class="search-stop-id">${stop.id}</span>
      `;

      // Operational Event Handler: Tap to pop up the real-time timings
      li.addEventListener('click', () => {
        window.stopno = stop.id; 
        if (typeof window.showResultsOverlay === 'function') {
          window.showResultsOverlay();
        } else {
          alert(`Selected Stop: ${stop.name} (${stop.id}). Overlay function not mapped yet.`);
        }
      });

      resultsContainer.appendChild(li);
    });
  });
});