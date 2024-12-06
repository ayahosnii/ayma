<template>
  <div v-if="loading" class="loading-container">
    <div class="spinner"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  }
})
</script>

<style scoped>
.loading-container {
  position: fixed;
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.5);
  block-size: 100vh;
  inline-size: 100vw;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.spinner {
  border: 6px solid #f3f3f3;
  border-radius: 50%;
  animation: spin 2s linear infinite;
  block-size: 40px;
  border-block-start: 6px solid #ffd600;
  inline-size: 40px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
