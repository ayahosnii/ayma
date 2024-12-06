<script setup>
import trophy from '@images/avatars/avatar-12.png'
defineProps({
  userName: {
    type: String,
  }
});

</script>

<template>
  <VCard class="position-relative">
    <VCardText>
      <div class="mb-2">
        <h5 class="text-h5">
          Welcome Back {{ userName }}! <span class="text-high-emphasis">🎉</span>
        </h5>
      </div>
    </VCardText>

    <!-- Trophy -->
    <VImg
      :src="trophy"
      class="trophy"
    />
  </VCard>
</template>

<style lang="scss">
.trophy {
  position: absolute;
  inline-size: 4.188rem;
  /* Adjust positioning to move it outside */
  inset-block-start: -1rem; /* Move it upward */
  inset-inline-end: -1rem; /* Move it to the right */
}

.v-card {
  /* Optional: Add a margin or padding to ensure spacing */
  margin-block-start: 1rem;
}
</style>
