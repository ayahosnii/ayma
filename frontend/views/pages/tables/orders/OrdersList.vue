<template>
  <div>
    <VTable>
      <thead>
        <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/orders/add">
          <i class="ri-add-circle-line"></i> Add Order
        </VBtn>
        <tr>
          <th class="text-uppercase text-center">Order Number</th>
          <th class="text-uppercase text-center">Customer</th>
          <th class="text-uppercase text-center">Total Amount</th>
          <th class="text-uppercase text-center">Status</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td class="text-center">{{ order.order_number }}</td>
          <td class="text-center">{{ order.user.name }}</td>
          <td class="text-center">${{ order.total_amount }}</td>
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

    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Info Modal -->
    <VDialog v-model="infoModal" max-width="1000px">
      <VCard>
        <VCardTitle>Order Details</VCardTitle>
        <VCardText>

          <!-- Order Details Section -->
          <VRow>
            <VCol cols="12">
              <h3>Order Details</h3>
              <br>
              <VRow>
                <VCol cols="3">
                  <strong>Order Number:</strong> {{ infoOrder.order_number }}
                </VCol>
                <VCol cols="3">
                  <strong>Status:</strong> {{ infoOrder.status }}
                </VCol>
                <VCol cols="3">
                  <strong>Total Amount:</strong> ${{ infoOrder.total_amount }}
                </VCol>
                <VCol cols="3">
                  <strong>Payment Status:</strong> {{ infoOrder.payment_status }}
                </VCol>
                <VCol cols="3">
                  <strong>Payment Method:</strong> {{ infoOrder.payment_method }}
                </VCol>
                <VCol cols="3">
                  <strong>Transaction ID:</strong> {{ infoOrder.transaction_id }}
                </VCol>
                <VCol cols="4">
                  <strong>Order Date:</strong> {{ infoOrder.order_date }}
                </VCol>
              </VRow>
            </VCol>
          </VRow>

          <!-- Shipping Details Section -->
          <VRow>
            <VCol cols="12" class="mt-4">
              <h3>Shipping Details</h3>
              <br>
              <VRow>
                <VCol cols="3">
                  <strong>Shipping Address:</strong> {{ infoOrder.shipping_address }}
                </VCol>
                <VCol cols="3">
                  <strong>Shipping City:</strong> {{ infoOrder.shipping_city }}
                </VCol>
                <VCol cols="3">
                  <strong>Shipping State:</strong> {{ infoOrder.shipping_state }}
                </VCol>
                <VCol cols="3">
                  <strong>Shipping Postal Code:</strong> {{ infoOrder.shipping_postal_code }}
                </VCol>
                <VCol cols="3">
                  <strong>Shipping Country:</strong> {{ infoOrder.shipping_country }}
                </VCol>
                <VCol cols="3">
                  <strong>Shipping Phone:</strong> {{ infoOrder.shipping_phone }}
                </VCol>
              </VRow>
            </VCol>
          </VRow>

          <!-- Order Items Section -->
      <VRow>
        <VCol cols="12" class="mt-4">
          <h3>Order Items</h3>
          <VTable>
            <thead>
              <tr>
                <th class="text-uppercase text-center">Product Name</th>
                <th class="text-uppercase text-center">Quantity</th>
                <th class="text-uppercase text-center">Price</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in infoOrder.order_items" :key="item.id">
                <td class="text-center">{{ item.product_name }}</td>
                <td class="text-center">{{ item.quantity }}</td>
                <td class="text-center">${{ item.price }}</td>
              </tr>
            </tbody>
          </VTable>
        </VCol>
      </VRow>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex justify-end">
            <VBtn color="error" @click="closeInfoModal">Close</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="800px">
      <VCard>
        <VCardTitle>Edit Order</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <!-- Order ID (Read-Only) -->
              <VCol cols="4">
                <VTextField v-model="editOrder.id" label="Order ID" readonly />
              </VCol>

              <!-- Order Number (Read-Only) -->
              <VCol cols="4">
                <VTextField v-model="editOrder.order_number" label="Order Number" readonly />
              </VCol>

              <!-- Customer Name -->
              <VCol cols="4">
                <VTextField v-model="editOrder.customer_name" label="Customer Name" required />
              </VCol>
            </VRow>

            <VRow>
              <!-- Product -->
              <VCol cols="4">
                <VSelect v-model="editOrder.product_id" :items="products" item-title="name" item-value="id" label="Product" required />
              </VCol>

              <!-- Quantity -->
              <VCol cols="4">
                <VTextField v-model="editOrder.quantity" label="Quantity" type="number" min="1" required />
              </VCol>

              <!-- Status -->
              <VCol cols="4">
                <VSelect v-model="editOrder.status" :items="statuses" item-title="label" item-value="value" label="Status" required />
              </VCol>
            </VRow>

            <VRow>
              <!-- Total Amount -->
              <VCol cols="4">
                <VTextField v-model="editOrder.total_amount" label="Total Amount" type="number" required />
              </VCol>

              <!-- Shipping Address -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_address" label="Shipping Address" required />
              </VCol>

              <!-- Shipping City -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_city" label="Shipping City" required />
              </VCol>
            </VRow>

            <VRow>
              <!-- Shipping State -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_state" label="Shipping State" required />
              </VCol>

              <!-- Shipping Postal Code -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_postal_code" label="Postal Code" required />
              </VCol>

              <!-- Shipping Country -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_country" label="Shipping Country" required />
              </VCol>
            </VRow>

            <VRow>
              <!-- Shipping Phone -->
              <VCol cols="4">
                <VTextField v-model="editOrder.shipping_phone" label="Shipping Phone" required />
              </VCol>

              <!-- Payment Status -->
              <VCol cols="4">
                <VSelect v-model="editOrder.payment_status" :items="['pending', 'completed', 'failed']" label="Payment Status" required />
              </VCol>

              <!-- Payment Method -->
              <VCol cols="4">
                <VSelect v-model="editOrder.payment_method" :items="['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery']" label="Payment Method" required />
              </VCol>
            </VRow>

            <VRow>
              <!-- Transaction ID -->
              <VCol cols="4">
                <VTextField v-model="editOrder.transaction_id" label="Transaction ID" required />
              </VCol>

              <!-- Order Date -->
              <VCol cols="4">
                <VTextField v-model="editOrder.order_date" label="Order Date" type="date" required />
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
        <VCardText>Are you sure you want to delete this order?</VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" @click="deleteOrder">Delete</VBtn>
          </VCol>
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

