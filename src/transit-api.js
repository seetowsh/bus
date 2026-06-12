// src/transit-api.js

// --- Private Module State Engine ---
let activeStopId = null;
let ltaServices = [];
let nusServices = [];
let pollIntervalId = null;
let timerIntervalId = null;

// Hardcoded fallback definitions for the NUS transit router
const NUS_STOP_DICT = {
  "16169": "UTOWN", "16199": "COCHMAN", "15131": "KENTVALE", "16141": "MUSEUM",
  "16151": "YIH", "15121": "RAFFLES", "16121": "COMP", "16131": "VENTUS",
  "16241": "LT13", "16231": "AS5", "16221": "BIZ2", "16211": "OPP_HSSML",
  "16111": "PGP", "16181": "PGPR", "16411": "KR-MRT", "16421": "KR-MRT-OPP",
  "15151": "OPP_NUSS", "15171": "OPP_KC", "15181": "OPP_HSSVALE", "16311": "S17"
};

/**
 * Formats fractional minutes to explicit structural mm:ss string formats
 * Intuition: Isolates whole numbers for minutes, applies modulo math for seconds, pads strings with zeros.
 */
function minTommss(minutes) {
  const sign = minutes < 0 ? "-" : "";
  const min = Math.floor(Math.abs(minutes));
  const sec = Math.floor((Math.abs(minutes) * 60) % 60);
  return sign + (min < 10 ? "0" : "") + min + ":" + (sec < 10 ? "0" : "") + sec;
}

/**
 * Evaluates temporal delta ranges across timestamps
 */
function calculateArrivalTime(time, timenow) {
  const targetTime = +new Date(time);
  if (isNaN(targetTime)) return '';
  
  const deltaMinutes = (targetTime - timenow) / 60000;
  const timeStr = minTommss(deltaMinutes);
  
  return (timeStr.charAt(0) === "-") ? "Very soon!" : `${timeStr} mins`;
}

// --- Semantic Design Tokens Map ---
function getWAB(bus) {
  return (bus["Feature"] === "WAB" || bus["Load"] === "") ? '' : '<i style="color:var(--md-sys-color-error, red);" class="material-icons">accessible</i>';
}

function getLoad(bus) {
  const loads = { 'SDA': 'directions_walk', 'SEA': 'airline_seat_recline_normal', 'LSD': 'wc' };
  return loads[bus["Load"]] ? `<i class="material-icons">${loads[bus["Load"]]}</i>` : "";
}

function getDeck(bus) {
  if (bus["Type"] === "SD") return '<i class="material-icons">filter_1</i> ';
  if (bus["Type"] === "DD") return '<i class="material-icons">filter_2</i> ';
  if (bus["Type"] === "BD") return '🅱️';
  return "";
}

function getMapLink(bus, contentHtml) {
  if (!bus["Latitude"] || parseFloat(bus["Latitude"]) === 0) return contentHtml;
  // Fixes the 2017 broken string redirect framework with native maps lookup syntax
  return `<a target="_blank" href="https://www.google.com/maps/search/?api=1&query=${bus["Latitude"]},${bus["Longitude"]}">${contentHtml}</a>`;
}

/**
 * Creates structural cellular strings for LTA elements
 */
function createBusArrivalHtml(bus) {
  if (!bus || !bus.EstimatedArrival) return '<td>--</td>';

  const metrics = `${getWAB(bus)}${getLoad(bus)}${getDeck(bus)}`;
  const timeSpan = `<span class="bus-countdown-ticker" data-arrival="${bus.EstimatedArrival}"></span>`;

  return `<td>${getMapLink(bus, metrics + timeSpan)}</td>`;
}

/**
 * Creates structural cellular strings for NUS items
 */
function createNusArrivalHtml(eta) {
  if (!eta || !eta.ts) return '<td>--</td>';
  return `<td><span class="bus-countdown-ticker" data-arrival="${eta.ts}"></span></td>`;
}

/**
 * Lightweight independent rendering loop executing at 1Hz
 * Intuition: Prevents costly network thrashing by recalculating text elements on the fly in screen memory.
 */
function runCountdownLoop() {
  const timenow = Date.now();
  document.querySelectorAll(".bus-countdown-ticker").forEach(element => {
    const timestamp = element.getAttribute("data-arrival");
    if (timestamp) {
      element.innerText = calculateArrivalTime(timestamp, timenow);
    }
  });
}

/**
 * Resolves stop details against the central single-source GeoJSON array cache
 */
function getStopMetadata(stopId) {
  const geojson = window.seetowbusgeojson;
  if (!geojson || !geojson.features) return { name: "Unknown Stop", lat: 0, lng: 0 };

  const feature = geojson.features.find(f => String(f.properties.busstopcode) === String(stopId));
  if (!feature) return { name: "Unknown Stop", lat: 0, lng: 0 };

  return {
    name: feature.properties.description,
    lat: feature.geometry.coordinates[1], // Latitude
    lng: feature.geometry.coordinates[0]  // Longitude
  };
}

/**
 * Asynchronously pipes server streams concurrently using standard high-performance Promises
 */
