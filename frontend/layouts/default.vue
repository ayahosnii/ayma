<script setup>
import DefaultLayoutWithVerticalNav from './components/DefaultLayoutWithVerticalNav.vue'
import Loading from '../components/Loading.vue'
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

const loading = ref(true) // Start with loading being true
const isLoggedIn = ref(false) // Initialize with false
const router = useRouter()

// Check for login status on mount
onMounted(() => {
  const authToken = localStorage.getItem('authToken')
  isLoggedIn.value = !!authToken // Convert to boolean
  if (!isLoggedIn.value) {
    router.push('/login') // Redirect to login if not authenticated
  }
  loading.value = false // Stop loading once check is complete
})

// Show loading during navigation
router.beforeEach((to, from, next) => {
  loading.value = true // Trigger loading state
  next()
})

router.afterEach(async () => {
  await nextTick() // Wait for DOM updates
  loading.value = false // Turn off loading
})
</script>

<template>
  <div>
    <!-- Show loading screen if in loading state -->
    <Loading v-if="loading" />

    <!-- Render layout only when user is logged in and not loading -->
    <div v-else>
      <DefaultLayoutWithVerticalNav v-if="isLoggedIn">
        <slot />
      </DefaultLayoutWithVerticalNav>
    </div>
  </div>
</template>

<style lang="scss">
// Import the styles for the layout
@use "@layouts/styles/default-layout";
</style>
