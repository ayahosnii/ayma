<template>
  <div>
    <!-- Add User Button and Search Input -->
    <div class="mb-4 ml-4 d-flex align-items-center">
      <VBtn size="small" title="Add" type="Add" color="secondary" to="/users/add">
        <i class="ri-add-circle-line"></i> Add Users
      </VBtn>
      <VTextField
        v-model="searchQuery"
        label="Search User"
        class="ml-4"
        hide-details
        clearable
        dense
        style="max-inline-size: 300px;"
        placeholder="Search by name, phone, email, etc."
      />
    </div>

    <!-- Table to Display Users -->
    <VTable>
      <thead>
      <tr>
        <th class="text-uppercase text-center">User Name</th>
        <th class="text-uppercase text-center">Phone</th>
        <th class="text-uppercase text-center">Email</th>
        <th class="text-uppercase text-center">Actions</th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="user in filteredUsers" :key="user.id">
        <td class="text-center">{{ user.name }}</td>
        <td class="text-center">{{ user.phone_number }}</td>
        <td class="text-center">{{ user.email }}</td>
        <td class="text-center">
          <VBtn size="small" title="Info" color="info" @click="openInfoModal(user)">
            <i class="ri-information-line"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Edit" color="warning" @click="openEditModal(user)">
            <i class="ri-edit-fill"></i>
          </VBtn>&nbsp;
          <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(user)">
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
        <VCardTitle>User Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <strong>User Name:</strong> {{ infoUser.name }}
            </VCol>
            <VCol cols="6">
              <strong>Phone:</strong> {{ infoUser.phone_number }}
            </VCol>
            <VCol cols="6">
              <strong>Email:</strong> <a :href="`mailto:${infoUser.email}`" target="_blank">
              {{ infoUser.email }}
            </a>
            </VCol>
            <VCol cols="6">
              <strong>City:</strong> {{ infoUser.city }}
            </VCol>
            <VCol cols="6">
              <strong>State:</strong> {{ infoUser.state }}
            </VCol>
            <VCol cols="6">
              <strong>Country:</strong> {{ infoUser.country }}
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
        <VCardTitle>Edit User</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="editUser.name" label="User Name" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.phone_number" label="Phone" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.email" label="Email" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.city" label="City" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.state" label="State" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.zip_code" label="Zip Code" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="editUser.country" label="Country" required />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VBtn color="success" @click="updateUser">Save</VBtn>
          <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete User</VCardTitle>
        <VCardText>Are you sure you want to delete this user?</VCardText>
        <VCardActions>
          <VBtn color="error" @click="deleteUser">Delete</VBtn>
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

const users = ref([]);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);

const infoModal = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);

const editUser = ref({
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

const infoUser = ref({});
const deleteUserData = ref(null);

// Computed property to filter shipping users based on the search query
const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;

  const query = searchQuery.value.toLowerCase();
  return users.value.filter(
    (users) =>
      users.name.toLowerCase().includes(query) ||
      users.email.toLowerCase().includes(query)
  );
});

// Fetch shipping users from API
const fetchUsers = async (page = 1) => {
  const token = localStorage.getItem('authToken');
  const response = await axios.get(`${BASE_URL}/users?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  users.value = response.data.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

// Modal controls
const openInfoModal = (user) => (infoUser.value = user, infoModal.value = true);
const openEditModal = (user) => (editUser.value = { ...user }, editModal.value = true);
const openDeleteModal = (user) => (deleteUserData.value = user, deleteModal.value = true);
const closeInfoModal = () => (infoModal.value = false);
const closeEditModal = () => (editModal.value = false);
const closeDeleteModal = () => (deleteModal.value = false);

// CRUD operations
const updateUser = async () => {
  const token = localStorage.getItem('authToken');
  const response = await axios.put(
    `${BASE_URL}/users/${editUser.value.id}`,
    editUser.value,
    { headers: { Authorization: `Bearer ${token}` } }
  );

  if (response.data.success) {
    $toast.success('Users updated successfully.');
    fetchUsers();
    closeEditModal();
  }
};

const deleteUser = async () => {
  const token = localStorage.getItem('authToken');
  const response = await axios.delete(`${BASE_URL}/users/${deleteUserData.value.id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });

  if (response.data.success) {
    $toast.success('User deleted successfully.');
    fetchUsers();
    closeDeleteModal();
  }
};

// Fetch users when component is mounted
onMounted(() => fetchUsers());

// Handle pagination page change
const onPageChange = (page) => fetchUsers(page);
</script>
