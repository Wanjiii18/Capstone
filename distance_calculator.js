// Calculate distance between user and alice kitchen
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lng2 - lng1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
}

// User location from console
const userLat = 10.360995;
const userLng = 123.928589;

// Alice kitchen location from database
const aliceLat = 10.36365290;
const aliceLng = 123.92793201;

const distance = calculateDistance(userLat, userLng, aliceLat, aliceLng);

console.log(`Distance between user and alice kitchen: ${distance.toFixed(2)} meters`);
console.log(`Distance in kilometers: ${(distance/1000).toFixed(3)} km`);
console.log(`Search radius: 1000 meters`);
console.log(`Within search radius: ${distance <= 1000 ? 'YES' : 'NO'}`);