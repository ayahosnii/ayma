<template>
  <div class="delivery-status">
    <!-- Header Section -->
    <div class="header">
      <h1>Out For Delivery Today</h1>
      <p>The delivery of the parcel is estimated at 10.30 A.M to 11.30 P.M</p>
    </div>

    <!-- Delivery Cards -->
    <div class="delivery-card" v-for="(delivery, index) in deliveries" :key="index">
      <div class="delivery-header">
        <div class="status-date">
          <span class="date">{{ delivery.date }}</span>
          <span :class="['status', delivery.status.toLowerCase().replace(' ', '-')]">
            {{ delivery.status }}
          </span>
        </div>
        <div class="product-name">{{ delivery.productName }}</div>
        <div class="delivery-partner">{{ delivery.deliveryPartner }}</div>
      </div>

      <!-- Enhanced Progress Bar -->
      <div class="progress-container">
        <div class="progress-bar">
          <div
            class="milestone"
            v-for="(step, i) in delivery.timeline"
            :key="i"
            :class="{ completed: i < delivery.currentStep, active: i === delivery.currentStep }"
          >
            <!-- Circle -->
            <div class="circle">
              <span v-if="i < delivery.currentStep" class="checkmark">✔</span>
            </div>
            <!-- Labels -->
            <div class="labels">
              <span class="label">{{ step.status }}</span>
              <span class="date">{{ step.date }}</span>
            </div>
            <!-- Connector Line -->
            <div
              class="connector"
              v-if="i < delivery.timeline.length - 1"
              :class="{ completed: i < delivery.currentStep }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Details Section -->
      <div v-if="delivery.showDetails" class="details-section">
        <div class="table-header">TPS - {{ delivery.trackingCode }}</div>
        <table class="details-table">
          <thead>
          <tr>
            <th>Date & Time</th>
            <th>Location</th>
            <th>Status</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(detail, i) in delivery.details" :key="i">
            <td>{{ detail.dateTime }}</td>
            <td>{{ detail.location }}</td>
            <td>{{ detail.status }}</td>
          </tr>
          </tbody>
        </table>
      </div>

      <!-- Toggle Button -->
      <button class="toggle-button" @click="toggleDetails(index)">
        {{ delivery.showDetails ? "Collapse" : "More details" }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "vue";

const deliveries = reactive([
  {
    date: '20 APR',
    status: 'IN TRANSIT',
    productName: 'Cooler Master MasterLiquid ML240L',
    deliveryPartner: 'Blue Dart',
    trackingCode: 'SP1205',
    currentStep: 3,
    showDetails: false,
    timeline: [
      { status: 'Order Confirmed', date: 'Mon, 19 Dec' },
      { status: 'Processing', date: 'Mon, 19 Dec' },
      { status: 'Shipped', date: 'Tue, 20 Dec' },
      { status: 'Delivered', date: 'Thu, 22 Dec' },
    ],
    details: [
      { dateTime: '20 Apr, 2023 - 12:30 P.M', location: 'Bangalore', status: 'Order processed' },
      { dateTime: '20 Apr, 2023 - 2:30 P.M', location: 'Bangalore', status: 'Delivered to consignee - Delivered' },
      { dateTime: '20 Apr, 2023 - 3:30 P.M', location: '-', status: 'Shipment received at facility - Pending' },
      { dateTime: '20 Apr, 2023 - 4:30 P.M', location: 'Bangalore', status: 'Bag received at facility - In transit' },
    ],
  },
]);

const toggleDetails = (index) => {
  deliveries[index].showDetails = !deliveries[index].showDetails;
};
</script>

<style scoped>
/* General Layout */
.delivery-status {
  font-family: 'Roboto', Arial, sans-serif;
  padding: 20px;
  background-color: #f9fafc;
}

.header {
  text-align: center;
  margin-bottom: 24px;
}

.delivery-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  background: #ffffff;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
}

/* Delivery Header */
.status-date {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.status {
  padding: 6px 12px;
  border-radius: 16px;
  color: #fff;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}

.status.in-transit {
  background-color: #f8b400;
}

.status.delivered {
  background-color: #27aa80;
}

.product-name {
  font-weight: bold;
  font-size: 20px;
  text-align: center;
  margin: 10px 0;
}

.delivery-partner {
  text-align: right;
  color: #757575;
  font-size: 14px;
}

/* Progress Bar */
.progress-container {
  margin: 20px 0;
}

.progress-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}

.milestone {
  text-align: center;
  flex: 1;
  position: relative;
}

.circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #f1c232;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  color: white;
  font-weight: bold;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.milestone.completed .circle {
  background-color: #27aa80;
}

.connector {
  position: absolute;
  top: 50%;
  left: 0;
  width: 100%;
  height: 6px;
  background-color: #f1c232;
  z-index: -1;
}

.milestone.completed ~ .connector {
  background-color: #27aa80;
}

/* Labels */
.labels {
  margin-top: 12px;
}

.label {
  font-size: 16px;
  font-weight: bold;
  color: #888888;
}

.milestone.completed .label {
  color: #000;
}

.date {
  font-size: 14px;
  color: #888888;
}

.milestone.completed .date {
  color: #000;
}

/* Table Section */
.details-section {
  margin-top: 20px;
  background-color: #f8f8f8;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.details-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border: 1px solid #ddd;
}

.details-table th,
.details-table td {
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.details-table th {
  background-color: #f0f0f0;
  color: #555;
}

.details-table td {
  font-size: 14px;
}

/* Toggle Button */
.toggle-button {
  margin-top: 16px;
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 20px; /* Rounded for the pill shape */
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.toggle-button:hover {
  background-color: #0056b3;
}
</style>
