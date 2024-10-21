<template>
  <div>
    <!-- Color Table -->
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" color="secondary" @click="openAddModal">
          <i class="ri-add-circle-line"></i> Add Color
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">Hex Code</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="color in colors" :key="color.id">
          <td class="text-center">{{ color.name }}</td>
          <td class="text-center">{{ color.hex_code }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(color)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(color)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>

    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Add Modal -->
    <VDialog v-model="addModal" max-width="500px">
      <VCard>
        <VCardTitle>Add Color</VCardTitle>
        <VCardText>
          <VForm ref="addForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="newColor.name"
                  label="Name"
                  placeholder="Color Name"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="newColor.hex_code"
                  label="Hex Code"
                  placeholder="#FFFFFF"
                  :error-messages="hexCodeErrors"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="addColor">Add</VBtn>
            <VBtn color="error" @click="closeAddModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="500px">
      <VCard>
        <VCardTitle>Edit Color</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editColor.name"
                  label="Name"
                  placeholder="Color Name"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editColor.hex_code"
                  label="Hex Code"
                  placeholder="#FFFFFF"
                  :error-messages="hexCodeErrorsEdit"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="updateColor">Save</VBtn>
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Color</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this color?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteColor">Delete</VBtn>
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

const colors = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const addModal = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);

const newColor = ref({ name: '', hex_code: '' });
const editColor = ref({ id: '', name: '', hex_code: '' });
const deleteColorId = ref(null);

const hexCodeErrors = ref<string | string[]>('');
const hexCodeErrorsEdit = ref<string | string[]>('');

// Function to validate hex code
const isValidHexCode = (hex: string) => {
  const hexRegex = /^#([0-9A-F]{3}){1,2}$/i;
  return hexRegex.test(hex);
};

// Function to validate and set hex code errors for the Add modal
const validateHexCode = () => {
  if (!newColor.value.hex_code) {
    hexCodeErrors.value = 'Hex code is required.';
  } else if (!isValidHexCode(newColor.value.hex_code)) {
    hexCodeErrors.value = 'Hex code is not valid. Please enter a valid hex code.';
  } else {
    hexCodeErrors.value = '';
  }
};

// Function to validate and set hex code errors for the Edit modal
const validateHexCodeEdit = () => {
  if (!editColor.value.hex_code) {
    hexCodeErrorsEdit.value = 'Hex code is required.';
  } else if (!isValidHexCode(editColor.value.hex_code)) {
    hexCodeErrorsEdit.value = 'Hex code is not valid. Please enter a valid hex code.';
  } else {
    hexCodeErrorsEdit.value = '';
  }
};

watch(newColor.value, validateHexCode, { deep: true });
watch(editColor.value, validateHexCodeEdit, { deep: true });

const fetchColors = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/colors?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      colors.value = response.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch colors:', error);
  }
};

const openAddModal = () => {
  newColor.value = { name: '', hex_code: '' };
  addModal.value = true;
};

const closeAddModal = () => {
  addModal.value = false;
};

const addColor = async () => {
  validateHexCode();
  if (hexCodeErrors.value) {
    return; // Stop the function if there are validation errors
  }

  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.post(`${BASE_URL}/colors`, newColor.value, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Color added successfully!');
      fetchColors(currentPage.value);
      closeAddModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to add color:', error);
    $toast.error('Error adding color: ' + (error.response?.data?.message || error.message));
  }
};

const openEditModal = (color) => {
  editColor.value = { ...color };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateColor = async () => {
  validateHexCodeEdit();
  if (hexCodeErrorsEdit.value) {
    return; // Stop the function if there are validation errors
  }

  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.put(`${BASE_URL}/colors/${editColor.value.id}`, editColor.value, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Color updated successfully!');
      fetchColors(currentPage.value);  // Refresh the list
      closeEditModal();  // Close the modal after updating
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update color:', error);
    $toast.error('Error updating color: ' + (error.response?.data?.message || error.message));
  }
};

const openDeleteModal = (color) => {
  deleteColorId.value = color.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteColor = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteColorId.value) {
      await axios.delete(`${BASE_URL}/colors/${deleteColorId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Color deleted successfully!');
      fetchColors(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete color ID missing');
    }
  } catch (error) {
    console.error('Failed to delete color:', error);
    $toast.error('Error deleting color: ' + (error.response?.data?.message || error.message));
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchColors(currentPage.value);
});

watch(currentPage, (newPage) => fetchColors(newPage));
</script>
