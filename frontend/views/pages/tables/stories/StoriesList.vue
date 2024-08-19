<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/stories/add"><i class="ri-add-circle-line"></i> Add Story</VBtn>
        
        <tr>
          <th class="text-uppercase text-center">Story Title</th>
          <th class="text-uppercase text-center">Level</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="story in stories" :key="story.id">
          <td class="text-center">{{ story.title }}</td>
          <td class="text-center">{{ story.level.name }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" type="edit" color="warning" @click="openEditModal(story)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
            <VBtn size="small" title="Story Slides" type="view" color="success" @click="viewStorySlides(story)"><i class="ri-image-line"></i></VBtn>&nbsp;
            <VBtn size="small" title="Story Sounds" type="view" color="secondary" @click="viewStorySounds(story)"><i class="ri-folder-music-line"></i></VBtn>&nbsp;
            <VBtn size="small" title="Delete" type="delete" color="error" @click="openDeleteModal(story)"><i class="ri-delete-bin-line"></i></VBtn>&nbsp;
          </td>
        </tr>
      </tbody>
    </VTable>
    <VPagination
      v-model="currentPage"
      :length="totalPages"
      @page-change="onPageChange"
    />

    <VDialog v-model="editModal" max-width="800px">
      <VCard>
        <VCardTitle>Edit Story</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editStory.title"
                  label="Title"
                  :rules="[v => !!v || 'Title is required']"
                  required
                />
              </VCol>
              <VCol cols="12">
                <VSelect
                  v-model="editStory.level_id"
                  :items="levels"
                  label="Level"
                  item-text="name"
                  item-value="id"
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
            <VBtn color="success" text @click="updateStory">Save</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Story</VCardTitle>
        <VCardText>Are you sure you want to delete this story?</VCardText>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteStory">Delete</VBtn>
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

const stories = ref([]);
const levels = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);

const editStory = ref({
  id: '',
  title: '',
  level_id: '',
});
const deleteStoryId = ref(null);

const fetchStories = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/stories?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      stories.value = response.data.stories;
      totalPages.value = response.data.stories.last_page;
      currentPage.value = response.data.stories.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch stories:', error);
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

      console.log(response);
      levels.value = response.data.data;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch levels:', error);
  }
};

const openEditModal = (story) => {
  editStory.value = { ...story, level_id: story.level.id };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateStory = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.put(`${BASE_URL}/stories/${editStory.value.id}`, {
        title: editStory.value.title,
        level_id: editStory.value.level_id,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      fetchStories(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update story:', error);
  }
};

const openDeleteModal = (story) => {
  deleteStoryId.value = story.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteStory = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteStoryId.value) {
      const response = await axios.delete(`${BASE_URL}/stories/${deleteStoryId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      console.log(response);
      fetchStories(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete story ID missing');
    }
  } catch (error) {
    console.error('Failed to delete story:', error);
  }
};

const viewStorySounds = (story) => {
  window.location.href = `/stories/sounds/${story.id}`;
};

const viewStorySlides = (story) => {
  window.location.href = `/stories/slides/${story.id}`;
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchStories(currentPage.value);
  fetchLevels();
});

watch(currentPage, (newPage) => fetchStories(newPage));
</script>

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}
</style>
