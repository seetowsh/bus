// src/search-engine.js
import { fetchNearbyStops } from './geo.js';
import { openTimingPanel } from './transit-api.js';

// Private sandbox state for this module
let searchIndex = [];

/**
 * Parses the global GeoJSON FeatureCollection into a flat, optimized array
 */
function initializeSearchIndex() {
  const geojson = window.seetowbusgeojson;
  
  if (!geojson || !geojson.features) {
    console.error("QA Failure: window.seetowbusgeojson was not found on the global scope.");
    return;
  }

  // Parse the up-to-date GeoJSON features array cleanly
  searchIndex = geojson.features.map(feature => {
    const props = feature.properties;
    const coords = feature.geometry.coordinates;
    const name = props.description;

    return {
      id: String(props.busstopcode),
      name: name,
      normalizedName: name.toLowerCase(),
      // Remember: GeoJSON specification dictates [Longitude, Latitude] order!
      lat: coords[1],
      lng: coords[0]
    };
  });
  
  console.log(`QA Success: Single-source search index built for ${searchIndex.length} bus stops via GeoJSON.`);
}

function searchBusStopsByName(queryString) {
  const cleanQuery = queryString.trim().toLowerCase();
  if (!cleanQuery) return [];

  const matches = [];
  for (const stop of searchIndex) {
    if (stop.id.includes(cleanQuery) || stop.normalizedName.includes(cleanQuery)) {
      matches.push(stop);
    }
    if (matches.length >= 10) break; // Matches your updated 10-result limit
  }
  return matches;
}

function renderListRow(leftContentHtml, rightContentHtml, clickCallback) {
  const item = document.createElement('li');
  item.className = 'collection-item';
  item.style.cssText = 'cursor: pointer; display: flex; justify-content: space-between; align-items: center;';
  item.innerHTML = `<div>${leftContentHtml}</div>${rightContentHtml}`;
  if (clickCallback) item.addEventListener('click', clickCallback);
  return item;
}

function renderDefaultActionState(container) {
  container.innerHTML = '';
  
  const leftHtml = `
    <i class="material-icons left" style="margin-right:10px; color: var(--md-sys-color-primary);">near_me</i>
    <strong style="color: var(--md-sys-color-primary);">Find nearby bus stops</strong>
  `;
  const rightHtml = `<span class="search-stop-id"><i class="material-icons" style="font-size: 1.2rem; opacity: 0.3;">chevron_right</i></span>`;
  
  const actionRow = renderListRow(leftHtml, rightHtml, () => {
    actionRow.style.pointerEvents = 'none';
    actionRow.querySelector('strong').innerText = "Calculating nearby stops...";

    // PASSING THE SEARCH INDEX: Cleaner execution pipeline
    fetchNearbyStops(searchIndex)
      .then((closestStops) => {
        container.innerHTML = '';
        
        if (closestStops.length === 0) {
          container.appendChild(renderListRow(
            `<strong>No bus stops found within 1km.</strong>`,
            `<span class="search-stop-id">--</span>`
          ));
          return;
        }

        closestStops.forEach(stop => {
          const distanceLabel = stop.distance < 0.1 
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

document.addEventListener('DOMContentLoaded', () => {
  initializeSearchIndex();

  const searchInput = document.getElementById('bus-search-input');
  const resultsContainer = document.getElementById('search-results-list');

  renderDefaultActionState(resultsContainer);

  searchInput.addEventListener('input', (e) => {
    const query = e.target.value;
    
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
            `<span class="search-stop-id">${stop.id}</span>`,
            () => {
              // This fires instantly on the very first tap!
              openTimingPanel(stop.id);
            }
          );
          resultsContainer.appendChild(row);
        });
  });
});