<script setup>
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Statistics array with placeholders for product count
const statistics = ref([
  {
    title: 'Sales',
    stats: '245k',
    icon: 'ri-pie-chart-2-line',
    color: 'primary',
  },
  {
    title: 'Customers',
    stats: '12.5k',
    icon: 'ri-group-line',
    color: 'success',
  },
  {
    title: 'Products',
    stats: 'Loading...', // Placeholder for product count
    icon: 'ri-apple-fill',
    color: 'warning',
  },
  {
    title: 'Revenue',
    stats: '$88k',
    icon: 'ri-money-dollar-circle-line',
    color: 'info',
  },
])

const fetchProductCount = async () => {
  try {
    const token = localStorage.getItem('authToken')
    const productsResponse = await axios.get(`${BASE_URL}/count-products`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    const productCount = productsResponse.data.countProducts
    // Update the statistics array with the fetched product count
    statistics.value = statistics.value.map(item =>
      item.title === 'Products' ? { ...item, stats: `${productCount}` } : item
    )
  } catch (error) {
    console.error('Error fetching product count:', error)
  }
}

onMounted(() => {
  fetchProductCount()
})
</script>

<template>
  <VCard title="Transactions">
    <template #subtitle>
      <p class="text-body-1 mb-0">
        <span class="d-inline-block font-weight-medium text-high-emphasis">Total 48.5% Growth</span> <span class="text-high-emphasis">😎</span> this month
      </p>
    </template>

    <template #append>
      <IconBtn class="mt-n5">
        <VIcon
          color="high-emphasis"
          icon="ri-more-2-line"
        />
      </IconBtn>
    </template>

    <VCardText class="pt-10">
      <VRow>
        <VCol
          v-for="item in statistics"
          :key="item.title"
          cols="12"
          sm="6"
          md="3"
        >
          <div class="d-flex align-center gap-x-3">
            <VAvatar
              :color="item.color"
              rounded
              size="40"
              class="elevation-2"
            >
              <VIcon
                size="24"
                :icon="item.icon"
              />
            </VAvatar>

            <div class="d-flex flex-column">
              <div class="text-body-1">
                {{ item.title }}
              </div>
              <h5 class="text-h5">
                {{ item.stats }}
              </h5>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>
