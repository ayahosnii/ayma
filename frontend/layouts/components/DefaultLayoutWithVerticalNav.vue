<script setup>
import { ref, onMounted } from 'vue';
import Echo from 'laravel-echo';
import { io } from 'socket.io-client';
import NavItems from '@/layouts/components/NavItems.vue'
import logo from '@images/logo.svg?raw'
import Pusher from 'pusher-js';
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'

// Notification state
const notificationCount = ref(0);  // Count of unread notifications
const dropdownVisible = ref(false); // Whether the notification dropdown is visible
const notifications = ref([]); // Array to hold notification data

// Echo setup (only runs on the client)
let echo;
onMounted(() => {
  // Ensure this is running only in the browser
  window.Pusher = Pusher;
  if (typeof window !== 'undefined') {
    const echo = new Echo({
      broadcaster: 'pusher',           // Use 'pusher' as the broadcaster
      key: '34ce77c06682a4356d1e',     // Replace with your Pusher app key
      cluster: 'eu',                   // Replace with your Pusher app cluster
      wsHost: 'localhost', // Use localhost or your actual hostname
      wsPort: 6001,                     // The WebSocket port (make sure WebSocket server is running)
      forceTLS: false,                 // Disable TLS for local development
      encrypted: false,                // Don't use encryption for local development
      disableStats: true,              // Disable statistics (optional)
      enableStats: false,              // Disable statistics (optional)
      enabledTransports: ['ws']
    });


    echo.channel('order-channel')
      .listen('OrderUpdated', (event) => {
        console.log(event); // Handle the event data
      });

  }
});

// Toggle visibility of the notification dropdown
const toggleDropdown = () => {
  dropdownVisible.value = !dropdownVisible.value;
};

// Close the dropdown when clicked outside
const closeDropdown = () => {
  dropdownVisible.value = false;
};

// Listen for clicks outside to close the dropdown
onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('click', (e) => {
      if (!e.target.closest('.notification-dropdown') && !e.target.closest('.notification-icon')) {
        closeDropdown();
      }
    });
  }
});
</script>


<template>
  <VerticalNavLayout>
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- 👉 Vertical nav toggle in overlay mode -->
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="ri-menu-line" />
        </IconBtn>

        <!-- 👉 Search -->
        <div
          class="d-flex align-center cursor-pointer"
          style="user-select: none;"
        >
          <!-- 👉 Search Trigger button -->
          <IconBtn>
            <VIcon icon="ri-search-line" />
          </IconBtn>

          <span class="d-none d-md-flex align-center text-disabled">
            <span class="me-3">Search</span>
            <span class="meta-key">&#8984;K</span>
          </span>
        </div>

        <VSpacer />

        <!-- 👉 Notification Icon with Dropdown -->
        <div class="notification-icon" @click="toggleDropdown">
          <IconBtn class="me-2">
            <VIcon icon="ri-notification-line" />
            <!-- Notification badge -->
            <span v-if="notificationCount > 0" class="notification-badge">
              {{ notificationCount }}
            </span>
          </IconBtn>

          <!-- Notification Dropdown -->
          <div
            v-show="dropdownVisible"
            class="notification-dropdown dropdown-menu"
            @click.stop
          >
            <div v-if="notifications.length === 0" class="no-notifications">
              No notifications
            </div>
            <ul v-else>
              <li v-for="(notification, index) in notifications" :key="index" class="dropdown-item">
                <div>
                  <strong>{{ notification.product_name }}</strong>
                  <p>Stock remaining: {{ notification.stock }}</p>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <NavbarThemeSwitcher class="me-2" />
        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <NuxtLink
        to="/"
        class="app-logo app-title-wrapper"
      >
        <h1 class="font-weight-medium leading-normal text-xl text-uppercase">
          AYMA POS System
        </h1>
      </NuxtLink>

      <IconBtn
        class="d-block d-lg-none"
        @click="toggleIsOverlayNavActive(false)"
      >
        <VIcon icon="ri-close-line" />
      </IconBtn>
    </template>

    <template #vertical-nav-content>
      <NavItems />
    </template>

    <slot /> <!-- Pages -->

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}

.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-logo-title {
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.75rem;
    text-transform: uppercase;
  }
}

.notification-icon {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: red;
  color: white;
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 50%;
  min-width: 20px;
  text-align: center;
}

.notification-dropdown {
  position: absolute;
  top: 40px;
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  width: 250px;
  z-index: 1000;
  max-height: 300px;
  overflow-y: auto;
  padding: 10px;
  display: none;
}

.notification-dropdown[style*="display: block"] {
  display: block;
  opacity: 1;
  transform: translateY(10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.notification-dropdown ul {
  padding: 0;
  list-style: none;
  margin: 0;
}

.notification-dropdown .dropdown-item {
  padding: 8px 12px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
}

.notification-dropdown .dropdown-item:hover {
  background-color: #f7f7f7;
}

.no-notifications {
  padding: 10px;
  text-align: center;
  color: #888;
}

</style>
