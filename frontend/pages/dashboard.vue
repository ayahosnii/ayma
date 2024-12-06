<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AnalyticsAward from '@/views/dashboard/AnalyticsAward.vue'
import AnalyticsDepositWithdraw from '@/views/dashboard/AnalyticsDepositWithdraw.vue'
import AnalyticsSalesByCountries from '@/views/dashboard/AnalyticsSalesByCountries.vue'
import AnalyticsTotalEarning from '@/views/dashboard/AnalyticsTotalEarning.vue'
import AnalyticsUserTable from '@/views/dashboard/AnalyticsUserTable.vue'
import AnalyticsWeeklyOverview from '@/views/dashboard/AnalyticsWeeklyOverview.vue'
import CardStatisticsVertical from '@core/components/cards/CardStatisticsVertical.vue'
import productImagePlaceholder from '@images/ecommerce/2.png'

const router = useRouter();
const totalSales = ref({});
const totalProfit = ref({});
const orders = ref({});
const newCustomers = ref({});
const loading = ref(true);
const topProducts = ref([]);
const productHighlights = ref([]);
const salesByCountry = ref([]);
const userName = ref('');

onMounted(async () => {
  setTimeout(() => {
    loading.value = false;  // Simulate loading data and stop after 3 seconds
  }, 3000);
  
  const accessToken = localStorage.getItem('authToken');
  if (!accessToken) {
    // Redirect to login if accessToken is missing
    router.push('/login');
  }

  try {
    const response = await fetch('http://127.0.0.1:8000/api/dashboard', {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${accessToken}`,
      },
    });

    const data = await response.json();

    // Log the response to check the structure
    console.log("API Response:", data);

    if (data.status === 'success') {
      const { total_sales, total_profit, orders: orderCount, new_customers: newCustomerCount } = data.data.totalStats;
      userName.value = data.data.userName
      const sales_country = data.data.salesByCountry;
      salesByCountry.value = sales_country

      const topProducts = data.data.topProducts;

      productHighlights.value = topProducts.map(product => {
        const stockAvailability = Math.floor(product.stock); // Simulate stock availability
        const unitsSold = product.units_sold;

        // Calculate progress as the percentage of stock used
        const progress = stockAvailability > 0
          ? Math.min((unitsSold / stockAvailability) * 100, 100)  // Ensure progress doesn't exceed 100%
          : 0;

        return {
          image: productImagePlaceholder, // Use a placeholder or actual product image if available
          name: product.name,
          unitsSold: unitsSold,
          revenue: `$${parseFloat(product.revenue).toFixed(2)}`, // Format the revenue
          stockAvailability: stockAvailability, // Simulate stock availability
          stockAlert: stockAvailability < 20,
          progress: progress, // Add the calculated progress
        };
      });

      // Set other values like totalSales, totalProfit, orders, etc.
      totalSales.value = {
        title: 'Total Sales',
        color: 'secondary',
        icon: 'ri-money-dollar-circle-line', // Dollar sign icon
        stats: `$${total_sales.value.toFixed(2)}`, // Ensure proper number formatting
        statsUnit: 'USD',
        change: total_sales.change !== null ? total_sales.change.toFixed(2) : 'N/A', // Handle null or 0 change
        subtitle: 'Compared to last year',
        period: '2024',
        tooltip: 'Total revenue generated in the current year, compared to last year.',
      };

      totalProfit.value = {
        title: 'Total Profit',
        color: 'success',
        icon: 'ri-line-chart-line', // Upward graph icon
        stats: `$${parseFloat(total_profit.value).toFixed(2)}`, // Proper formatting
        statsUnit: 'USD',
        change: total_profit.change !== null ? total_profit.change.toFixed(2) : 'N/A', // Handle null or 0 change
        subtitle: 'Profit margin analysis',
        period: '2024',
        tooltip: 'Total profit for the current year, including profit margin trends.',
      };

      orders.value = {
        title: 'Orders',
        color: 'primary',
        icon: 'ri-shopping-cart-line',
        stats: orderCount.value,  // Correctly assigning orderCount.value
        statsUnit: 'orders',
        change: orderCount.change !== null ? orderCount.change.toFixed(2) : 'N/A', // Ensure proper formatting
        subtitle: 'Compared to last month',
        period: 'Last 30 days',
        tooltip: 'Total number of orders placed in the last 30 days with comparison to the previous period.',
      };

      newCustomers.value = {
        title: 'New Customers',
        color: 'info',
        icon: 'ri-user-line', // User icon
        stats: newCustomerCount.value,
        statsUnit: 'customers',
        change: newCustomerCount.change !== null ? newCustomerCount.change.toFixed(2) : 'N/A', // Modify based on data
        subtitle: 'Growth in customer base',
        tooltip: 'New customers acquired in the current year and their percentage growth compared to last year.',
      };

    } else {
      console.error("API returned an error:", data);
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
});

</script>

<template>
  <VRow class="match-height">
    <VCol cols="12" md="12">
      <AnalyticsAward :userName="userName" />
    </VCol>

    <VCol cols="12" md="12">
      <VRow class="match-height">
        <VCol cols="12" sm="3">
          <CardStatisticsVertical v-bind="totalSales" :loading="loading" />
        </VCol>
        <VCol cols="12" sm="3">
          <CardStatisticsVertical v-bind="totalProfit" :loading="loading" />
        </VCol>

        <VCol cols="12" sm="3">
          <CardStatisticsVertical v-bind="orders" :loading="loading" />
        </VCol>

        <VCol cols="12" sm="3">
          <CardStatisticsVertical v-bind="newCustomers" :loading="loading" />
        </VCol>
      </VRow>
    </VCol>

    <VCol cols="12" md="6">
      <AnalyticsTotalEarning :topProducts="productHighlights" />
    </VCol>
    <VCol cols="12" md="6">
       <AnalyticsSalesByCountries :salesByCountry="salesByCountry" />
    </VCol>

    <VCol cols="12">
      <AnalyticsUserTable />
    </VCol>

    <VCol cols="12" md="6">
      <AnalyticsWeeklyOverview />
    </VCol>

    <VCol cols="12" md="6">
      <AnalyticsDepositWithdraw />
    </VCol>
  </VRow>
</template>
