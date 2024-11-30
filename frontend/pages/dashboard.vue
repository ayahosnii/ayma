<script setup>
import AnalyticsAward from '@/views/dashboard/AnalyticsAward.vue'
import AnalyticsBarCharts from '@/views/dashboard/AnalyticsBarCharts.vue'
import AnalyticsDepositWithdraw from '@/views/dashboard/AnalyticsDepositWithdraw.vue'
import AnalyticsSalesByCountries from '@/views/dashboard/AnalyticsSalesByCountries.vue'
import AnalyticsTotalEarning from '@/views/dashboard/AnalyticsTotalEarning.vue'
import AnalyticsTotalProfitLineCharts from '@/views/dashboard/AnalyticsTotalProfitLineCharts.vue'
import AnalyticsTransactions from '@/views/dashboard/AnalyticsTransactions.vue'
import AnalyticsUserTable from '@/views/dashboard/AnalyticsUserTable.vue'
import AnalyticsWeeklyOverview from '@/views/dashboard/AnalyticsWeeklyOverview.vue'
import CardStatisticsVertical from '@core/components/cards/CardStatisticsVertical.vue'

const router = useRouter();

onMounted(() => {
  const accessToken = localStorage.getItem('authToken');
  if (!accessToken) {
    // Redirect to login if accessToken is missing
    router.push('/login');
  }
});


const totalSales = {
  title: 'Total Sales',
  color: 'secondary',
  icon: 'ri-money-dollar-circle-line', // Dollar sign icon
  stats: '$862k',
  statsUnit: 'USD',
  change: 18,
  changeType: 'negative', // 'positive' or 'negative'
  subtitle: 'Compared to last year',
  period: '2024',
  tooltip: 'Total revenue generated in the current year, compared to last year.',
};
const totalProfit = {
  title: 'Total Profit',
  color: 'success',
  icon: 'ri-line-chart-line', // Upward graph icon
  stats: '$245k',
  statsUnit: 'USD',
  change: 5,
  changeType: 'positive', // 'positive' or 'negative'
  subtitle: 'Profit margin analysis',
  period: '2024',
  tooltip: 'Total profit for the current year, including profit margin trends.',
};
const orders = {
  title: 'Orders',
  color: 'primary',
  icon: 'ri-shopping-cart-line', // Shopping cart icon
  stats: '1,200',
  statsUnit: 'orders',
  change: 10,
  changeType: 'positive', // 'positive' or 'negative'
  subtitle: 'Compared to last month',
  period: 'Last 30 days',
  tooltip: 'Total number of orders placed in the last 30 days with comparison to the previous period.',
};
const newCustomers = {
  title: 'New Customers',
  color: 'info',
  icon: 'ri-user-line', // User icon
  stats: '350',
  statsUnit: 'customers',
  change: 7,
  changeType: 'positive', // 'positive' or 'negative'
  subtitle: 'Growth in customer base',
  period: '2024',
  tooltip: 'New customers acquired in the current year and their percentage growth compared to last year.',
};


</script>

<template>
  <VRow class="match-height">
    <VCol
      cols="12"
      md="12"
    >
      <AnalyticsAward />
    </VCol>



    <VCol
      cols="12"
      md="12"
    >
      <VRow class="match-height">
        <VCol
          cols="12"
          sm="3"
        >
          <CardStatisticsVertical v-bind="totalSales" />
        </VCol>
        <VCol
          cols="12"
          sm="3"
        >
          <CardStatisticsVertical v-bind="totalProfit" />
        </VCol>

        <VCol
          cols="12"
          sm="3"
        >
          <CardStatisticsVertical v-bind="orders" />
        </VCol>

        <VCol
          cols="12"
          sm="3"
        >
          <CardStatisticsVertical v-bind="newCustomers " />
        </VCol>
      </VRow>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsTotalEarning />
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsSalesByCountries />
    </VCol>

    <VCol cols="12">
      <AnalyticsUserTable />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsWeeklyOverview />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsDepositWithdraw />
    </VCol>
  </VRow>
</template>
