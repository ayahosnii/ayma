<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    required: false,
    default: 'primary',
  },
  icon: {
    type: String,
    required: true,
  },
  stats: {
    type: String,
    required: true,
  },
  change: {
    type: Number,
    required: true,
  },
  subtitle: {
    type: String,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false, // Loading state prop to control spinner visibility
  }
})

const isPositive = computed(() => Math.sign(props.change) === 1)
</script>

<template>
  <VCard class="position-relative">
    <!-- Show loading spinner when loading is true -->
    <v-progress-circular
      v-if="loading"
      indeterminate
      color="primary"
      class="loading-spinner"
    />

    <!-- Card Content -->
    <VCardText class="d-flex align-center">
      <VAvatar
        v-if="props.icon"
        size="40"
        :color="props.color"
        class="elevation-2"
      >
        <VIcon
          :icon="props.icon"
          size="24"
        />
      </VAvatar>

      <VSpacer />

      <MoreBtn class="me-n3 mt-n1" />
    </VCardText>

    <VCardText>
      <h6 class="text-h6 mb-1">
        {{ props.title }}
      </h6>

      <div
        v-if="props.change"
        class="d-flex align-center mb-1 flex-wrap"
      >
        <h4 class="text-h4 me-2">
          {{ props.stats }}
        </h4>
        <div
          :class="isPositive ? 'text-success' : 'text-error'"
          class="text-body-1"
        >
          {{ isPositive ? `+${props.change}` : props.change }}%
        </div>
      </div>
      <div class="text-body-2">
        {{ props.subtitle }}
      </div>
    </VCardText>
  </VCard>
</template>

<style scoped>
/* Center the loading spinner in the card */
.loading-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
