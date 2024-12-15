<template>
  <div>
    <!-- Map container -->
    <div id="map" style="height: 400px; width: 100%;"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { BASE_URL } from '@/config/apiConfig';
import { io } from 'socket.io-client';

// Store the latitude and longitude values from the backend
const latitude = ref(0);
const longitude = ref(0);

// Google Maps API URL (make sure to replace with your actual API key)
const googleMapsUrl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAQl9KFqleeQfGVTmHDGFTsKzGKf7EMaVA';

// Function to load the Google Maps API script
const loadGoogleMapsScript = (): Promise<void> => {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.maps) {
      resolve(); // If Google Maps is already loaded
    } else {
      const script = document.createElement('script');
      script.src = googleMapsUrl;
      script.async = true;
      script.defer = true;
      script.onload = () => resolve();
      script.onerror = () => reject(new Error('Google Maps API failed to load.'));
      document.head.appendChild(script);
    }
  });
};

// Function to fetch location data from the backend
const fetchLocationData = async (): Promise<void> => {
  const token = localStorage.getItem('authToken');

  try {
    // Replace with the actual delivery ID and API endpoint
    const response = await axios.get(`${BASE_URL}/deliveries/1`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    latitude.value = parseFloat(response.data.latitude);
    longitude.value = parseFloat(response.data.longitude);
  } catch (error) {
    console.error('Error fetching location data:', error);
  }
};

// Function to initialize the Google Map
const initializeMap = (): void => {
  const map = new window.google.maps.Map(document.getElementById('map') as HTMLElement, {
    center: { lat: latitude.value, lng: longitude.value },
    zoom: 18, // Increased zoom level for more detail
  });

  // Add a marker at the initial location
  new window.google.maps.Marker({
    position: { lat: latitude.value, lng: longitude.value },
    map: map,
  });

  // Return the map object for later use
  return map;
};

// Function to update the map marker in real-time
const updateMapLocation = (lat: number, lng: number): void => {
  latitude.value = lat;
  longitude.value = lng;

  // Find the map and update the marker's position
  const map = new window.google.maps.Map(document.getElementById('map') as HTMLElement, {
    center: { lat, lng },
    zoom: 18, // Increased zoom level for more detail
  });

  new window.google.maps.Marker({
    position: { lat, lng },
    map: map,
  });
};

// Listen for Socket.IO updates
const socket = io('http://localhost:6001'); // Connect to the Node.js Socket.IO server

socket.on('connect', () => {
  console.log('Socket connected:', socket.id);
});
// Listen for location updates on the delivery-tracking channel
socket.on('laravel_database_tracking.1', (data) => {
  console.log('Received delivery location update:', data);
  updateMapLocation(data.latitude, data.longitude); // Update map with the new location
});

// Initialize Google Map after loading the script and fetching location data
onMounted(async () => {
  try {
    // Load Google Maps script
    await loadGoogleMapsScript();

    // Fetch initial location data
    await fetchLocationData();

    // Initialize the map with the initial coordinates
    initializeMap();
  } catch (error) {
    console.error('Error loading Google Maps or initializing the map:', error);
  }
});
</script>

<style scoped>
/* Optionally style the map */
#map {
  height: 400px;
  width: 100%;
}
</style>
