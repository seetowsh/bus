// src/map-engine.js
import { openTimingPanel } from './transit-api.js';

let mapInstance = null;

/**
 * Uses the browser's engine to compute the exact absolute RGB values 
 * of the color-mix tokens so that the Canvas renderer can draw them.
 */
function getThemeColor() {
  const dummy = document.createElement('div');
  // Pass the exact layout color-mix definition you want to inherit
  dummy.style.color = 'color-mix(in srgb, var(--md-sys-color-primary), var(--md-sys-color-on-primary) 16%)';
  document.body.appendChild(dummy);
  
  // The browser evaluates the tokens instantly into a solid "rgb(r, g, b)" string
  const resolvedColor = getComputedStyle(dummy).color;
  document.body.removeChild(dummy);
  
  return resolvedColor || '#ff7800'; // Fallback to classic orange if resolution slips
}

/**
 * Initializes the Leaflet map and plots the GeoJSON single-source layer
 */
export function initializeMap() {
  const mapContainer = document.getElementById('map');
  if (!mapContainer) return;

  // 1. Instatiate the core Leaflet Map viewport
  mapInstance = L.map('map', {
    center: { lat: 1.290270, lng: 103.851959 },
    zoom: 17,
    minZoom: 14,
    preferCanvas: true,
    layers: [
      new L.TileLayer("https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}", {
        bounds: [[1.506417, 103.552020], [1.199572, 104.058765]],
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '© 2026 Google, SLA',
      })
    ]
  });

  // Handle errors gracefully without stalling the UI main threads
  mapInstance.on('locationerror', (err) => {
    console.warn("Map geo-location track skipped:", err.message);
  });

  // Automatically focus the map wrapper to the user's current spatial position
  mapInstance.locate({ setView: true, minZoom: 15 });

  // 2. Resolve the dynamic theme color token
  const dynamicMarkerColor = getThemeColor();

  // 3. Ingest the global GeoJSON dataset layer
  if (window.seetowbusgeojson) {
    L.geoJSON(window.seetowbusgeojson, {
      pointToLayer: (feature, latlng) => {
        return L.circleMarker(latlng, {
          radius: 6,
          weight: 1,
          opacity: 1,
          fillOpacity: 0.8,
          // Apply your unified theme properties directly to the canvas elements
          fillColor: dynamicMarkerColor,
          color: dynamicMarkerColor 
        });
      },
      onEachFeature: (feature, layer) => {
              const stopCode = feature.properties.busstopcode;
              const stopName = feature.properties.description;

              // 1. Generate an isolated DOM element node to guarantee style/event sandboxing
              const popupElement = document.createElement('div');
              popupElement.style.cssText = 'display: flex; flex-direction: column; gap: 8px; text-align: center; padding: 2px 0;';

              // 2. Populate with clean semantic elements using your theme typography variables
              popupElement.innerHTML = `
                <div>
                  <strong style="display: block; font-size: 1.05rem; color: var(--text-color); font-weight: 700; line-height: 1.3;">${stopName}</strong>
                  <span style="display: block; font-size: 0.85rem; opacity: 0.6; color: var(--text-color); margin-top: 2px;">Stop ID: ${stopCode}</span>
                </div>
                <button class="btn filled btn-small" style="text-transform: none; font-weight: 600; width: 100%; margin-top: 4px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                  <i class="material-icons" style="font-size: 1.1rem;">directions_bus</i>View Timings
                </button>
              `;

              // 3. Bind the interactive behavior cleanly using scoped event listeners instead of legacy window string hooks
              const actionButton = popupElement.querySelector('button');
              actionButton.addEventListener('click', (e) => {
                e.preventDefault();
                openTimingPanel(stopCode); // Hand off directly to your high-performance modular pipeline
                layer.closePopup();        // Cleanly collapse map bubble as the transit overlay slides into view
              });

              // 4. Pass the configured live node directly into Leaflet's layout binder
              layer.bindPopup(popupElement, {
                maxWidth: 240,
                offset: [0, -2] // Budges the bubble tip slightly above your circular dot marker apex
              });
            }
    }).addTo(mapInstance);
  } else {
    console.error("Map Engine Failure: window.seetowbusgeojson data source was not ready.");
  }
}

// Automatically boot up the map module context once the document structure has stabilized
document.addEventListener('DOMContentLoaded', () => {
  initializeMap();
});