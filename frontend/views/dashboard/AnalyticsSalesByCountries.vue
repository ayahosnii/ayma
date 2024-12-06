<script setup>
defineProps({
  salesByCountry: {
    type: Array,
    required: true
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
              :color="'primary'"  
              variant="tonal"
              size="40"
            >
              {{ data.shipping_country.slice(0, 2).toUpperCase() }} <!-- Display the first two letters of the country as abbreviation -->
            </VAvatar>
          </template>

          <VListItemTitle class="mb-1 d-flex align-center">
            <h6 class="text-h6">
              ${{ parseFloat(data.sales).toFixed(2) }} <!-- Format sales amount as currency -->
            </h6>
            <VIcon
              size="24"
              :color="data.percentage.charAt(0) === '+' ? 'success' : 'error'" 
              class="mx-1"
            >
              {{ data.percentage.charAt(0) === '+' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line' }}
            </VIcon>
            <div
              :class="`${data.percentage.charAt(0) === '+' ? 'text-success' : 'text-error'}`"
              class="text-body-1"
            >
              {{ data.percentage }} <!-- Display percentage change -->
            </div>
          </VListItemTitle>

          <VListItemSubtitle class="text-body-1 me-2">
            {{ data.shipping_country }} <!-- Display country name -->
          </VListItemSubtitle>

          <template #append>
            <div>
              <h6 class="text-h6 mb-1">
                {{ data.sales }} <!-- Display sales number -->
              </h6>
              <div class="text-body-2 text-disabled text-end">
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
</style>
