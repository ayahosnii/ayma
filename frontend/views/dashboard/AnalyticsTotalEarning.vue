<script setup>
import product1 from '@images/ecommerce/2.png'
import product2 from '@images/ecommerce/2.png'
import product3 from '@images/ecommerce/2.png'

const productHighlights = [
  {
    image: product1,
    name: 'Stylish Backpack',
    unitsSold: 1200,
    revenue: '$24,895.65',
    stockAvailability: 20, // Remaining stock
    stockAlert: true, // Low stock alert
    progress: 80, // Stock usage percentage
  },
  {
    image: product2,
    name: 'Wireless Headphones',
    unitsSold: 850,
    revenue: '$18,650.20',
    stockAvailability: 50,
    stockAlert: false,
    progress: 60,
  },
  {
    image: product3,
    name: 'Smart Watch',
    unitsSold: 450,
    revenue: '$12,450.80',
    stockAvailability: 10,
    stockAlert: true, // Low stock alert
    progress: 90,
  },
]
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Product Highlights & Sales Overview</VCardTitle>

      <template #append>
        <div class="me-n3">
          <MoreBtn />
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <VList class="card-list">
        <VListItem
          v-for="product in productHighlights"
          :key="product.name"
        >
          <!-- Product Image -->
          <template #prepend>
            <VAvatar
              rounded
              :image="product.image"
              variant="tonal"
            />
          </template>

          <!-- Product Name and Revenue -->
          <div>
            <VListItemTitle class="font-weight-medium">
              {{ product.name }}
            </VListItemTitle>
            <VListItemSubtitle class="text-body-1">
              Revenue: {{ product.revenue }}
            </VListItemSubtitle>
          </div>

          <!-- Units Sold and Stock Availability -->
          <template #append>
            <div>
              <h6 class="text-h6 mb-2">
                Units Sold: {{ product.unitsSold }}
              </h6>
              <p
                class="text-body-2"
                :class="{ 'text-danger': product.stockAlert }"
              >
                Stock: {{ product.stockAvailability }}
                <span v-if="product.stockAlert">(Low Stock)</span>
              </p>
              <VProgressLinear
                :color="product.stockAlert ? 'error' : 'primary'"
                :model-value="product.progress"
                rounded-bar
                rounded
              />
            </div>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 1.5rem;
}

.text-danger {
  color: #d32f2f; /* Red for low stock alert */
}
</style>
