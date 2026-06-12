// src/favourites.js
import { openTimingPanel } from './transit-api.js';

const COOKIE_NAME = 'fav';

/**
 * Parses the raw legacy cookie string into a clean array of Unique IDs
 */
export function getFavourites() {
  const cookies = document.cookie.split(';');
  const favCookie = cookies.find(row => row.trim().startsWith(`${COOKIE_NAME}=`));
  if (!favCookie) return [];

  const rawValue = favCookie.split('=')[1];
  if (!rawValue) return [];

  // Decode and clear out any empty mutations or structural padding
  return decodeURIComponent(rawValue).split(',').filter(Boolean);
}

/**
 * Commits the modified active sequence back to the browser storage
 */
function saveFavourites(favouritesArray) {
  const expiryDate = new Date();
  // Standard long-lived durability mapping (1 Year retention safety window)
  expiryDate.setTime(expiryDate.getTime() + (365 * 24 * 60 * 60 * 1000));
  
  document.cookie = `${COOKIE_NAME}=${favouritesArray.join(',')};expires=${expiryDate.toUTCString()};path=/;SameSite=Lax`;
}

/**
 * Public evaluation check to coordinate active button states
 */
export function isFavourite(stopId) {
  return getFavourites().includes(String(stopId));
}

/**
 * Toggles a selection's presence inside the cookie storage layer
 */
export function toggleFavourite(stopId) {
  const cleanId = String(stopId);
  let currentFavs = getFavourites();

  if (currentFavs.includes(cleanId)) {
    currentFavs = currentFavs.filter(id => id !== cleanId);
  } else {
    currentFavs.push(cleanId);
  }

  saveFavourites(currentFavs);
  
  // Trigger an instant reactive refresh across the primary interface layers
  renderFavouritesView();
}

/**
 * Destroys the persistent cookie and triggers UI layout transitions
 */
export function clearAllFavourites() {
  document.cookie = `${COOKIE_NAME}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
  if (typeof M !== 'undefined' && M.toast) {
    M.toast({ html: 'Favourites cleared successfully.', displayLength: 2500 });
  }
  renderFavouritesView();
}

/**
 * Resolves a Stop ID against the unified map cache to extract its text descriptors
 */
function getStopMetadata(stopId) {
  const geojson = window.seetowbusgeojson;
  if (!geojson || !geojson.features) return null;
  
  const match = geojson.features.find(f => String(f.properties.busstopcode) === String(stopId));
  return match ? match.properties.description : null;
}

/**
 * Builds and mounts the structural interactive rows inside the Favourites panel
 */
export function renderFavouritesView() {
  const container = document.getElementById('favourites-pane-content');
  if (!container) return;

  const favouriteIds = getFavourites();

  // Handle empty state configurations gracefully
  if (favouriteIds.length === 0) {
    container.innerHTML = `
      <div class="center-align" style="padding: 40px 20px; opacity: 0.6;">
        <i class="material-icons large">star_border</i>
        <h5>No saved favourites yet</h5>
        <p style="max-width: 400px; margin: 12px auto 0 auto; font-size: 0.95rem; line-height: 1.5;">
          Tap the star icon inside any bus arrival panel to bookmark your frequent stops for instant access!
        </p>
      </div>
    `;
    return;
  }

  // Generate an isolated list container matched to your clean search aesthetics
  const listElement = document.createElement('ul');
  listElement.className = 'collection';
  listElement.style.cssText = 'border:none !important; box-shadow:none !important; margin:0 !important; background:transparent !important;';

  favouriteIds.forEach(id => {
    const stopName = getStopMetadata(id) || 'Unknown Bus Stop';
    
    const rowItem = document.createElement('li');
    rowItem.className = 'collection-item';
    rowItem.style.cssText = 'cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 20px 8px !important; border-top:none !important; border-left:none !important; border-right:none !important; border-bottom:1px solid var(--border-color) !important; background:transparent !important;';
    
    rowItem.innerHTML = `
      <div style="display: flex; align-items: center; gap: 12px;">
        <i class="material-icons" style="color: var(--md-sys-color-primary);">place</i>
        <strong style="color: var(--text-color); font-weight: 600;">${stopName}</strong>
      </div>
      <span class="search-stop-id">${id}</span>
    `;

    // Clicking a row slides open the live data timelines instantly
    rowItem.addEventListener('click', () => openTimingPanel(id));
    listElement.appendChild(rowItem);
  });

  // Inject structural list along with an ergonomic layout cleanup trigger
  container.innerHTML = '';
  container.appendChild(listElement);

  const cleanupContainer = document.createElement('div');
  cleanupContainer.style.cssText = 'margin-top: 32px; padding: 0 8px;';
  cleanupContainer.innerHTML = `
    <button class="btn-flat waves-effect" style="width: 100%; border: 1px solid var(--border-color); color: var(--md-sys-color-error, #ba1a1a); text-transform: none; font-weight: 600; border-radius: 8px;">
      <i class="material-icons left">delete_sweep</i>Clear All Bookmarks
    </button>
  `;
  
  cleanupContainer.querySelector('button').addEventListener('click', clearAllFavourites);
  container.appendChild(cleanupContainer);
}

// Automatically populate and synchronize rows when the client initialization finishes
document.addEventListener('DOMContentLoaded', () => {
  renderFavouritesView();
});