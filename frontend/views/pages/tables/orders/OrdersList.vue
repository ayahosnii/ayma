<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/orders/add">
          <i class="ri-add-circle-line"></i> Add Order
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Order ID</th>
          <th class="text-uppercase text-center">Customer</th>
          <th class="text-uppercase text-center">Total Amount</th>
          <th class="text-uppercase text-center">Status</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td class="text-center">{{ order.id }}</td>
          <td class="text-center">{{ order.customer_name }}</td>
          <td class="text-center">{{ order.total }}</td>
          <td class="text-center">{{ order.status }}</td>
          <td class="text-center">
            <VBtn size="small" title="Info" color="info" @click="openInfoModal(order)">
              <i class="ri-information-line"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(order)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(order)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
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
        <VCardTitle>Edit Order</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editOrder.customer_name"
                  label="Customer Name"
                  placeholder="Customer Name"
                  required
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="editOrder.total"
                  label="Total Amount"
                  placeholder="Total Amount"
                  required
                  type="number"
                  min="0"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VSelect
                  v-model="editOrder.status"
                  label="Order Status"
                  :items="statuses"
                  item-title="label"
                  item-value="value"
                  density="compact"
                  class="me-3"
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="updateOrder">Save</VBtn>
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Order</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardText>Are you sure you want to delete this order?</VCardText>
          </VCol>
        </VRow>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteOrder">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Info Modal -->
    <VDialog v-model="infoModal" max-width="600px">
      <VCard>
        <VCardTitle>Order Details</VCardTitle>
        <VRow>
          <VCol cols="12">
            <VCardItem>
              <VCardTitle>{{ infoOrder.id }}</VCardTitle>
            </VCardItem>

            <VCardText>
              <span class="font-weight-medium">Customer:</span> <span>{{ infoOrder.customer_name }}</span>
            </VCardText>

            <VCardText>
              <span class="font-weight-medium">Total Amount:</span> <span>{{ infoOrder.total }}</span>
            </VCardText>

            <VCardText class="text-subtitle-1">
              <span class="font-weight-medium">Status:</span> <span>{{ infoOrder.status }}</span>
            </VCardText>
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
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const orders = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false); // New modal for order info

const editOrder = ref({ id: '', customer_name: '', total: 0, status: 'pending' });
const deleteOrderId = ref(null);
const infoOrder = ref({ id: '', customer_name: '', total: 0, status: 'pending' }); // Store the selected order info

const statuses = ref([
  { label: 'Pending', value: 'pending' },
  { label: 'Processing', value: 'processing' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' },
]);

const fetchOrders = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/orders?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      orders.value = response.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch orders:', error);
  }
};

const openEditModal = (order) => {
  editOrder.value = { ...order };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const updateOrder = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      await axios.put(`${BASE_URL}/orders/${editOrder.value.id}`, editOrder.value, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Order updated successfully!');
      fetchOrders(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update order:', error);
    $toast.error('Error updating order: ' + (error.response?.data?.message || error.message));
  }
};

const openDeleteModal = (order) => {
  deleteOrderId.value = order.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteOrder = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteOrderId.value) {
      await axios.delete(`${BASE_URL}/orders/${deleteOrderId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Order deleted successfully!');
      fetchOrders(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete order ID missing');
    }
  } catch (error) {
    console.error('Failed to delete order:', error);
    $toast.error('Error deleting order: ' + (error.response?.data?.message || error.message));
  }
};

const openInfoModal = (order) => {
  infoOrder.value = { ...order };
  infoModal.value = true;
};

const closeInfoModal = () => {
  infoModal.value = false;
};

const onPageChange = (page) => {
  currentPage.value = page;
  fetchOrders(page);
};

onMounted(() => {
  fetchOrders(currentPage.value);
});
</script>
