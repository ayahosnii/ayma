<script setup>
import { onMounted } from "vue";

// Sections components
import BaseLayout from "../../components/BaseLayout.vue";
import View from "../../components/View.vue";

// Modals page components
import SimpleModal from "./components/SimpleModal.vue";

// Modals page components codes
import { simpleModalCode } from "./components/codes";

//nav-pills


</script>
<template>
  <BaseLayout
    title="Modals"
    :breadcrumb="[
      { label: 'Attention Catchers', route: '#' },
      { label: 'Modals' },
    ]"
  >
    <View title="Simple Modal" :code="simpleModalCode" id="simple-modal">
      <SimpleModal />
    </View>
  </BaseLayout>
</template>
