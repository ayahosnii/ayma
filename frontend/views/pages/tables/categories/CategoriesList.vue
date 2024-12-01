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
          <th class="text-uppercase text-center">Image</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <template v-for="(category, index) in categories" :key="category.id">
          <!-- Parent category row -->
          <tr v-if="!category.parent_id">
            <td class="text-center">
              <button @click="toggleCategory(index)" class="collapse-btn">
                <i :class="category.expanded ? 'ri-arrow-down-s-line' : 'ri-arrow-right-s-line'"></i>
              </button>
              {{ category.name }}
              <span v-if="category.children && category.children.length > 0"> - ({{ category.children.length }})</span>
            </td>
            <td class="text-center">{{ category.slug }}</td>
            <td class="text-center">
              <img :src="getImageUrl(category.image)" alt="Category Image" class="slide-image mt-1" />
            </td>
            <td class="text-center">
              <VBtn size="small" title="Info" color="info" @click="openInfoModal(category)">
                <i class="ri-information-line"></i>
              </VBtn>&nbsp;
              <VBtn size="small" title="Edit" color="warning" @click="openEditModal(category)">
                <i class="ri-edit-fill"></i>
              </VBtn>&nbsp;
              <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(category)">
                <i class="ri-delete-bin-line"></i>
              </VBtn>
            </td>
          </tr>
        
          <!-- Child categories (shown only when parent is expanded) -->
          <tr v-if="category.expanded && category.children" v-for="child in category.children" :key="child.id" class="sub-category-row">
            <td class="text-center">
              <span class="ml-4">{{ child.name }}</span>
            </td>
            <td class="text-center">{{ child.slug }}</td>
            <td class="text-center">
              <img :src="getImageUrl(child.image)" alt="Category Image" class="slide-image mt-1" />
            </td>
            <td class="text-center">
              <VBtn size="small" title="Info" color="info" @click="openInfoModal(child)">
                <i class="ri-information-line"></i>
              </VBtn>&nbsp;
              <VBtn size="small" title="Edit" color="warning" @click="openEditModal(child)">
                <i class="ri-edit-fill"></i>
              </VBtn>&nbsp;
              <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(child)">
                <i class="ri-delete-bin-line"></i>
              </VBtn>
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

    <!-- Info Modal -->
    <VDialog v-model="infoModal" max-width="600px">
      <VCard>
        <VCardTitle>Category Details</VCardTitle>
        <VRow>
          <VCol cols="12">
            
              <div class="d-flex justify-space-between flex-wrap flex-md-nowrap flex-column flex-md-row">
                <!-- <div class="ma-auto pa-5"> -->
                  <VImg :src="getImageUrl(infoCategory.image)" width="200" height="200" alt="Category Image" />
                <!-- </div> -->

                <VDivider :vertical="$vuetify.display.mdAndUp" />

                <div>
                  <VCardItem>
                    <VCardTitle>{{ infoCategory.name }}</VCardTitle>
                  </VCardItem>

                  <VCardText>
                    <span class="font-weight-medium">Description:</span> <span>{{ infoCategory.description || 'No description available.'}}</span>
                  </VCardText>

                  <VCardText class="text-subtitle-1">
                    <span class="font-weight-medium">Slug:</span> <span>{{ infoCategory.slug }}</span>
                  </VCardText>

                  <VCardText class="text-subtitle-1">
                    <span class="font-weight-medium">Parent Category:</span> <span>{{ infoCategory.parent ? infoCategory.parent.name : 'None' }}</span>
                  </VCardText>

                  <VCardText class="text-subtitle-1">
                    <span class="font-weight-medium">Status:</span> <span >{{ infoCategory.is_active ? 'Active' : 'Inactive' }}</span>
                  </VCardText>
                </div>
              </div>
            
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12" class="d-flex justify-end">
            <VBtn color="secondary" @click="closeInfoModal">Close</VBtn>
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
const infoModal = ref(false);

const editCategory = ref({ id: '', name: '', slug: '', description: '', image: '', parent_id: null, order: 0, is_active: 1 });
const originalSlug = ref(''); 
const deleteCategoryId = ref(null);
const infoCategory = ref({ id: '', name: '', slug: '', description: '', image: '', parent: null, order: 0, is_active: 1 }); 
const categories2 = ref([]); 

const fetchCategories = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/categories?page=${page}`, {
        headers: { Authorization: `Bearer ${token}` },
      });

      // Add expanded property to manage collapse/expand state
      categories.value = response.data.map(category => ({ ...category, expanded: false }));
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const toggleCategory = (index) => {
  categories.value[index].expanded = !categories.value[index].expanded;
};

const getImageUrl = (path) => {
  return path ? `http://127.0.0.1:8000/storage/${path}` : 'placeholder_image_url';
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

const openInfoModal = (item) => {
  infoCategory.value = { ...item };
  infoModal.value = true;
};

const closeInfoModal = () => {
  infoModal.value = false;
};

onMounted(() => {
  fetchCategories(currentPage.value);
  fetchParentCategories();
});

watch(currentPage, (newPage) => fetchCategories(newPage));
</script>

<style scoped>
/* Styles for table rows */

tbody tr.sub-category-row {
  background-color: #e9ecef;
  font-weight: 500;
}

button.collapse-btn {
  border: none;
  background: none;
  cursor: pointer;
}

.slide-image {
  block-size: 90px;
  inline-size: 90px;
  object-fit: cover;
}
</style>
