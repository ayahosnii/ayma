<template>
  <div>
    <!-- Add Shipping Company Button and Search Input -->
    <div class="mb-4 ml-4 d-flex align-items-center">
      <VBtn size="small" title="Add" type="Add" color="secondary" to="/shipping-companies/add">
        <i class="ri-add-circle-line"></i> Add Shipping Company
      </VBtn>
      <VTextField
        v-model="searchQuery"
        label="Search Shipping Companies"
        class="ml-4"
        hide-details
        clearable
        dense
        style="max-inline-size: 300px;"
        placeholder="Search by name, phone, email, etc."
      />
    </div>

    <!-- Table to Display Shipping Companies -->
    <VTable>
      <thead>
      <tr>
        <th class="text-uppercase text-center">Company Name</th>
        <th class="text-uppercase text-center">Phone</th>
        <th class="text-uppercase text-center">Email</th>
        <th class="text-uppercase text-center">Actions</th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="company in filteredCompanies" :key="company.id">
        <td class="text-center">{{ company.name }}</td>
        <td class="text-center">
          <a :href="`https://wa.me/${company.phone.replace(/\D/g, '')}`" target="_blank" style="color: inherit;">
            {{ company.phone }}
          </a>
        </td>
        <td class="text-center">
          <a :href="`mailto:${company.email}`" target="_blank" style="color: inherit;">
            {{ company.email }}
          </a>
        </td>
        <td class="text-center">
          <VBtn size="small" title="Info" color="info" @click="openInfoModal(company)">
            <i class="ri-information-line"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Edit" color="warning" @click="openEditModal(company)">
            <i class="ri-edit-fill"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(company)">
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
        <VCardTitle>Shipping Company Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <strong>Company Name:</strong> {{ infoCompany.name }}
            </VCol>
            <VCol cols="6">
              <strong>Phone:</strong> <a :href="`https://wa.me/${infoCompany.phone.replace(/\D/g, '')}`" target="_blank">
              {{ infoCompany.phone }}
            </a>
            </VCol>
            <VCol cols="6">
              <strong>Email:</strong> <a :href="`mailto:${infoCompany.email}`" target="_blank">
              {{ infoCompany.email }}
            </a>
            </VCol>
            <VCol cols="6">
              <strong>Website:</strong> <a
              :href="infoCompany.website.startsWith('http') ? infoCompany.website : `https://${infoCompany.website}`"
              target="_blank">
              {{ infoCompany.website }}
            </a>
            </VCol>
            <VCol cols="6">
              <strong>Address:</strong> {{ infoCompany.address_line_1 }}
            </VCol>
            <VCol cols="6">
              <strong>City:</strong> {{ infoCompany.city }}
            </VCol>
            <VCol cols="6">
              <strong>State:</strong> {{ infoCompany.state }}
            </VCol>
            <VCol cols="6">
              <strong>Country:</strong> {{ infoCompany.country }}
            </VCol>
            <VCol cols="6">
              <strong>Zip Code:</strong> {{ infoCompany.zip_code }}
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
        <VCardTitle>Edit Shipping Company</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="editCompany.name" label="Company Name" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.phone" label="Phone" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.email" label="Email" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.website" label="Website" />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.address_line_1" label="Address Line 1" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.city" label="City" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.state" label="State" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.zip_code" label="Zip Code" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editCompany.country" label="Country" required />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VBtn color="success" @click="updateCompany">Save</VBtn>
          <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Shipping Company</VCardTitle>
        <VCardText>Are you sure you want to delete this shipping company?</VCardText>
        <VCardActions>
          <VBtn color="error" @click="deleteCompany">Delete</VBtn>
          <VBtn color="success" @click="closeDeleteModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();
const BASE_URL = 'http://127.0.0.1:8000/api';

const companies = ref([]);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);

const infoModal = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);

const editCompany = ref({
  name: '',
  phone: '',
  email: '',
  website: '',
  address_line_1: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
});

const infoCompany = ref({});
const deleteCompanyData = ref(null);

// Computed property to filter shipping companies based on the search query
const filteredCompanies = computed(() => {
  if (!searchQuery.value) return companies.value;

  const query = searchQuery.value.toLowerCase();
  return companies.value.filter(
    (company) =>
      company.name.toLowerCase().includes(query) ||
      company.phone.toLowerCase().includes(query) ||
      company.email.toLowerCase().includes(query) ||
      company.address_line_1.toLowerCase().includes(query) ||
      company.city.toLowerCase().includes(query)
  );
});

// Fetch shipping companies from API
const fetchCompanies = async (page = 1) => {
  const token = localStorage.getItem('authToken');
  const response = await axios.get(`${BASE_URL}/shipping-companies?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  console.log(response.data)
  companies.value = response.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

// Modal controls
const openInfoModal = (company) => (infoCompany.value = company, infoModal.value = true);
const openEditModal = (company) => (editCompany.value = { ...company }, editModal.value = true);
const openDeleteModal = (company) => (deleteCompanyData.value = company, deleteModal.value = true);
const closeInfoModal = () => (infoModal.value = false);
const closeEditModal = () => (editModal.value = false);
const closeDeleteModal = () => (deleteModal.value = false);

// CRUD operations
const updateCompany = async () => {
  const token = localStorage.getItem('authToken');
  const response = await axios.put(
    `${BASE_URL}/shipping-companies/${editCompany.value.id}`,
    editCompany.value,
    { headers: { Authorization: `Bearer ${token}` } }
  );

  if (response.data.success) {
    $toast.success('Shipping company updated successfully.');
    fetchCompanies();
    closeEditModal();
  }
};

const deleteCompany = async () => {
  const token = localStorage.getItem('authToken');
  const response = await axios.delete(`${BASE_URL}/shipping-companies/${deleteCompanyData.value.id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });

  if (response.data.success) {
    $toast.success('Shipping company deleted successfully.');
    fetchCompanies();
    closeDeleteModal();
  }
};

// Fetch companies when component is mounted
onMounted(() => fetchCompanies());

// Handle pagination page change
const onPageChange = (page) => fetchCompanies(page);
</script>