async function fetchTimingsFromServer() {
  if (!activeStopId) return;

  const ltaUrl = `https://misty-king-3f2c.seetow.workers.dev/LTA?id=${activeStopId}`;
  const nusCode = NUS_STOP_DICT[activeStopId];
  const nusUrl = nusCode ? `https://misty-king-3f2c.seetow.workers.dev/NUS?id=${nusCode}` : null;

  try {
    // Fire network fetch requests concurrently in the background pipeline
    const [ltaRes, nusRes] = await Promise.all([
      fetch(ltaUrl).then(r => r.json()).catch(() => ({ Services: [] })),
      nusUrl ? fetch(nusUrl).then(r => r.json()).catch(() => null) : Promise.resolve(null)
    ]);

    ltaServices = ltaRes.Services || [];
    nusServices = nusRes?.ShuttleServiceResult?.shuttles || [];

    renderTimingTable();
  } catch (error) {
    console.error("Critical API ingestion crash:", error);
  }
}

/**
 * Injects layout nodes into the clean results grid view container sheet
 */
function renderTimingTable() {
  const targetContainer = document.querySelector("#results-panel .container");
  if (!targetContainer) return;

  const meta = getStopMetadata(activeStopId);
  const formattedStopId = activeStopId > 100000 ? "NUS" : activeStopId;
  const currentTimestamp = new Date().toLocaleTimeString();

  let tableRowsHtml = [];

  // 1. Process LTA Rows
  ltaServices.forEach(service => {
    if (service?.ServiceNo) {
      tableRowsHtml.push(`
        <tr>
          <td><strong><a href="https://busrouter.sg/#/services/${service.ServiceNo}" target="_blank" style="color:var(--md-sys-color-primary); font-weight:700;">${service.ServiceNo}</a></strong></td>
          ${createBusArrivalHtml(service.NextBus)}
          ${createBusArrivalHtml(service.NextBus2)}
          ${createBusArrivalHtml(service.NextBus3)}
        </tr>
      `);
    }
  });

  // 2. Process NUS Rows
  nusServices.forEach(shuttle => {
    if (shuttle?._etas?.length > 0) {
      tableRowsHtml.push(`
        <tr>
          <td><strong>${shuttle.name}</strong> <span style="color: #003D7C; font-weight: bold; font-size:0.8rem;">(NUS)</span></td>
          ${createNusArrivalHtml(shuttle._etas[0])}
          ${createNusArrivalHtml(shuttle._etas[1])}
          ${createNusArrivalHtml(shuttle._etas[2])}
        </tr>
      `);
    }
  });

  // Handle empty state configurations gracefully
  if (tableRowsHtml.length === 0) {
    tableRowsHtml.push(`<tr><td colspan="4" class="center-align" style="padding:40px 0;">No active bus services found for this location.</td></tr>`);
  }

  // Inject structural layout templates directly into the container viewport sheet
  targetContainer.innerHTML = `
      <h4 style="font-family:Livvic; font-weight:600; margin-bottom:4px;">${meta.name}</h4>
      <p class="muted-text" style="margin:0 0 20px 0; font-size:1.1rem; opacity:0.6;">Stop ID: ${formattedStopId}</p>
      
      <div style="display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <a target="_blank" href="https://www.google.com/maps/?q=&layer=c&cbll=${meta.lat},${meta.lng}" class="btn waves-effect waves-light" style="background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary);"><i class="material-icons left">directions</i> Street View</a>
        <button class="btn-flat waves-effect" id="overlay-fav-toggle" style="border:1px solid var(--border-color); color:var(--text-color);"><i class="material-icons left">star_border</i> Favourite</button>
      </div>

      <p class="muted-text" style="font-size:0.85rem; opacity:0.5; margin-bottom:8px;">Data fetched from server at: ${currentTimestamp}</p>
      
      <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        
        <table class="striped">
          <thead>
            <tr>
              <th>Bus</th>
              <th>Next</th>
              <th>2nd Bus</th>
              <th>3rd Bus</th>
            </tr>
          </thead>
          <tbody>
            ${tableRowsHtml.join("")}
          </tbody>
        </table>
        
      </div>
      
    `;


  // Start the 1Hz ticker updates immediately now that the fields are present in the DOM
  runCountdownLoop();
}

/**
 * Primary Entry Point called natively from interaction scripts
 */
export function openTimingPanel(stopId) {
  if (!stopId) return;

  // Kill any active intervals to prevent background performance memory leaks
  closeTimingPanel();

  activeStopId = String(stopId);

  // Present the placeholder skeleton load state to the user cleanly
  const targetContainer = document.querySelector("#results-panel .container");
  if (targetContainer) {
    targetContainer.innerHTML = `
      <h4>Loading Bus Stop...</h4>
      <div class="progress" style="background-color: color-mix(in srgb, var(--md-sys-color-primary) 20%, transparent);"><div class="indeterminate" style="background-color: var(--md-sys-color-primary);"></div></div>
      <p class="center-align muted-text" style="margin-top:20px;">Fetching dynamic arrival streams from server layer...</p>
    `;
  }

  // Slide up the overlay view pane container natively at 60fps
  if (typeof window.showResultsOverlay === "function") {
    window.showResultsOverlay();
  }

  // Execute initial data load hook immediately
  fetchTimingsFromServer();

  // Establish persistent background schedulers
  pollIntervalId = setInterval(fetchTimingsFromServer, 30000);
  timerIntervalId = setInterval(runCountdownLoop, 1000);
}

/**
 * Programmatic lifecycle termination hook to stop background CPU thrashing when closed
 */
export function closeTimingPanel() {
  if (pollIntervalId) clearInterval(pollIntervalId);
  if (timerIntervalId) clearInterval(timerIntervalId);
  pollIntervalId = null;
  timerIntervalId = null;
  activeStopId = null;
}