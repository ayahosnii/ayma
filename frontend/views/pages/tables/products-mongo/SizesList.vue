<template>
  <div>
    <!-- Size Table -->
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" color="secondary" @click="openAddModal">
          <i class="ri-add-circle-line"></i> Add Size
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">Abbreviation</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="size in sizes" :key="size.id">
          <td class="text-center">{{ size.name }}</td>
          <td class="text-center">{{ size.abbreviation }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(size)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(size)">
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
        <VCardTitle>Add Size</VCardTitle>
        <VCardText>
          <VForm ref="addForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="newSize.name"
                  label="Name"
                  placeholder="Size Name"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="newSize.abbreviation"
                  label="Abbreviation"
                  placeholder="S"
                  :error-messages="abbreviationErrors"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="addSize">Add</VBtn>
            <VBtn color="error" @click="closeAddModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="500px">
      <VCard>
        <VCardTitle>Edit Size</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editSize.name"
                  label="Name"
                  placeholder="Size Name"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editSize.abbreviation"
                  label="Abbreviation"
                  placeholder="S"
                  :error-messages="abbreviationErrorsEdit"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="updateSize">Save</VBtn>
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Size</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this size?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteSize">Delete</VBtn>
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

const sizes = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const addModal = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);

const newSize = ref({ name: '', abbreviation: '' });
const editSize = ref({ id: '', name: '', abbreviation: '' });
const deleteSizeId = ref(null);

const abbreviationErrors = ref<string | string[]>('');
const abbreviationErrorsEdit = ref<string | string[]>('');

// Function to validate abbreviation
const validateAbbreviation = () => {
  if (!newSize.value.abbreviation) {
    abbreviationErrors.value = 'Abbreviation is required.';
  } else {
    abbreviationErrors.value = '';
  }
};

// Function to validate and set abbreviation errors for the Edit modal
const validateAbbreviationEdit = () => {
  if (!editSize.value.abbreviation) {
    abbreviationErrorsEdit.value = 'Abbreviation is required.';
  } else {
    abbreviationErrorsEdit.value = '';
  }
};

watch(newSize.value, validateAbbreviation, { deep: true });
watch(editSize.value, validateAbbreviationEdit, { deep: true });

const fetchSizes = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/sizes?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      sizes.value = response.data; 
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch sizes:', error);
  }
};

const openAddModal = () => {
  newSize.value = { name: '', abbreviation: '' };
  addModal.value = true;
};

const closeAddModal = () => {
  addModal.value = false;
};

const addSize = async () => {
  validateAbbreviation();
  if (abbreviationErrors.value) {
    return; // Stop the function if there are validation errors
  }

  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.post(`${BASE_URL}/sizes`, newSize.value, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Size added successfully!');
      fetchSizes(currentPage.value);
      closeAddModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to add size:', error);
    $toast.error('Error adding size: ' + (error.response?.data?.message || error.message));
  }
};

const openEditModal = (size) => {
  editSize.value = { ...size };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateSize = async () => {
  validateAbbreviationEdit();
  if (abbreviationErrorsEdit.value) {
    return; // Stop the function if there are validation errors
  }

  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.put(`${BASE_URL}/sizes/${editSize.value.id}`, editSize.value, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Size updated successfully!');
      fetchSizes(currentPage.value);  // Refresh the list
      closeEditModal();  // Close the modal after updating
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update size:', error);
    $toast.error('Error updating size: ' + (error.response?.data?.message || error.message));
  }
};

const openDeleteModal = (size) => {
  deleteSizeId.value = size.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteSize = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteSizeId.value) {
      await axios.delete(`${BASE_URL}/sizes/${deleteSizeId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Size deleted successfully!');
      fetchSizes(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete size ID missing');
    }
  } catch (error) {
    console.error('Failed to delete size:', error);
    $toast.error('Error deleting size: ' + (error.response?.data?.message || error.message));
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchSizes(currentPage.value);
});

watch(currentPage, (newPage) => fetchSizes(newPage));
</script>
