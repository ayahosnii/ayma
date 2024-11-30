<script setup>
import DefaultLayoutWithVerticalNav from './components/DefaultLayoutWithVerticalNav.vue'
import Loading from '../components/Loading.vue'
import { useState, nextTick, onMounted, ref } from '#imports'
import { useRouter } from 'vue-router'

const loading = useState('loading', () => true) // Start with loading being true
const isLoggedIn = ref(true) // Initialize with false

const router = useRouter()

// Check for login status only on the client side
onMounted(() => {
  if (!localStorage.getItem('authToken')) {
    isLoggedIn.value = false
  }else {
    isLoggedIn.value = true
  }
})

// Trigger loading state during navigation
router.beforeEach((to, from, next) => {
  // Only set loading to true if user is not logged in
  if (!isLoggedIn.value) {
    loading.value = true
  }
  next()
})

router.afterEach(async () => {
  await nextTick() // Wait for next DOM update before setting loading to false
  loading.value = false
})

// Ensure loading is shown on the initial mount, but only if not logged in
onMounted(async () => {
  if (!isLoggedIn.value) {
    await nextTick()
    loading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Display loading screen while loading is true and the user is not logged in -->
    <Loading v-if="!isLoggedIn && loading" :loading="loading" />

    <!-- Render the layout only when loading is false or the user is logged in -->
    <div v-if="!loading || isLoggedIn">
      <DefaultLayoutWithVerticalNav>
        <slot />
        </DefaultLayoutWithVerticalNav>
    </div>
  </div>
</template>

<style lang="scss">
// Import the styles for the layout
@use "@layouts/styles/default-layout";
</style>
