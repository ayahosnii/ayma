<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/categories/add"><i class="ri-add-circle-line"></i> Add Category</VBtn>
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
            <img :src="getImageUrl(item.image)" alt="Category Image" class="slide-image mt-1" />
          </td>
          <td class="text-center">
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(item)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(item)"><i class="ri-delete-bin-line"></i></VBtn>
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
    <VDialog v-model="editModal" max-width="600px">
      <VCard>
        <VCardTitle>Edit Category</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
          <VCol cols="12">
            <VTextField
              v-model="editCategory.name"
              label="Name"
              placeholder="Category Name"
              required
            />
          </VCol>
        </VRow>
          <VRow>
          <VCol cols="12">
            <VTextField
              v-model="editCategory.slug"
              label="Slug"
              placeholder="Category Slug"
            />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="12">
            <VTextField
              v-model="editCategory.description"
              label="Description"
              placeholder="Category Description (optional)"
            />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="12">
            <VFileInput
              v-model="editCategory.image"
              label="Image"
              placeholder="Select an image"
              accept="image/*"
              @change="handleEditImageUpload"
            />
          </VCol>
          </VRow>
          <VRow>
          <VCol cols="12">
            <VSelect
              v-model="editCategory.parent_id"
              label="Choose Parent Category"
              :items="categories2"
              item-title="name"
              item-value="id"
              density="compact"
              class="me-3"
            />
          </VCol>
          </VRow>
          <VRow>
          <VCol cols="12">
            <VTextField
              v-model="editCategory.order"
              label="Order"
              placeholder="Display Order"
              type="number"
              min="0"
            />
          </VCol>
          </VRow>
          <VRow>
          <VCol cols="12">
            <VCheckbox
              v-model="editCategory.is_active"
              label="Is Active?"
              :true-value="1"
              :false-value="0"
            />
          </VCol>
        </VRow>
        </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="updateCategory">Save</VBtn>
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
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
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const categories = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);

const editCategory = ref({ id: '', name: '', slug: '', description: '', image: '', parent_id: null, order: 0, is_active: 1 });
const originalSlug = ref(''); // Keep track of the original slug when editing
const deleteCategoryId = ref(null);
const categories2 = ref([]); // For parent category options

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
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const fetchParentCategories = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const response = await axios.get(`${BASE_URL}/categories`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    categories2.value = response.data; // Populate the categories2 array for the select dropdown
  } catch (error) {
    console.error('Failed to fetch parent categories:', error);
  }
};

const getImageUrl = (path) => {
  return path ? `http://127.0.0.1:8000/storage/${path}` : 'placeholder_image_url'; // Placeholder if no image
};

const generateSlug = (name) => {
  return name
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const openEditModal = (item) => {
  editCategory.value = { ...item, image: '', parent_id: item.parent_id || null };
  originalSlug.value = item.slug; // Save the original slug
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const handleEditImageUpload = (event) => {
  const file = event.target.files[0];
  editCategory.value.image = file;
};

const updateCategory = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const categoryData = {
        name: editCategory.value.name,
        slug: editCategory.value.slug,
        description: editCategory.value.description || '',
        parent_id: editCategory.value.parent_id,
        order: editCategory.value.order,
        is_active: editCategory.value.is_active,
      };

      // Check if the slug has been modified, if not, keep the original
      if (editCategory.value.slug === originalSlug.value) {
        delete categoryData.slug;
      }

      // Only add image if it's a new file, otherwise don't include it
      if (editCategory.value.image instanceof File) {
        const formData = new FormData();
        Object.keys(categoryData).forEach(key => {
          formData.append(key, categoryData[key]);
        });
        formData.append('image', editCategory.value.image);

        await axios.put(`${BASE_URL}/categories/${editCategory.value.id}`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data',
          },
        });
      } else {
        await axios.put(`${BASE_URL}/categories/${editCategory.value.id}`, categoryData, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      }

      $toast.success('Category updated successfully!');
      fetchCategories(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update category:', error);
    $toast.error('Error updating category: ' + (error.response?.data?.message || error.message));
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

      $toast.success('Category deleted successfully!');
      fetchCategories(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete category ID missing');
    }
  } catch (error) {
    console.error('Failed to delete category:', error);
    $toast.error('Error deleting category: ' + (error.response?.data?.message || error.message));
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchCategories(currentPage.value);
  fetchParentCategories(); // Fetch parent categories for the select dropdown
});

watch(currentPage, (newPage) => fetchCategories(newPage));

// Watch the name field and update the slug accordingly
watch(() => editCategory.value.name, (newName) => {
  if (!editModal.value || editCategory.value.slug === originalSlug.value) {
    editCategory.value.slug = generateSlug(newName);
  }
});
</script>


<style scoped>
.slide-image {
  block-size: 100px;
  inline-size: 100px;
  object-fit: cover;
}
</style>
