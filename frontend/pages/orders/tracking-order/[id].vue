<template>
  <div>
    <div id="map" style="height: 400px; width: 100%;"></div>
    <table class="table">
      <thead>
      <tr>
        <th>Date / Status</th>
        <th>Product Name</th>
        <th>Delivery Partner</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <!-- Main Row -->
      <template v-for="(item, index) in deliveryData" :key="index">
        <tr>
          <td>
            <div>{{ item.date }}</div>
            <div :class="item.status === 'Delivered' ? 'status-delivered' : 'status-in-transit'">
              {{ item.status }}
            </div>
          </td>
          <td>{{ item.productName }}</td>
          <td>{{ item.deliveryPartner }}</td>
          <td>
            <button @click="toggleDetails(index)">
              {{ item.showDetails ? 'Less details' : 'More details' }}
            </button>
          </td>
        </tr>
        <!-- Details Row -->
        <tr v-if="item.showDetails" class="details-row">
          <td colspan="4">
            <div class="delivery-status">
              <!-- Header Section -->
              <div class="header">
                <h1>Out For Delivery Today</h1>
                <p>The delivery of the parcel is estimated at 10.30 A.M to 11.30 P.M</p>
              </div>

              <!-- Enhanced Progress Bar -->
              <div class="progress-container">
                <div class="progress-bar">
                  <div
                    class="milestone"
                    v-for="(step, i) in item.timeline"
                    :key="i"
                    :class="{ completed: i < item.currentStep, active: i === item.currentStep }"
                  >
                    <!-- Circle -->
                    <div class="circle">
                      <span v-if="i < item.currentStep" class="checkmark">✔</span>
                    </div>
                    <!-- Labels -->
                    <div class="labels">
                      <span class="label">{{ step.status }}</span>
                      <span class="date">{{ step.date }}</span>
                    </div>
                    <!-- Connector Line -->
                    <div
                      class="connector"
                      v-if="i < item.timeline.length - 1"
                      :class="{ completed: i < item.currentStep }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Details Table -->
              <div class="details-section">
                <div class="table-header">
                </div>
                <table class="details-table">
                  <thead>
                  <tr>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Status</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(detail, i) in item.details" :key="i">
                    <td>{{ detail.dateTime }}</td>
                    <td>{{ detail.location }}</td>
                    <td>{{ detail.status }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </td>
        </tr>
      </template>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { io } from 'socket.io-client';
import { BASE_URL } from '@/config/apiConfig';
import { useRoute } from 'vue-router';

const route = useRoute();
const deliveryData = reactive([]);
const latitude = ref(0);
const longitude = ref(0);
let map = null; // Store the map instance
let marker = null; // Store the marker instance
const deliveryId = route.params.id;


const loadGoogleMapsScript = (): Promise<void> => {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.maps) {
      resolve(); // Google Maps is already loaded
    } else {
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDvGW3_JWixICYtxI-5rjM_gamP9xhvYzQ`; // Replace with your key
      script.async = true;
      script.defer = true;
      script.onload = () => resolve();
      script.onerror = () => reject(new Error('Google Maps API failed to load.'));
      document.head.appendChild(script);
    }
  });
};

const fetchDeliveryData = async (): Promise<void> => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`${BASE_URL}/deliveries/${deliveryId}`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = response.data;
    deliveryData.push({
      date: new Date(data.last_updated_at).toLocaleDateString(),
      status: data.status,
      productName: `Order #${data.order_id}`,
      deliveryPartner: data.delivery_partner,
      trackingCode: data.tracking_code,
      currentStep: data.current_step,
      showDetails: false, // Default to collapsed details
      timeline: data.timeline
    });

    latitude.value = parseFloat(data.latitude);
    longitude.value = parseFloat(data.longitude);

    console.log(parseFloat(data.latitude))
  } catch (error) {
    console.error('Error fetching delivery data:', error);
  }
};

const initializeMap = (): void => {
  map = new window.google.maps.Map(document.getElementById('map') as HTMLElement, {
    center: { lat: latitude.value, lng: longitude.value },
    zoom: 15,
  });

  marker = new window.google.maps.Marker({
    position: { lat: latitude.value, lng: longitude.value },
    map: map,
  });
};

const updateMapLocation = (lat: number, lng: number): void => {
  if (map && marker) {
    map.setCenter({ lat, lng });
    marker.setPosition({ lat, lng });
  }
};

const toggleDetails = (index: number): void => {
  deliveryData[index].showDetails = !deliveryData[index].showDetails;
};

const socket = io('http://localhost:6001');

socket.on('connect', () => {
  console.log('Socket connected:', socket.id);
});

socket.on(`laravel_database_tracking.${deliveryId}`, (data) => {
  console.log('Received location update:', data);
  updateMapLocation(data.latitude, data.longitude);
});

onMounted(async () => {
  try {
    await loadGoogleMapsScript();
    await fetchDeliveryData();
    initializeMap();
  } catch (error) {
    console.error('Error initializing delivery tracking:', error);
  }
});
</script>

<style>
/* Updated Table Styles */
.table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 1rem;
  text-align: left;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
  padding: 16px;
}

.table th {
  background-color: #f4f4f9;
  color: #333;
  font-weight: 600;
  text-transform: uppercase;
}

.table tbody tr {
  transition: background-color 0.3s ease;
}

.table tbody tr:hover {
  background-color: #f9f9ff;
}

.status-delivered {
  color: #27aa80;
  font-weight: bold;
  padding: 4px 8px;
  background-color: #eafaf1;
  border-radius: 4px;
  display: inline-block;
}

.status-in-transit {
  color: #f1c232;
  font-weight: bold;
  padding: 4px 8px;
  background-color: #fff8e0;
  border-radius: 4px;
  display: inline-block;
}

/* Details Row Enhancements */
.details-row td {
  background-color: #fafbfc;
  color: #555;
  padding: 20px;
}

/* Enhanced Progress Bar */
.progress-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  position: relative;
}

.milestone {
  flex: 1;
  text-align: center;
  position: relative;
}

.circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  margin: 0 auto;
  transition: all 0.3s ease;
}

.milestone.completed .circle {
  background-color: #27aa80;
  box-shadow: 0 0 10px #27aa80;
}

.milestone.active .circle {
  background-color: #f1c232;
}

.connector {
  width: 100%;
  height: 4px;
  background-color: #ccc;
  position: absolute;
  top: 50%;
  left: -50%;
  z-index: -1;
  transition: all 0.3s ease;
}

.milestone.completed ~ .connector {
  background-color: #27aa80;
}

/* Responsive Enhancements */
@media (max-width: 768px) {
  .table th,
  .table td {
    padding: 12px;
    font-size: 0.9rem;
  }

  .progress-bar {
    flex-wrap: wrap;
    justify-content: center;
  }

  .milestone {
    margin: 10px 0;
  }

  .circle {
    width: 30px;
    height: 30px;
  }
}
</style>
