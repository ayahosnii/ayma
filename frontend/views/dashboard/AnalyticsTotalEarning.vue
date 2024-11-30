<script setup>
defineProps({
  topProducts: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Total Earning Analytics</VCardTitle>
    </VCardItem>

    <VCardText>
      <VList class="card-list">
        <VListItem
          v-for="product in topProducts"
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
