<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import { BASE_URL } from '@/config/apiConfig';
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';

import axios from 'axios';
import { onMounted, ref } from 'vue';

const storiesCount = ref();
const levelsCount = ref();

const fetchCounts = async () => {
  try {
    const token = localStorage.getItem('authToken');

    // Fetch stories count
    const storiesResponse = await axios.get(`${BASE_URL}/count-stories`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    storiesCount.value = storiesResponse.data.count;

    // Fetch levels count
    const levelsResponse = await axios.get(`${BASE_URL}/count-levels`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    levelsCount.value = levelsResponse.data.countLevels;
  } catch (error) {
    console.error('Error fetching counts:', error);
  }
};

onMounted(() => {
  fetchCounts();
});
</script>

<template>
  <!-- 👉 Dashboards -->
  <VerticalNavGroup
    :item="{
      title: 'Dashboards',
      badgeContent: '5',
      badgeClass: 'bg-success',
      icon: 'ri-home-smile-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Analytics',
        to: '/dashboard',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'CRM',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/dashboards/crm',
        target: '_blank',
        badgeContent: 'Pro',
        badgeClass: 'bg-light-primary text-primary',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'ECommerce',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/dashboards/ecommerce',
        target: '_blank',
        badgeContent: 'Pro',
        badgeClass: 'bg-light-primary text-primary',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Academy',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/dashboards/academy',
        target: '_blank',
        badgeContent: 'Pro',
        badgeClass: 'bg-light-primary text-primary',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Logistics',
        href: 'https://demos.themeselection.com/materio-vuetify-nuxtjs-admin-template/demo-1/dashboards/logistics',
        target: '_blank',
        badgeContent: 'Pro',
        badgeClass: 'bg-light-primary text-primary',
      }"
    />
  </VerticalNavGroup>

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
      title: 'Stories',
      badgeContent: storiesCount,
      badgeClass: 'bg-success',
      icon: 'ri-git-repository-line',
      href: '/stories/list',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Categories',
      icon: 'ri-function-fill',
      href: '/categories/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Levels',
      badgeContent: levelsCount,
      badgeClass: 'bg-warning',
      icon: 'ri-stack-line',
      href: '/levels/list',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  <VerticalNavGroup
    :item="{
      title: 'Grammar',
      icon: 'ri-menu-search-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Games',
        href: '/grammar-games/list',
        icon: 'ri-puzzle-2-line',
      }"
    />
    <VerticalNavLink
    :item="{
      title: 'Questions',
      icon: 'ri-questionnaire-line',
      href: '/grammar-questions',
      // badgeContent: 'New',
      // badgeClass: 'bg-light-primary text-primary',
    }"
  />
  </VerticalNavGroup>
  <VerticalNavLink
    :item="{
      title: 'Listening Section',
      icon: 'ri-customer-service-line',
      href: '/listening/list',
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


