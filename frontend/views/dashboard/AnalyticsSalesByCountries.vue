<script setup>

defineProps({
  salesByCountry: {
    type: Array,
    required: true,
    default: () => [] // Provide a default empty array
  }
});
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Sales by Countries</VCardTitle>

      <template #append>
        <div class="me-n3">
          <MoreBtn />
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <VList class="card-list">
        <!-- Loop through the salesByCountry array and display data -->
        <VListItem
          v-for="(data, index) in salesByCountry"
          :key="index"
        >
          <template #prepend>
            <VAvatar
              color="primary"  
              variant="tonal"
              size="40"
            >
              {{ data.shipping_country.slice(0, 2).toUpperCase() }} <!-- Display the first two letters of the country as abbreviation -->
            </VAvatar>
          </template>

          <VListItemTitle class="mb-1 d-flex align-center">
            <h6 class="text-h6 me-2">
              ${{ parseFloat(data.sales).toFixed(2) }} <!-- Format sales amount as currency -->
            </h6>
            <VIcon
              size="24"
              :color="data.percentage > 0 ? 'success' : 'error'" 
              class="mx-1"
            >
              {{ data.percentage > 0 ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line' }}
            </VIcon>
            <div
              :class="data.percentage > 0 ? 'text-success' : 'text-error'"
              class="text-body-1"
            >
              {{ data.percentage }}% <!-- Display percentage change with 2 decimals -->
            </div>
          </VListItemTitle>

          <VListItemSubtitle class="text-body-1">
            {{ data.shipping_country }} <!-- Display country name -->
          </VListItemSubtitle>

          <template #append>
            <div class="text-end">
              <h6 class="text-h6 mb-1">
                ${{ parseFloat(data.sales).toFixed(2) }} <!-- Format and display sales -->
              </h6>
              <div class="text-body-2 text-muted">
                Sales
              </div>
            </div>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 0.875rem;
}

.text-success {
  color: var(--v-success-base);
}

.text-error {
  color: var(--v-error-base);
}
</style>
