<template>
  <div v-if="loading" class="loading-container">
    <div class="spinner">Loading...</div>
  </div>
</template>

<script setup>
// Since Vue 3 and Nuxt 3 support <script setup>, you can accept the prop like this:
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
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

.spinner {
  border: 6px solid #f3f3f3;
  border-radius: 50%;
  border-top: 6px solid #3498db;
  width: 40px;
  height: 40px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
