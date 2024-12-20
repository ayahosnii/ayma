<template>
  <div>
    
        <div class="d-flex align-items-center justify-between gap-2 mb-4">
          <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" href="/orders/add">
            <i class="ri-add-circle-line"></i> Add Order
          </VBtn>
          <VTextField
            v-model="searchQuery"
            label="Search Orders"
            class="ml-4"
            hide-details
            clearable
            dense
            style="max-inline-size: 300px;"
            placeholder="Search by number or name..."
          />
        </div>
    
        <!-- New Status Filter -->
        <VRow class="mb-4">
        <VCol cols="12" md="4" class="mx-4">
          <VSelect
            v-model="selectedStatus"
            :items="statuses"
            label="Filter by Status"
            item-title="label"
            item-value="value"
            clearable
          />
        </VCol>
        </VRow>
        <VTable>
          <thead>
        <tr>
          <th class="text-uppercase text-center">Order Number</th>
          <th class="text-uppercase text-center">Customer</th>
          <th class="text-uppercase text-center">Total Amount</th>
          <th class="text-uppercase text-center">Status</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="order in filteredOrders" :key="order.id">
          <td class="text-center">{{ order.order_number }}</td>
          <td class="text-center">{{ order.user.name }}</td>
          <td class="text-center">${{ order.total_amount }}</td>
          <td class="text-center">
            <VSelect
              v-model="order.status"
              :items="statuses"
              item-title="label"
              item-value="value"
              @update:modelValue="(newStatus) => updateOrderStatus(order.id, newStatus)"              dense
              hide-details
              clearable
            />
          </td>
          <td class="text-center">
            <VBtn size="small" title="Info" color="info" @click="openInfoModal(order)">
              <i class="ri-information-line"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(order)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(order.id)">
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
                    <th class="text-uppercase text-center">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in infoOrder.order_items" :key="item.id">
                    <td class="text-center">{{ item.product_name }}</td>
                    <td class="text-center">{{ item.quantity }}</td>
                    <td class="text-center">${{ item.price }}</td>
                    <td class="text-center">${{ item.total }}</td>
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
              <!-- Customer Name -->
              <VCol cols="4">
                <VTextField v-model="editOrder.user.name" label="Customer Name" required />
              </VCol>

              <!-- Status -->
              <VCol cols="4">
                <VSelect v-model="editOrder.status" :items="statuses" item-title="label" item-value="value" label="Status" required />
              </VCol>

              <!-- Transaction ID -->
            <VCol cols="4">
              <VTextField v-model="editOrder.transaction_id" label="Transaction ID" required />
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
                <VSelect v-model="editOrder.payment_status" :items="['pending', 'paid', 'failed']" label="Payment Status" required />
              </VCol>

              <!-- Payment Method -->
              <VCol cols="4">
                <VSelect v-model="editOrder.payment_method" :items="['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery']" label="Payment Method" required />
              </VCol>
            </VRow>

            <VRow>
              
              <!-- Order Date -->
              <VCol cols="4">
                <VTextField v-model="editOrder.order_date" label="Order Date" type="date" required />
              </VCol>
            </VRow>
            
            <!-- Product Items Table in Edit Modal -->
            <VRow>
              <VCol cols="12" class="mt-4">
                <h3>Order Items</h3>
                <VTable>
                  <thead>
                    <tr>
                      <th class="text-uppercase text-center">Product Name</th>
                      <th class="text-uppercase text-center">Quantity</th>
                      <th class="text-uppercase text-center">Price</th>
                      <th class="text-uppercase text-center">Total</th>
                      <th class="text-uppercase text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in editOrder.order_items" :key="item.id">
                      <td class="text-center">{{ item.product_name }}</td>
                      <td class="text-center">
                        <VTextField 
                          v-model.number="item.quantity" 
                          type="number" 
                          min="1" 
                          @input="updateItemTotal(item)"
                        />
                      </td>
                      <td class="text-center">${{ item.price }}</td>
                      <td class="text-center">${{ item.total }}</td>
                      <td class="text-center">
                        <VBtn color="error" @click="removeItem(index)"><i class="ri-close-line"></i></VBtn>
                      </td>
                    </tr>
                  </tbody>
                </VTable>
              </VCol>
            </VRow>

            <VRow>
              <!-- Total Amount (Automatically updated) -->
              <VCol cols="4">
                <VTextField v-model="editOrder.total_amount" label="Total Amount" readonly />
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
            <VBtn color="error" @click="deleteOrder">Delete</VBtn>
            <VBtn color="success" @click="closeDeleteModal">Cancel</VBtn>
          </VCol>
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

