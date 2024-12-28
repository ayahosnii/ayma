<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

let chart;
let ApexCharts;

// Get current month and year
const currentMonth = new Date().getMonth(); // 0-based, Jan = 0
const currentYear = new Date().getFullYear();

const series = ref([]); // Reactive reference for series data
const isDataLoaded = ref(false); // Flag to check if data is loaded

const fetchOrderStatistics = async (token) => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/order-statistics', {
      headers: { Authorization: `Bearer ${token}` },
    });

    // Assign the data directly to series.value
    series.value = response.data.ordersData;

    console.log(series.value); // Debug to verify the data structure
    isDataLoaded.value = true; // Mark data as loaded
  } catch (error) {
    console.error('Failed to fetch order statistics:', error);
  }
};

// Example usage
const token = localStorage.getItem('authToken');
fetchOrderStatistics(token);

onMounted(() => {
  if (typeof window !== 'undefined') {
    // Dynamically import ApexCharts only in the client-side environment
    import('apexcharts').then((module) => {
      ApexCharts = module.default;

      // Watch for data changes
      watch(isDataLoaded, (loaded) => {
        if (loaded && series.value.length) {
          // Filter the data to include only the months up to the current month
          const filteredOrdersData = series.value.slice(0, currentMonth + 1);

          // Find the month with the most orders
          const mostOrdersMonth = getMonthWithMostOrders(filteredOrdersData);

          const options = {
            series: [
              {
                name: 'Orders',
                data: filteredOrdersData,
              },
            ],
            chart: {
              type: 'area',
              height: 350,
              zoom: {
                enabled: false,
              },
            },
            dataLabels: {
              enabled: false,
            },
            stroke: {
              curve: 'smooth',
            },
            title: {
              text: 'Orders per Month',
              align: 'left',
            },
            subtitle: {
              text: `Month with most orders: ${mostOrdersMonth.month} with ${mostOrdersMonth.orders} orders`,
              align: 'left',
            },
            xaxis: {
              type: 'datetime',
              categories: filteredOrdersData.map(([timestamp]) =>
                new Date(timestamp).toLocaleString('default', { month: 'long', year: 'numeric' })
              ),
            },
            yaxis: {
              opposite: true,
            },
            legend: {
              horizontalAlign: 'left',
            },
          };

          chart = new ApexCharts(document.querySelector('#chart'), options);
          chart.render();
        }
      });
    });
  }
});

// Function to find the month with the most orders
function getMonthWithMostOrders(orders) {
  let mostOrders = 0;
  let mostOrdersMonth = '';

  orders.forEach(([timestamp, orderCount], index) => {
    if (orderCount > mostOrders) {
      mostOrders = orderCount;
      mostOrdersMonth = new Date(timestamp).toLocaleString('default', { month: 'long' });
    }
  });

  return { month: mostOrdersMonth, orders: mostOrders };
}
</script>

<template>
  <v-card>
    <v-card-title>
      Area Chart - Monthly Orders
    </v-card-title>
    <v-card-text class="d-flex justify-center align-center">
      <div id="chart"></div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
/* Ensure the chart takes up enough space and is centered */
#chart {
  width: 100%;
  max-width: 900px;
}
</style>
