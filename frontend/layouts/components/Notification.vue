<script setup>
import avatar1 from '@images/avatars/avatar-1.png'

defineProps({
  notifications: Array,
  notificationCount: Number,
});
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    {{notificationCount }}
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
                  <VImg :src="avatar1" max-width="50px" max-height="50px" />
                </VCol>
                <VCol cols="md-7">
                  <VListItemTitle>{{ notification.product_name }}</VListItemTitle>
                  <p>Stock remaining: {{ notification.stock }}</p>
                </VCol>
              </VRow>
            </VListItem>
          </VList>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
