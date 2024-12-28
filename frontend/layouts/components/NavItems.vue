<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import { BASE_URL } from '@/config/apiConfig';
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';

import axios from 'axios';
import { onMounted, ref } from 'vue';

const storiesCount = ref();
const levelsCount = ref();
const categoriesCount = ref();
const productsCount = ref();
const productsmongoCount = ref();
const ordersCount = ref();
const suppliersCount = ref(); 
const inventoryrecordsCount = ref(); 
const deliveriesCount = ref(); 


const fetchCounts = async () => {
  try {
    const token = localStorage.getItem('authToken');

    // Fetch categories count
    const categoriesResponse = await axios.get(`${BASE_URL}/count-categories`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    categoriesCount.value = categoriesResponse.data.count;

    // Fetch products count
    const productsResponse = await axios.get(`${BASE_URL}/count-products`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    productsCount.value = productsResponse.data.countProducts;

    // Fetch products Mongo count
    // const productsmongoResponse = await axios.get(`${BASE_URL}/count-products-mongo`, {
    //   headers: {
    //     Authorization: `Bearer ${token}`,
    //   },
    // });

    // productsmongoCount.value = productsmongoResponse.data.countProductsMongo;

    
    const ordersResponse = await axios.get(`${BASE_URL}/count-orders`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    ordersCount.value = ordersResponse.data.countOrders;

    //Suppliers Count
    const suppliersResponse = await axios.get(`${BASE_URL}/count-suppliers`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    suppliersCount.value = suppliersResponse.data.countSuppliers;

    //Inventory Reocrds Count
    const inventoryrecordsResponse = await axios.get(`${BASE_URL}/count-inventoryrecords`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    inventoryrecordsCount.value = inventoryrecordsResponse.data.countInvetoryRecords;

    //Deliveries Count
    const deliveriesResponse = await axios.get(`${BASE_URL}/count-deliveries`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    deliveriesCount.value = deliveriesResponse.data.countDeliveries;

  } catch (error) {
    console.error('Error fetching counts:', error);
  }
};



onMounted(() => {
  fetchCounts();
});
</script>

<template>
  <VerticalNavLink
    :item="{
        title: 'Dashboards',
        icon: 'ri-home-2-fill',
        to: '/dashboard',
      }"
  />
  <!-- 👉 Front Pages -->
  <VerticalNavGroup
    :item="{
      title: 'Front Pages',
      icon: 'ri-file-copy-line',
      badgeContent: 'Pro',
      badgeClass: 'bg-light-primary text-primary',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Landing',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/front-pages/landing-page',
        target: '_blank',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Pricing',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/front-pages/pricing',
        target: '_blank',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Payment',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/front-pages/payment',
        target: '_blank',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Checkout',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/front-pages/checkout',
        target: '_blank',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Help Center',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/front-pages/help-center',
        target: '_blank',
      }"
    />
  </VerticalNavGroup>

  <!-- 👉 Apps & Pages -->
  <VerticalNavSectionTitle
    :item="{
      heading: 'Apps & Pages',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Categories',
      icon: 'ri-function-fill',
      to: '/categories/list',
      badgeContent: categoriesCount,
      badgeClass: 'bg-secondary-darken-1',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Products',
      icon:'ri-apple-fill',
      to: '/products/list',
      badgeContent: productsCount,
      badgeClass: 'bg-secondary-darken-1',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Suppliers',
      badgeContent: suppliersCount,
      icon:' ri-truck-fill',
      to: '/suppliers/list',
      badgeClass: 'bg-secondary-darken-1',
    }"
  />
  <VerticalNavLink
    :item="{
    title: 'Shipping Companies',
    badgeContent: suppliersCount,
    icon: 'ri-building-fill',
    to: '/shipping-companies/list',
    badgeClass: 'bg-secondary-darken-1',
  }"
  />

  <VerticalNavLink
    :item="{
      title: 'Orders',
      badgeContent: ordersCount,
      icon:'ri-shopping-cart-2-fill',
      to: '/orders/list',
      // badgeContent: 'New',
      badgeClass: 'bg-secondary-darken-1',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Inventory',
      badgeContent: inventoryrecordsCount,
      badgeClass: 'bg-secondary-darken-1',
      icon: 'ri-archive-drawer-fill',
      to: '/inventory/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Tracking',
      badgeContent: deliveriesCount,
      badgeClass: 'bg-secondary-darken-1',
      icon: 'ri-gps-fill',
      to: '/deliveries/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Users',
      badgeContent: deliveriesCount,
      badgeClass: 'bg-secondary-darken-1',
      icon: 'ri-user-fill',
      to: '/users/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Roles',
      badgeContent: deliveriesCount,
      badgeClass: 'bg-secondary-darken-1',
      icon: 'ri-user-settings-fill',
      to: '/roles/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Sales Analysis',
      badgeContent: 0,
      badgeClass: 'bg-secondary-darken-1',
      icon: 'ri-line-chart-fill',
      to: '/sales-analysis/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Calendar',
      icon: 'ri-calendar-line',
      href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/apps/calendar',
      target: '_blank',
      badgeContent: 'Pro',
      badgeClass: 'bg-light-primary text-primary',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Account Settings',
      icon: 'ri-user-settings-line',
      to: '/account-settings',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Login',
      icon: 'ri-login-box-line',
      to: '/login',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Register',
      icon: 'ri-user-add-line',
      to: '/register',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Error',
      icon: 'ri-information-line',
      to: '/no-existence',
    }"
  />

  <!-- 👉 User Interface -->
  <VerticalNavSectionTitle
    :item="{
      heading: 'User Interface',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Typography',
      icon: 'ri-text',
      to: '/typography',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Icons',
      icon: 'ri-remixicon-line',
      to: '/icons',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Cards',
      icon: 'ri-bar-chart-box-line',
      to: '/cards',
    }"
  />

  <!-- 👉 Forms & Tables -->
  <VerticalNavSectionTitle
    :item="{
      heading: 'Forms & Tables',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Form Layouts',
      icon: 'ri-layout-4-line',
      to: '/form-layouts',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Form Validation',
      icon: 'ri-checkbox-multiple-line',
      href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/forms/form-validation',
      target: '_blank',
      badgeContent: 'Pro',
      badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Form Wizard',
      icon: 'ri-git-commit-line',
      href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/forms/form-wizard-numbered',
      target: '_blank',
      badgeContent: 'Pro',
      badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Tables',
      icon: 'ri-table-alt-line',
      to: '/tables',
    }"
  />

  <!-- 👉 Others -->
  <VerticalNavSectionTitle
    :item="{
      heading: 'Others',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Access Control',
      icon: 'ri-shield-line',
      href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/access-control',
      target: '_blank',
      badgeContent: 'Pro',
      badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Documentation',
      icon: 'ri-article-line',
      href: 'https://demos.themeselection.com/materio-vuetify-vuejs-admin-template/documentation/',
      target: '_blank',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Raise Support',
      href: 'https://github.com/themeselection/materio-vuetify-nuxtjs-admin-template-free/issues',
      icon: 'ri-lifebuoy-line',
      target: '_blank',
    }"
  />
</template>


