<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/categories/add">
          <i class="ri-add-circle-line"></i> Add Category
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">Slug</th>
          <th class="text-uppercase text-center">Description</th>
          <th class="text-uppercase text-center">Image</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <template v-for="parent in parentCategories" :key="parent.id">
          <!-- Parent Category Row -->
          <tr class="parent-category">
            <td @click="toggleChildren(parent.id)" class="text-center font-weight-bold cursor-pointer">{{ parent.name }}</td>
            <td class="text-center">{{ parent.slug }}</td>
            <td class="text-center">{{ parent.description }}</td>
            <td class="text-center">
              <img :src="getImageUrl(parent.image)" alt="Category Image" class="slide-image mt-1" />
            </td>
            <td class="text-center">
              <VBtn size="small" title="Edit" color="warning" @click="openEditModal(parent)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
              <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(parent)"><i class="ri-delete-bin-line"></i></VBtn>
            </td>
          </tr>

          <!-- Child Categories Rows -->
          <tr v-if="isChildrenVisible(parent.id)" v-for="child in parent.children" :key="child.id" class="child-category">
            <td class="text-center">{{ child.name }}</td>
            <td class="text-center">{{ child.slug }}</td>
            <td class="text-center">{{ child.description }}</td>
            <td class="text-center">
              <img :src="getImageUrl(child.image)" alt="Category Image" class="slide-image mt-1" />
            </td>
            <td class="text-center">
              <VBtn size="small" title="Edit" color="warning" @click="openEditModal(child)"><i class="ri-edit-fill"></i></VBtn>&nbsp;
              <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(child)"><i class="ri-delete-bin-line"></i></VBtn>
            </td>
          </tr>
        </template>
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

const parentCategories = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const visibleChildren = ref({});

const editModal = ref(false);
const deleteModal = ref(false);

const editCategory = ref({ id: '', name: '', slug: '', description: '', image: '', parent_id: null, order: 0, is_active: 1 });
const originalSlug = ref(''); // Keep track of the original slug when editing
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

      const categories = response.data;

      // Group categories into parent-child structure
      parentCategories.value = categories.filter(cat => !cat.parent_id).map(parent => ({
        ...parent,
        children: categories.filter(cat => cat.parent_id === parent.id),
      }));

      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const getImageUrl = (path) => {
  return path ? `http://127.0.0.1:8000/storage/${path}` : 'placeholder_image_url'; // Placeholder if no image
};

const toggleChildren = (parentId) => {
  visibleChildren.value[parentId] = !visibleChildren.value[parentId];
};

const isChildrenVisible = (parentId) => {
  return visibleChildren.value[parentId];
};

const openEditModal = (item) => {
  editCategory.value = { ...item, image: '', parent_id: item.parent_id || null };
  originalSlug.value = item.slug; // Save the original slug
  editModal.value = true;
};

// rest of the script stays the same ...

onMounted(() => {
  fetchCategories(currentPage.value);
});

watch(currentPage, (newPage) => fetchCategories(newPage));
</script>

<style scoped>
.slide-image {
  block-size: 100px;
  inline-size: 100px;
  object-fit: cover;
}

.parent-category {
  background-color: #f0f0f0;
}

.child-category {
  background-color: #fff;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
