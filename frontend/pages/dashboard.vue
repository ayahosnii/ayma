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

const totalProfit = {
  title: 'Total Profit',
  color: 'secondary',
  icon: 'ri-pie-chart-2-line',
  stats: '$25.6k',
  change: 42,
  subtitle: 'Weekly Project',
}

const totalSales = {
  title: 'Total Sales',
  color: 'secondary',
  icon: 'ri-bar-chart-line',
  stats: '$862',
  statsUnit: 'units',
  change: 18,
  changeType: 'negative',
  subtitle: 'Compared to last year',
  period: '2024',
  tooltip: 'Total sales for the current year with a comparison to the previous year.',
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
          <AnalyticsTotalProfitLineCharts />
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
          <AnalyticsBarCharts />
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
