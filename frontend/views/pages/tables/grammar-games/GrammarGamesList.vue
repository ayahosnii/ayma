<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" @click="openAddModal">
          <i class="ri-add-circle-line"></i> Add Game
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Title</th>
          <th class="text-uppercase text-center">Level</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="game in grammarGames" :key="game.id">
          <td class="text-center">{{ game.title }}</td>
          <td class="text-center">{{ game.level.name }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" type="edit" color="warning" @click="openEditModal(game)">
              <i class="ri-edit-fill"></i>
            </VBtn>
            &nbsp;
            <VBtn size="small" title="Delete" type="delete" color="error" @click="openDeleteModal(game)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>
    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />
    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="500px">
      <VCard>
        <VCardTitle>Edit Grammar Game</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editGame.title"
                  label="Title"
                  :rules="[v => !!v || 'Title is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VSelect
                  v-model="editGame.level_id"
                  :items="levels"
                  item-value="id"
                  item-text="name"
                  label="Level"
                  :rules="[v => !!v || 'Level is required']"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" text @click="closeEditModal">Cancel</VBtn>
            <VBtn color="success" text @click="updateGame">Save</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Grammar Game</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this grammar game?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteGame">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
    <!-- Add Game Modal -->
    <VDialog v-model="addModal" max-width="500px">
      <VCard>
        <VCardTitle>Add Grammar Game</VCardTitle>
        <VCardText>
          <VForm ref="addForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="newGame.title"
                  label="Title"
                  :rules="[v => !!v || 'Title is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VSelect
                  v-model="newGame.level_id"
                  :items="levels"
                  item-value="id"
                  item-text="name"
                  label="Level"
                  :rules="[v => !!v || 'Level is required']"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" text @click="closeAddModal">Cancel</VBtn>
            <VBtn color="success" text @click="addGame">Save</VBtn>
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

const grammarGames = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const addModal = ref(false);

const editGame = ref({ id: '', title: '', level_id: '' });
const newGame = ref({ title: '', level_id: '' });
const deleteGameId = ref(null);

const levels = ref([]);

const fetchGames = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/grammar-games?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      grammarGames.value = response.data.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;

      console.log(totalPages.value);
      console.log(currentPage.value);
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch grammar games:', error);
  }
};

const fetchLevels = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/levels`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log('Fetched levels:', response.data.data);
      levels.value = response.data.data;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch levels:', error);
  }
};

const openEditModal = (game) => {
  editGame.value = { ...game };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateGame = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.put(`${BASE_URL}/grammar-games/${editGame.value.id}`, {
        title: editGame.value.title,
        level_id: editGame.value.level_id,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      fetchGames(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update grammar game:', error);
  }
};

const openDeleteModal = (game) => {
  deleteGameId.value = game.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteGame = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteGameId.value) {
      const response = await axios.delete(`${BASE_URL}/grammar-games/${deleteGameId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      fetchGames(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete game ID missing');
    }
  } catch (error) {
    console.error('Failed to delete grammar game:', error);
  }
};

const openAddModal = () => {
  newGame.value = { title: '', level_id: '' }; // Reset form data
  addModal.value = true;
};

const closeAddModal = () => {
  addModal.value = false;
};

const addGame = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.post(`${BASE_URL}/grammar-games`, {
        title: newGame.value.title,
        level_id: newGame.value.level_id,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      fetchGames(currentPage.value);
      closeAddModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to add grammar game:', error);
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchGames(currentPage.value);
  fetchLevels();
});
watch(currentPage, (newPage) => fetchGames(newPage));
</script>

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}
</style>
