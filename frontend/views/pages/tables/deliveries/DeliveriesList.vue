<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/deliveries/add">
          <i class="ri-add-circle-line"></i> Add Delivery
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Tracking Code</th>
          <th class="text-uppercase text-center">Status</th>
          <th class="text-uppercase text-center">Current Step</th>
          <th class="text-uppercase text-center">Delivery Partner</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="delivery in deliveries" :key="delivery.id">
          <td class="text-center">{{ delivery.tracking_code }}</td>
          <td class="text-center">{{ delivery.status }}</td>
          <td class="text-center">{{ delivery.current_step }}</td>
          <td class="text-center">{{ delivery.delivery_partner }}</td>
          <td class="text-center">
            <VBtn size="small" title="Info" color="info" @click="openInfoModal(delivery)">
              <i class="ri-information-line"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(delivery)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(delivery)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>

    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Info Modal -->
    <VDialog v-model="infoModal" max-width="1000px">
      <VCard>
        <VCardTitle>Delivery Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="3"><strong>Tracking Code:</strong> {{ infoDelivery.tracking_code }}</VCol>
            <VCol cols="3"><strong>Status:</strong> {{ infoDelivery.status }}</VCol>
            <VCol cols="3"><strong>Current Step:</strong> {{ infoDelivery.current_step }}</VCol>
            <VCol cols="3"><strong>Delivery Partner:</strong> {{ infoDelivery.delivery_partner }}</VCol>
          </VRow>
          <VRow class="mt-4">
            <VCol cols="12">
              <strong>Timeline:</strong>
              <ul>
                <li v-for="step in JSON.parse(infoDelivery.timeline || '[]')" :key="step.date">
                  {{ step.date }} - {{ step.description }}
                </li>
              </ul>
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VBtn color="error" @click="closeInfoModal">Close</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="800px">
      <VCard>
        <VCardTitle>Edit Delivery</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="6">
                <VTextField v-model="editDelivery.tracking_code" label="Tracking Code" required />
              </VCol>
              <VCol cols="6">
                <VSelect v-model="editDelivery.status" :items="statuses" label="Status" required />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="6">
                <VTextField v-model="editDelivery.delivery_partner" label="Delivery Partner" required />
              </VCol>
              <VCol cols="6">
                <VTextField v-model="editDelivery.current_step" label="Current Step" type="number" required />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextarea v-model="editDelivery.timeline" label="Timeline (JSON)" />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VBtn color="success" @click="updateDelivery">Save</VBtn>
          <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Delivery</VCardTitle>
        <VCardText>Are you sure you want to delete this delivery?</VCardText>
        <VCardActions>
          <VBtn color="error" @click="deleteDelivery">Delete</VBtn>
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

const deliveries = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false);

const editDelivery = ref({
  tracking_code: '',
  status: 'In Transit',
  delivery_partner: '',
  current_step: 0,
  timeline: '[]',
});

const infoDelivery = ref({});
const deleteDeliveryData = ref(null);

const fetchDeliveries = async (page = 1) => {
  const response = await axios.get(`${BASE_URL}/deliveries?page=${page}`);
  deliveries.value = response.data.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

const openInfoModal = (delivery) => {
  infoDelivery.value = { ...delivery };
  infoModal.value = true;
};

const closeInfoModal = () => {
  infoModal.value = false;
};

const openEditModal = (delivery) => {
  editDelivery.value = { ...delivery };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const openDeleteModal = (delivery) => {
  deleteDeliveryData.value = delivery;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteDelivery = async () => {
  try {
    await axios.delete(`${BASE_URL}/deliveries/${deleteDeliveryData.value.id}`);
    $toast.success('Delivery deleted successfully!');
    closeDeleteModal();
    fetchDeliveries();
  } catch (error) {
    $toast.error('Error deleting delivery.');
  }
};

const updateDelivery = async () => {
  try {
    await axios.put(`${BASE_URL}/deliveries/${editDelivery.value.id}`, editDelivery.value);
    $toast.success('Delivery updated successfully!');
    closeEditModal();
    fetchDeliveries();
  } catch (error) {
    $toast.error('Error updating delivery.');
  }
};

const onPageChange = (page) => {
  currentPage.value = page;
  fetchDeliveries(page);
};

onMounted(() => {
  fetchDeliveries();
});

const statuses = ref(['Pending', 'In Transit', 'Delivered', 'Cancelled']);
</script>
