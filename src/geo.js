// src/geo.js

// Approximate degree threshold equivalent to a 1km bounding box
const ROUGH_KM_IN_DEGREES = 0.01;

function deg2rad(deg) {
  return deg * (Math.PI / 180);
}

function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2 - lat1);
  var dLon = deg2rad(lon2 - lon1);
  var a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c; // Distance in km
}

/**
 * Obtains user coordinates and filters the global dictionary down to nearby items.
 * @param {Object} busDict The global bus stop dataset array map.
 * @returns {Promise<Array>} A sorted list of the closest 5 stops within 1km.
 */
export function fetchNearbyStops(busDict) {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      return reject(new Error("Spatial features are unsupported by this platform."));
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLat = position.coords.latitude;
        const userLong = position.coords.longitude;
        let stopsWithDistance = [];

        for (const [stopId, data] of Object.entries(busDict)) {
          if (!data || data.length < 3) continue;

          const stopLat = data[0];
          const stopLng = data[1];

          // --- The First Pass Bounding Box Filter ---
          if (Math.abs(userLat - stopLat) > ROUGH_KM_IN_DEGREES) continue;
          if (Math.abs(userLong - stopLng) > ROUGH_KM_IN_DEGREES) continue;

          // Only compute full spherical trigonometry inside the bounding area
          const dist = getDistanceFromLatLonInKm(userLat, userLong, stopLat, stopLng);

          if (dist <= 1.0) {
            stopsWithDistance.push({
              id: stopId,
              name: data[2],
              distance: dist
            });
          }
        }

        // Sort ascending by geographical distance metrics
        stopsWithDistance.sort((a, b) => a.distance - b.distance);

        // Return only the top 5 closest items back to the UI loop
        resolve(stopsWithDistance.slice(0, 10));
      },
      (error) => {
        reject(error);
      }
    );
  });
}