<template>
  <div>
    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">Slug</th>
          <th class="text-uppercase text-center">Description</th>
          <th class="text-uppercase text-center">Image</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="item in categories" :key="item.id">
          <td class="text-center">{{ item.name }}</td>
          <td class="text-center">{{ item.slug }}</td>
          <td class="text-center">{{ item.description }}</td>
          <td class="text-center">
            <img :src="getImageUrl(item.image)" alt="Category Image" width="50" height="50" />
          </td>
          <td class="text-center">
            <VBtn size="small" title="Edit" type="edit" color="warning" @click="openEditModal(item)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
            <VBtn size="small" title="Delete" type="delete" color="error" @click="openDeleteModal(item)"><i class="ri-delete-bin-line"></i></VBtn>
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
        <VCardTitle>Edit Category</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editCategory.name"
                  label="Name"
                  :rules="[v => !!v || 'Name is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editCategory.slug"
                  label="Slug"
                  :rules="[v => !!v || 'Slug is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editCategory.description"
                  label="Description"
                  :rules="[v => !!v || 'Description is required']"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editCategory.image"
                  label="Image URL"
                  :rules="[v => !!v || 'Image URL is required']"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" text @click="closeEditModal">Cancel</VBtn>
            <VBtn color="success" text @click="updateCategory">Save</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Category</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this category?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteCategory">Delete</VBtn>
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

const categories = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);

const editCategory = ref({ id: '', name: '', slug: '', description: '', image: '' });
const deleteCategoryId = ref(null);

const fetchCategories = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/categories?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      categories.value = response.data;
      totalPages.value = response.data.meta.last_page;
      currentPage.value = response.data.meta.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const getImageUrl = (path) => {
  return path ? `${BASE_URL}/storage/${path}` : 'placeholder_image_url'; // Placeholder if no image
};

const openEditModal = (item) => {
  editCategory.value = { ...item };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateCategory = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.put(`${BASE_URL}/categories/${editCategory.value.id}`, {
        name: editCategory.value.name,
        slug: editCategory.value.slug,
        description: editCategory.value.description,
        image: editCategory.value.image,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      fetchCategories(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update category:', error);
  }
};

const openDeleteModal = (item) => {
  deleteCategoryId.value = item.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteCategory = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteCategoryId.value) {
      await axios.delete(`${BASE_URL}/categories/${deleteCategoryId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      fetchCategories(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete category ID missing');
    }
  } catch (error) {
    console.error('Failed to delete category:', error);
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => fetchCategories(currentPage.value));
watch(currentPage, (newPage) => fetchCategories(newPage));
</script>
