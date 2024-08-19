<template>
  <div>
    <VTable>
      <thead>
      <tr>
        <th class="text-uppercase text-center">Questions</th>
        <th class="text-uppercase text-center">Answers</th>
        <th class="text-uppercase text-center">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="item in levels" :key="item.id">
        <td class="text-center" v-html="parseQuestionText(item.text, item.id, item.options)"></td>
        <td class="text-center">
          <v-select
            v-model="selectedOption[item.id]"
            :items="item.options.map(option => option.option)"
            item-text="option"
            item-value="id"
          ></v-select>
        </td>
        <td class="text-center">
          <VBtn size="small" type="edit" color="warning" @click="openEditModal(item.id)">Edit</VBtn>&nbsp;
          <VBtn size="small" type="delete" color="error" @click="openDeleteModal(item)">Delete</VBtn>
        </td>
      </tr>
      </tbody>
    </VTable>
    <VPagination
      v-model="currentPage"
      :length="totalPages"
      @page-change="onPageChange"
    />

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="500px">

      <v-card
        max-width="400"
        prepend-icon="mdi-update"
        text="Your application will relaunch automatically after the update is complete."
        title="Update in progress"
      >
        <template v-slot:actions>
          <v-btn
            class="ms-auto"
            text="Ok"
            @click="dialog = false"
          ></v-btn>
        </template>
      </v-card>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <!-- Delete Modal Content -->
    </VDialog>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { BASE_URL } from '@/config/apiConfig'

const levels = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);

const editLevel = ref({ id: '', text: '', description: '' });
const deleteLevelId = ref(null)

const selectedOption = ref({});

const fetchLevels = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/grammar-questions?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      levels.value = response.data.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;

      response.data.data.forEach(item => {
        selectedOption.value[item.id] = item.options.find(option => option.is_correct === 1)?.option || null;
      });
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch levels:', error);
  }
};

const parseQuestionText = (text, id, options) => {
  return text.replace('{placeholder}', `<select v-model="${selectedOption.value[id]}">
    ${options.map(option => `<option :key="${option.id}" :value="${option.id}">${option.option}</option>`).join('\n')}
  </select>`);
};

const openEditModal = (id) => {
  window.location.href = `grammar-questions/edit/${id}`; // Replace with your specific URL
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateLevel = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.put(`${BASE_URL}/grammar-questions/${editLevel.value.id}`, {
        name: editLevel.value.name,
        description: editLevel.value.description,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      fetchLevels(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update level:', error);
  }
};

const openDeleteModal = (item) => {
  deleteLevelId.value = item.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteLevel = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteLevelId.value) {
      const response = await axios.delete(`${BASE_URL}/grammar-questions/${deleteLevelId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      fetchLevels(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete level ID missing');
    }
  } catch (error) {
    console.error('Failed to delete level:', error);
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => fetchLevels(currentPage.value));
watch(currentPage, (newPage) => fetchLevels(newPage));
</script>

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}
</style>
