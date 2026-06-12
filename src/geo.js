// src/geo.js

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
 * Obtains user coordinates and filters the normalized index array down to nearby items.
 * @param {Array} indexArray The pre-compiled modular search index array.
 * @returns {Promise<Array>} A sorted list of the closest 10 stops within 1km.
 */
export function fetchNearbyStops(indexArray) {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      return reject(new Error("Spatial features are unsupported by this platform."));
    }

    const geoOptions = {
      enableHighAccuracy: false,
      timeout: 6000,             
      maximumAge: 300000         
    };

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLat = position.coords.latitude;
        const userLong = position.coords.longitude;
        let stopsWithDistance = [];

        // Direct flat-array iteration (Significantly faster performance)
        for (const stop of indexArray) {
          // Check simple bounding boundaries using our normalized property labels
          if (Math.abs(userLat - stop.lat) > ROUGH_KM_IN_DEGREES) continue;
          if (Math.abs(userLong - stop.lng) > ROUGH_KM_IN_DEGREES) continue;

          const dist = getDistanceFromLatLonInKm(userLat, userLong, stop.lat, stop.lng);

          if (dist <= 1.0) {
            stopsWithDistance.push({
              id: stop.id,
              name: stop.name,
              distance: dist
            });
          }
        }

        stopsWithDistance.sort((a, b) => a.distance - b.distance);
        resolve(stopsWithDistance.slice(0, 10)); 
      },
      (error) => {
        reject(error);
      },
      geoOptions
    );
  });
}