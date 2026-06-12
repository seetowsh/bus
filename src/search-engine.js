// src/search-engine.js
import { fetchNearbyStops } from './geo.js';

let searchIndex = [];

function initializeSearchIndex() {
  const rawDict = window.bus_dict;
  if (!rawDict) {
    console.error("QA Failure: window.bus_dict was not found on the global scope.");
    return;
  }

  searchIndex = Object.entries(rawDict).map(([id, data]) => ({
    id: id,
    name: data[2],
    normalizedName: data[2].toLowerCase()
  }));
}

function searchBusStopsByName(queryString) {
  const cleanQuery = queryString.trim().toLowerCase();
  if (!cleanQuery) return [];

  const matches = [];
  for (const stop of searchIndex) {
    if (stop.id.includes(cleanQuery) || stop.normalizedName.includes(cleanQuery)) {
      matches.push(stop);
    }
    if (matches.length >= 10) break;
  }
  return matches;
}

/**
 * Shared layout component factor to maintain identical design structure rules
 */
function renderListRow(leftContentHtml, rightContentHtml, clickCallback) {
  const item = document.createElement('li');
  item.className = 'collection-item';
  item.style.cssText = 'cursor: pointer; display: flex; justify-content: space-between; align-items: center;';
  
  item.innerHTML = `
    <div>${leftContentHtml}</div>
    ${rightContentHtml}
  `;
  
  if (clickCallback) {
    item.addEventListener('click', clickCallback);
  }
  return item;
}

/**
 * Renders the contextual location anchor block when the search query remains unpopulated
 */
function renderDefaultActionState(container) {
  container.innerHTML = '';
  
  // Uses your primary MD3 theme color dynamically for the action link text and icon
  const leftHtml = `
    <i class="material-icons left" style="margin-right:10px; color: var(--md-sys-color-primary);">near_me</i>
    <strong style="color: var(--md-sys-color-primary);">Find nearby bus stops</strong>
  `;
  const rightHtml = `<span class="search-stop-id"><i class="material-icons" style="font-size: 1.2rem; opacity: 0.3;">chevron_right</i></span>`;
  
  const actionRow = renderListRow(leftHtml, rightHtml, () => {
    // Drop interactions immediately and flip the string state to loading
    actionRow.style.pointerEvents = 'none';
    actionRow.querySelector('strong').innerText = "Calculating nearby stops...";

    fetchNearbyStops(window.bus_dict)
      .then((closestStops) => {
        container.innerHTML = '';
        
        if (closestStops.length === 0) {
          container.appendChild(renderListRow(
            `<strong>No bus stops found within 1km.</strong>`,
            `<span class="search-stop-id">--</span>`
          ));
          return;
        }

        // Output matching locations using the identical layout rhythm parameters
        closestStops.forEach(stop => {
          // Format distance cleanly to meters if nearby, else show decimal kilometers
          const distanceLabel = stop.distance < 0.5 
            ? `${Math.round(stop.distance * 1000)}m` 
            : `${stop.distance.toFixed(2)}km`;

          const stopRow = renderListRow(
            `<i class="material-icons left" style="margin-right:10px;">place</i><strong>${stop.name}</strong>`,
            `<span class="search-stop-id">${distanceLabel}</span>`,
            () => {
              window.stopno = stop.id;
              window.showResultsOverlay();
            }
          );
          container.appendChild(stopRow);
        });
      })
      .catch((err) => {
        console.error("Spatial runtime exception:", err);
        actionRow.style.pointerEvents = 'auto';
        actionRow.querySelector('strong').innerText = "Location tracking failed. Tap to retry.";
      });
  });
  
  container.appendChild(actionRow);
}

// System mounting orchestrator
document.addEventListener('DOMContentLoaded', () => {
  initializeSearchIndex();

  const searchInput = document.getElementById('bus-search-input');
  const resultsContainer = document.getElementById('search-results-list');

  // Enforce structural visibility right out of the gate on launch
  renderDefaultActionState(resultsContainer);

  searchInput.addEventListener('input', (e) => {
    const query = e.target.value;
    
    // Wiping field context smoothly falls back to the location action trigger
    if (!query.trim()) {
      renderDefaultActionState(resultsContainer);
      return;
    }

    const results = searchBusStopsByName(query);
    resultsContainer.innerHTML = '';
    
    if (results.length === 0) return;

    results.forEach(stop => {
      const row = renderListRow(
        `<i class="material-icons left" style="margin-right:10px;">place</i><strong>${stop.name}</strong>`,
        `<span class="search-stop-id">${stop.id}</span>`, // Uses your existing layout class structure
        () => {
          window.stopno = stop.id;
          window.showResultsOverlay();
        }
      );
      resultsContainer.appendChild(row);
    });
  });
});