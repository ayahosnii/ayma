<template>
  <div>
    <VTable>
      <thead>
      <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" to="/suppliers/add">
        <i class="ri-add-circle-line"></i> Add Supplier
      </VBtn>
      <tr>
        <th class="text-uppercase text-center">Supplier Name</th>
        <th class="text-uppercase text-center">Contact Person</th>
        <th class="text-uppercase text-center">Phone</th>
        <th class="text-uppercase text-center">Email</th>
        <th class="text-uppercase text-center">Actions</th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="supplier in suppliers" :key="supplier.id">
        <td class="text-center">{{ supplier.name }}</td>
        <td class="text-center">{{ supplier.contact_person }}</td>
        <td class="text-center">{{ supplier.phone }}</td>
        <td class="text-center">{{ supplier.email }}</td>
        <td class="text-center">
          <VBtn size="small" title="Info" color="info" @click="openInfoModal(supplier)">
            <i class="ri-information-line"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Edit" color="warning" @click="openEditModal(supplier)">
            <i class="ri-edit-fill"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(supplier)">
            <i class="ri-delete-bin-line"></i>
          </VBtn>
        </td>
      </tr>
      </tbody>
    </VTable>

    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Info Modal -->
    <VDialog v-model="infoModal" max-width="600px">
      <VCard>
        <VCardTitle>Supplier Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <strong>Supplier Name:</strong> {{ infoSupplier.name }}
            </VCol>
            <VCol cols="6">
              <strong>Contact Person:</strong> {{ infoSupplier.contact_person }}
            </VCol>
            <VCol cols="6">
              <strong>Phone:</strong> {{ infoSupplier.phone }}
            </VCol>
            <VCol cols="6">
              <strong>Contact Info</strong> {{ infoSupplier.contact_info }}
            </VCol>
            <VCol cols="6">
              <strong>Email:</strong> {{ infoSupplier.email }}
            </VCol>
            <VCol cols="6">
              <strong>Address:</strong> {{ infoSupplier.address }}
            </VCol>
            <VCol cols="4">
              <strong>City:</strong> {{ infoSupplier.city }}
            </VCol>
            <VCol cols="4">
              <strong>State:</strong> {{ infoSupplier.state }}
            </VCol>
            <VCol cols="4">
              <strong>Postal Code:</strong> {{ infoSupplier.postal_code }}
            </VCol>
            <VCol cols="4">
              <strong>Country:</strong> {{ infoSupplier.country }}
            </VCol>
            <VCol cols="4">
              <strong>Supplier Type:</strong> {{ infoSupplier.supplier_type }}
            </VCol>
            <VCol cols="4">
              <strong>Website:</strong> {{ infoSupplier.website }}
            </VCol>
            <VCol cols="4">
              <strong>Tax ID:</strong> {{ infoSupplier.tax_id }}
            </VCol>
            <VCol cols="4">
              <strong>Status:</strong> {{ infoSupplier.status }}
            </VCol>
            <VCol cols="12">
              <strong>Notes:</strong> {{ infoSupplier.notes }}
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VBtn color="error" @click="closeInfoModal">Close</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>


    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="600px">
      <VCard>
        <VCardTitle>Edit Supplier</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="editSupplier.name" label="Supplier Name" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editSupplier.contact_person" label="Contact Person" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editSupplier.phone" label="Phone" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editSupplier.email" label="Email" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editSupplier.address" label="Address" required />
              </VCol>
              <VCol cols="12">
                <VSelect
                  v-model="editSupplier.supplier_type"
                  :items="['local', 'international', 'wholesale', 'retail', 'factory']"
                  label="Supplier Type"
                  required
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VBtn color="success" @click="updateSupplier">Save</VBtn>
          <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Supplier</VCardTitle>
        <VCardText>Are you sure you want to delete this supplier?</VCardText>
        <VCardActions>
          <VBtn color="error" @click="deleteSupplier">Delete</VBtn>
          <VBtn color="success" @click="closeDeleteModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();
const BASE_URL = 'http://127.0.0.1:8000/api';

const suppliers = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const infoModal = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);

const editSupplier = ref({
  name: '',
  contact_person: '',
  phone: '',
  email: '',
  address: '',
});

const infoSupplier = ref({});
const deleteSupplierData = ref(null);

// Fetch suppliers from API
const fetchSuppliers = async (page = 1) => {
  const token = localStorage.getItem('authToken');
  const response = await axios.get(`${BASE_URL}/suppliers?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  suppliers.value = response.data.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

// Open Modals
const openInfoModal = (supplier) => {
  infoSupplier.value = supplier;
  infoModal.value = true;
};
const openEditModal = (supplier) => {
  editSupplier.value = { ...supplier };
  editModal.value = true;
};
const openDeleteModal = (supplier) => {
  deleteSupplierData.value = supplier;
  deleteModal.value = true;
};

// Close Modals
const closeInfoModal = () => (infoModal.value = false);
const closeEditModal = () => (editModal.value = false);
const closeDeleteModal = () => (deleteModal.value = false);

// Update Supplier
const updateSupplier = async () => {
  const token = localStorage.getItem('authToken');
  await axios.put(`${BASE_URL}/suppliers/${editSupplier.value.id}`, editSupplier.value, {
    headers: { Authorization: `Bearer ${token}` },
  });
  fetchSuppliers(currentPage.value);
  $toast.success('Supplier updated successfully!');
  closeEditModal();
};

// Delete Supplier
const deleteSupplier = async () => {
  const token = localStorage.getItem('authToken');
  await axios.delete(`${BASE_URL}/suppliers/${deleteSupplierData.value.id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  fetchSuppliers(currentPage.value);
  $toast.success('Supplier deleted successfully!');
  closeDeleteModal();
};

// Handle Pagination
const onPageChange = (page) => {
  currentPage.value = page;
  fetchSuppliers(page);
};

// Fetch data on mount
onMounted(() => {
  fetchSuppliers();
});
</script>
