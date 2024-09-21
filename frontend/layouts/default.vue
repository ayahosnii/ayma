<script setup>
import DefaultLayoutWithVerticalNav from './components/DefaultLayoutWithVerticalNav.vue'
import Loading from '../components/Loading.vue'
import { useState, nextTick, onMounted } from '#imports'
import { useRouter } from 'vue-router'

// Manage loading state using Nuxt's global state and router hooks
const loading = useState('loading', () => true) // Start with loading being true

const router = useRouter()

// Trigger loading state during navigation
router.beforeEach((to, from, next) => {
  loading.value = true
  next()
})

router.afterEach(async () => {
  await nextTick() // Wait for next DOM update before setting loading to false
  loading.value = false
})

// Ensure loading is shown on the initial mount
onMounted(async () => {
  await nextTick()
  loading.value = false
})
</script>

<template>
  <div>
    <!-- Display loading screen while loading is true -->
    <Loading :loading="loading" />

    <!-- Render the layout only when loading is false -->
    <div v-if="!loading">
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
