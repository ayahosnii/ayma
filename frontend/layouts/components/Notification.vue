<script setup>
import order from '@images/avatars/order.png';

defineProps({
  notifications: Array,
  notificationCount: Number,
});

// Computed property to format the notification count
const displayCount = computed(() => (notificationCount > 9 ? '9+' : notificationCount));
</script>

<template>
  <div class="notification-wrapper">
    <div class="notification-badge">
      <!-- Display the count inside a circle -->
      <div class="circle">
        {{ notificationCount > 9 ? '9+' : notificationCount }}
      </div>
    </div>

    <VAvatar
      color="primary"
      variant="tonal"
    >
      <VIcon icon="ri-notification-line" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="350"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <template v-if="notifications.length === 0">
            <VListItem>
              <VListItemTitle>No new notifications</VListItemTitle>
            </VListItem>
          </template>

          <VList>
            <VListItem v-for="(notification, index) in notifications" :key="index" link>
              <VRow align="center" class="d-flex" no-gutters>
                <VCol cols="md-3">
                  <VImg :src="order" max-width="50px" max-height="50px" />
                </VCol>
                <VCol cols="md-9">
                  <VListItemTitle>{{ notification.product_name }}</VListItemTitle>
                  <p>Total Amount: ${{ notification.total_amount }} <br>
                    Customer: {{ notification.user_name }}</p>
                </VCol>
              </VRow>
            </VListItem>
          </VList>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </div>
</template>

<style scoped>
.notification-wrapper {
  position: relative;
  display: inline-block;
}

.notification-badge {
  position: absolute;
  inset-block-start: 0;
  inset-inline-end: 0;
  transform: translate(-150%, 110%);
}

.circle {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: rgb(230,96,96); /* Error color */
  block-size: 20px; /* Match width for perfect circle */
  color: white;
  font-size: 10px; /* Ensure text fits */
  font-weight: bold;
  inline-size: 20px; /* Adjust size as needed */
}
</style>
