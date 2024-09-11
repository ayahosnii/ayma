<script setup>
import CategoriesList from '@/views/pages/tables/categories/CategoriesList.vue';

definePageMeta({
  middleware: 'auth'
});
</script>

<template>
  <div>
    <VCard title="Categories">
      <CategoriesList />
    </VCard>
  </div>
</template>