const orders = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false);

const editOrder = ref({
  customer_name: '',
  status: 'pending',
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
  order_items: [],
});

const infoOrder = ref({});
const deleteOrderData = ref(null);
const searchQuery = ref('');


// Computed property to filter orders based on the search query and selected status
const filteredOrders = computed(() => {
  let filtered = orders.value;

  // Apply search query filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (order) =>
        order.order_number.toLowerCase().includes(query) ||  // Search by order number
        order.user.name.toLowerCase().includes(query)         // Search by customer name
    );
  }

  // Apply status filter
  if (selectedStatus.value) {
    filtered = filtered.filter((order) => order.status === selectedStatus.value);
  }

  return filtered;
});


// Define the available statuses for filtering
const statuses = [
  { label: 'Pending', value: 'pending' },
  { label: 'Processing', value: 'processing' },
  { label: 'Shipped', value: 'shipped' },
  { label: 'Delivered', value: 'delivered' },
  { label: 'Cancelled', value: 'cancelled' },
];

// New property for status filter
const selectedStatus = ref(null);

// Computed property to filter orders based on selected status
// const filteredOrders = computed(() => {
//   return orders.value.filter(order => {
//     return selectedStatus.value ? order.status === selectedStatus.value : true;
//   });
// });
// Handle status update in the table and send the change to the API
const updateOrderStatus = async (orderId, newStatus) => {
  const token = localStorage.getItem('authToken');
  try {
    // Send the updated status to the backend
    await axios.put(`${BASE_URL}/orders/${orderId}/status`,
      { status: newStatus },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    );
    $toast.success('Order status updated successfully!');
    fetchOrders(); // Re-fetch the orders to ensure data is up-to-date
  } catch (error) {
    $toast.error('Error updating order status: ' + error.response.data.message);
  }
};


// Fetch orders from the API
const fetchOrders = async (page = 1) => {
  const token = localStorage.getItem('authToken');
  const response = await axios.get(`${BASE_URL}/orders?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  orders.value = response.data;
  totalPages.value = response.data.last_page;
  currentPage.value = response.data.current_page;
};

// Open Edit Modal and load order details
const openEditModal = (order) => {
  editOrder.value = { ...order, order_items: [...order.order_items] };
  editModal.value = true;
};

// Close Edit Modal
const closeEditModal = () => {
  editModal.value = false;
};

// Open Info Modal and load order details
const openInfoModal = (order) => {
  infoOrder.value = { ...order, order_items: [...order.order_items] };
  infoModal.value = true;
};

// Close Info Modal
const closeInfoModal = () => {
  infoModal.value = false;
};

// Open Delete Modal and store order details
const openDeleteModal = (order) => {
  deleteOrderData.value = order;
  deleteModal.value = true;
};

// Close Delete Modal
const closeDeleteModal = () => {
  deleteModal.value = false;
};

// Delete order
const deleteOrder = async () => {
  const token = localStorage.getItem('authToken');
  try {
    await axios.delete(`${BASE_URL}/orders/${deleteOrderData.value.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    $toast.success('Order deleted successfully!');
    closeDeleteModal();
    fetchOrders();
  } catch (error) {
    $toast.error('Error deleting order: ' + error.response.data.message);
  }
};

// Update total for each item
const updateItemTotal = (item) => {
  item.total = item.quantity * item.price;
  updateOrderTotal();
};

// Recalculate total order amount
const updateOrderTotal = () => {
  const total = editOrder.value.order_items.reduce((acc, item) => acc + item.total, 0);
  editOrder.value.total_amount = total.toFixed(2);
};

// Remove an item from the order
const removeItem = (index) => {
  editOrder.value.order_items.splice(index, 1);
  updateOrderTotal();
};

const updateOrder = async () => {
  const token = localStorage.getItem('authToken');
  try {
    await axios.put(`${BASE_URL}/orders/${editOrder.value.id}`, editOrder.value, {
      headers: { Authorization: `Bearer ${token}` },
    });
    $toast.success('Order updated successfully!');
    closeEditModal();
    fetchOrders();
  } catch (error) {
    $toast.error('Error updating order: ' + error.response.data.message);
  }
};

// On page change handler
const onPageChange = (newPage) => {
  fetchOrders(newPage);
};

onMounted(() => {
  fetchOrders();
});
</script>
