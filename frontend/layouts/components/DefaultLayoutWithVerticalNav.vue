<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { io } from 'socket.io-client';  // Import Socket.io client
import NavItems from '@/layouts/components/NavItems.vue';
import logo from '@images/logo.svg?raw';
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue';
import Footer from '@/layouts/components/Footer.vue';
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue';
import UserProfile from '@/layouts/components/UserProfile.vue';
import Notification from '@/layouts/components/Notification.vue';

// Notification state
const notificationCount = ref(0);  // Count of unread notifications
const dropdownVisible = ref(false); // Whether the notification dropdown is visible
const notifications = ref([]); // Array to hold notification data

// Setup Socket.io connection
const socket = io('http://localhost:6001');

socket.on('laravel_database_orders', (data) => {
  console.log('Notification received:', data.message);
  // Ensure notifications and count are updated reactively
  notifications.value.push({
    product_name: data.message || "Unknown Product",
    user_name: data.user_name || "Unknown User", // Add the user name
    total_amount: data.total_amount || "Unknown Stock"
  });
  notificationCount.value = notifications.value.length; // Update the count
});

// Handle incoming messages
socket.on('connect', () => {
  console.log('Socket connected:', socket.id);
});

socket.on('disconnect', () => {
  console.log('Socket disconnected');
});

// Toggle visibility of the notification dropdown
const toggleDropdown = () => {
  dropdownVisible.value = !dropdownVisible.value;
  console.log('Dropdown visibility:', dropdownVisible.value);
};

// Close the dropdown when clicked outside
const closeDropdown = () => {
  dropdownVisible.value = false;
};

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('click', (e) => {
      if (!e.target.closest('.notification-dropdown') && !e.target.closest('.notification-icon')) {
        closeDropdown();
      }
    });
  }
});

onBeforeUnmount(() => {
  socket.off('message'); // Remove the event listener when the component is destroyed
});
</script>

<template>
  <VerticalNavLayout>
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn class="ms-n3 d-lg-none" @click="toggleVerticalOverlayNavActive(true)">
          <VIcon icon="ri-menu-line" />
        </IconBtn>

        <div class="d-flex align-center cursor-pointer" style="user-select: none;">
          <IconBtn>
            <VIcon icon="ri-search-line" />
          </IconBtn>
          <span class="d-none d-md-flex align-center text-disabled">
            <span class="me-3">Search</span>
            <span class="meta-key">&#8984;K</span>
          </span>
        </div>

        <VSpacer />

        <!-- Pass notifications and count as props to the Notification component -->
        <Notification class="me-2" :notifications="notifications" :notificationCount="notificationCount" />

        <NavbarThemeSwitcher class="me-2" />
        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <NuxtLink to="/" class="app-logo app-title-wrapper">
        <h1 class="font-weight-medium leading-normal text-xl text-uppercase">
          AYMA POS System
        </h1>
      </NuxtLink>

      <IconBtn class="d-block d-lg-none" @click="toggleIsOverlayNavActive(false)">
        <VIcon icon="ri-close-line" />
      </IconBtn>
    </template>

    <template #vertical-nav-content>
      <NavItems />
    </template>

    <slot />

    <!-- Footer -->
    <template #footer>
      <Footer />
    </template>
  </VerticalNavLayout>
</template>