const orders = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false);  // New modal for order details

const editOrder = ref({
  id: '',
  customer_name: '',
  product_id: null,
  quantity: 1,
  status: 'pending',
  order_number: '',
  total_amount: 0,
  shipping_address: '',
  shipping_city: '',
  shipping_state: '',
  shipping_postal_code: '',
  shipping_country: '',
  shipping_phone: '',
  payment_status: '',
  payment_method: '',
  transaction_id: '',
  order_date: '',
});


const infoOrder = ref({
  // existing fields
  orderItems: [], // Add this line to hold the order items data
});

const statuses = ref([
  { label: 'Pending', value: 'pending' },
  { label: 'Processing', value: 'processing' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' },
]);

const fetchOrders = async (page = 1) => {
  const token = localStorage.getItem('authToken');
  const response = await axios.get(`${BASE_URL}/orders?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  orders.value = response.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

const openEditModal = (order) => {
  editOrder.value = { ...order };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const openInfoModal = async (order) => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`${BASE_URL}/orders/${order.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    infoOrder.value = response.data;
    infoModal.value = true;
  } catch (error) {
    $toast.error('Error fetching order details: ' + error.response.data.message);
  }
};

const closeInfoModal = () => {
  infoModal.value = false;
};

const updateOrder = async () => {
  const token = localStorage.getItem('authToken');
  try {
    await axios.put(`${BASE_URL}/orders/${editOrder.value.id}`, editOrder.value, {
      headers: { Authorization: `Bearer ${token}` },
    });
    $toast.success('Order updated successfully!');
    fetchOrders(currentPage.value);
    closeEditModal();
  } catch (error) {
    $toast.error('Error updating order: ' + error.response.data.message);
  }
};

const openDeleteModal = (order) => {
  editOrder.value = { ...order };
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteOrder = async () => {
  const token = localStorage.getItem('authToken');
  try {
    await axios.delete(`${BASE_URL}/orders/${editOrder.value.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    $toast.success('Order deleted successfully!');
    fetchOrders(currentPage.value);
    closeDeleteModal();
  } catch (error) {
    $toast.error('Error deleting order: ' + error.response.data.message);
  }
};

const onPageChange = (page) => {
  fetchOrders(page);
};

onMounted(() => {
  fetchOrders();
});
</script>
