<template>
  <div>
    <VTable>

      <thead>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">Description</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="item in levels" :key="item.id">
          <td class="text-center">{{ item.name }}</td>
          <td class="text-center">{{ item.description }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" type="edit" color="warning" @click="openEditModal(item)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
            <VBtn size="small" title="delete" type="delete" color="error" @click="openDeleteModal(item)"><i class="ri-delete-bin-line"></i></VBtn>
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
      <VCard>
        <VCardTitle>Edit Level</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
            <VTextField
              v-model="editLevel.name"
              label="Name"
              :rules="[v => !!v || 'Name is required']"
              required
            />
            </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
            <VTextField
              v-model="editLevel.description"
              label="Description"
              :rules="[v => !!v || 'Description is required']"
              required
            />
            </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol
          cols="12"
          class="d-flex gap-4"
          >
            <VBtn color="error" text="" @click="closeEditModal">Cancel</VBtn>
            <VBtn color="success" text="" @click="updateLevel">Save</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Level</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this level?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteLevel">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup lang="ts">
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const levels = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);

const editLevel = ref({ id: '', name: '', description: '' });
const deleteLevelId = ref(null);

const fetchLevels = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/levels?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      levels.value = response.data.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;

      console.log(totalPages.value);
      console.log(currentPage.value);
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch levels:', error);
  }
};

const openEditModal = (item) => {
  editLevel.value = { ...item };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateLevel = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.put(`${BASE_URL}/levels/${editLevel.value.id}`, {
        name: editLevel.value.name,
        description: editLevel.value.description,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
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
      const response = await axios.delete(`${BASE_URL}/levels/${deleteLevelId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
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
