<template>
  <div>
    <VBtn class="mb-4 ml-4" size="small" color="secondary" @click="openAddModal">
      <i class="ri-add-circle-line"></i> Add Slide
    </VBtn>
    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase text-center">Slide</th>
          <th class="text-uppercase text-center">Context</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="slide in slides" :key="slide.id">
          <td class="text-center">
            <img :src="`${PUBLIC_URL}/storage/${slide.image}`" class="slide-image mt-1" alt="Slide Image" />
          </td>
          <td class="text-center">{{ slide.context }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" type="edit" color="warning" @click="openEditModal(slide)">
              <i class="ri-edit-fill"></i>
            </VBtn>
            &nbsp;
            <VBtn size="small" title="Delete" type="delete" color="error" @click="openDeleteModal(slide)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>
    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Add Modal -->
    <VDialog v-model="isAddModalOpen" max-width="500px">
      <VCard>
        <VCardTitle>Add Slide</VCardTitle>
        <VCardText>
          <VForm ref="addForm">
            <VRow>
              <VCol cols="12">
                <VTextarea
                  v-model="newSlide.context"
                  label="Context"
                  :rules="[v => !!v || 'Context is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VFileInput
                  label="Slide Image"
                  @change="onAddFileChange"
                  prepend-icon="mdi-camera"
                  show-size
                  accept="image/*"
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" @click="closeAddModal">Cancel</VBtn>
            <VBtn color="success" @click="addSlide">Add</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Modal -->
    <VDialog v-model="isEditModalOpen" max-width="500px">
      <VCard>
        <VCardTitle>Edit Slide</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="currentSlide.context"
                  label="Context"
                  :rules="[v => !!v || 'Context is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VFileInput
                  label="Slide Image"
                  @change="onFileChange"
                  prepend-icon="mdi-camera"
                  show-size
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
            <VBtn color="success" @click="updateSlide">Save</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="isDeleteModalOpen" max-width="500px">
      <VCard>
        <VCardTitle>Delete Slide</VCardTitle>
        <VCardText>Are you sure you want to delete this slide?</VCardText>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" @click="deleteSlide">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup lang="ts">
import { BASE_URL, PUBLIC_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const slides = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const route = useRoute();
const storyId = route.params.id;

const isAddModalOpen = ref(false);
const newSlide = ref({ context: '' });
const selectedAddFile = ref(null);

const isEditModalOpen = ref(false);
const currentSlide = ref(null);
const selectedFile = ref(null);

const isDeleteModalOpen = ref(false);
const slideToDelete = ref(null);

const fetchSlides = async (storyId, page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/stories/${storyId}/slides?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      slides.value = response.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch slides:', error);
  }
};

const onPageChange = (page) => {
  fetchSlides(storyId, page);
};

const openAddModal = () => {
  isAddModalOpen.value = true;
};

const closeAddModal = () => {
  isAddModalOpen.value = false;
};

const onAddFileChange = (event) => {
  selectedAddFile.value = event.target.files[0];
};

const addSlide = async () => {
  try {
    const formData = new FormData();
    formData.append('context', newSlide.value.context);
    if (selectedAddFile.value) {
      formData.append('image', selectedAddFile.value);
    }

    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.post(`${BASE_URL}/stories/${storyId}/slides`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      });

      fetchSlides(storyId, currentPage.value);
      closeAddModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to add slide:', error);
  }
};

const openEditModal = (slide) => {
  currentSlide.value = { ...slide };
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const onFileChange = (event) => {
  selectedFile.value = event.target.files[0];
};

const updateSlide = async () => {
  try {
    const formData = new FormData();
    formData.append('context', currentSlide.value.context);
    if (selectedFile.value) {
      formData.append('image', selectedFile.value);
    }

    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.post(`${BASE_URL}/stories/${storyId}/slides/${currentSlide.value.id}`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      });

      fetchSlides(storyId, currentPage.value);
      isEditModalOpen.value = false;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update slide:', error);
  }
};

const openDeleteModal = (slide) => {
  slideToDelete.value = slide;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
};

const deleteSlide = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.delete(`${BASE_URL}/stories/${storyId}/slides/${slideToDelete.value.id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      fetchSlides(storyId, currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to delete slide:', error);
  }
};

onMounted(() => {
  fetchSlides(storyId, currentPage.value);
});
</script>

<style scoped>
.slide-image {
  block-size: 100px;
  inline-size: 100px;
  object-fit: cover;
}
</style>
