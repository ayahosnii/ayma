<script setup>
import UpgradeToPro from '@/components/UpgradeToPro.vue'

const { isMobile } = useDevice()
if (isMobile)
  configStore.appContentLayoutNav = 'vertical'
const router = useRouter()

const checkToken = () => {
  if (!localStorage.getItem('authToken')) {
    router.push('/login');
  }
}

onMounted(() => {
  checkToken()
  setInterval(checkToken, 10 * 60 * 1000)
})
</script>

<template>
  <VApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </VApp>
</template>
