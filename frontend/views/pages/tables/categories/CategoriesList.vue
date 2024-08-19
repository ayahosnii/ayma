<template>
  <div>
    <v-data-table
      item-key="id"
      class="elevation-1"
      :headers="headers"
      :items="categories"
      :loading="loading"
      loading-text="Loading... Please wait"
    >
      <template v-slot:item="{ item }">
        <tr :key="item.id">
          <td>{{ item.name }}</td>
          <td class="text-center">
            <v-btn size="small" color="warning">Edit</v-btn>&nbsp;
            <v-btn size="small" color="error">Delete</v-btn>
          </td>
        </tr>
      </template>
    </v-data-table>
    <v-pagination
      v-model="currentPage"
      :length="totalPages"
      @input="onPageChange"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { fetchCategoriesList } from './fetchCategoriesList.ts';

const headers = ref([
  { text: 'Name', value: 'name' },
  { text: 'Action', value: 'action', sortable: false }
]);

const {
  categories,
  currentPage,
  totalPages,
  loading,
  onPageChange
} = fetchCategoriesList();
</script>

<style src="./CategoriesTable.css" scoped></style>
