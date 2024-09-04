<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';

// Toast notification
const $toast = useToast();

// Form fields
const customer_name = ref('');
const product_id = ref(null);
const quantity = ref(1);
const status = ref('pending');
const products = ref([]);
const users = ref([]);

// Additional fields
const user_id = ref('');
const useSelectForUserId = ref(false); // Checkbox toggle
const order_number = ref('');
const total_amount = ref(0);
const shipping_address = ref('');
const shipping_city = ref('');
const shipping_state = ref('');
const shipping_postal_code = ref('');
const shipping_country = ref('');
const shipping_phone = ref('');
const payment_status = ref('');
const payment_method = ref('');
const transaction_id = ref('');
const order_date = ref('');

// Router instance
const router = useRouter();

// Fetch products and users when the component mounts
onMounted(async () => {
  const token = localStorage.getItem('authToken');

  try {
    // Fetch products
    const productResponse = await axios.get('http://127.0.0.1:8000/api/products', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    products.value = productResponse.data;

    // Fetch users (only if the select dropdown is needed)
    if (useSelectForUserId.value) {
      await fetchUsers();
    }
  } catch (error) {
    $toast.error('Error fetching data: ' + (error.response?.data?.message || error.message));
    console.error('Error fetching data:', error);
  }
});

// Fetch users function
const fetchUsers = async () => {
  const token = localStorage.getItem('authToken');
  
  try {
    const userResponse = await axios.get('http://127.0.0.1:8000/api/customers', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    users.value = userResponse.data;
    console.log(userResponse)
  } catch (error) {
    $toast.error('Error fetching users: ' + (error.response?.data?.message || error.message));
    console.error('Error fetching users:', error);
  }
};

// Watch the toggle and fetch users if necessary
watch(useSelectForUserId, (newValue) => {
  if (newValue && users.value.length === 0) {
    fetchUsers();
  }
});

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');

  try {
    const orderData = {
      customer_name: customer_name.value,
      product_id: product_id.value,
      quantity: quantity.value,
      status: status.value,
      user_id: user_id.value, // Add user ID
      order_number: order_number.value,
      total_amount: total_amount.value,
      shipping_address: shipping_address.value,
      shipping_city: shipping_city.value,
      shipping_state: shipping_state.value,
      shipping_postal_code: shipping_postal_code.value,
      shipping_country: shipping_country.value,
      shipping_phone: shipping_phone.value,
      payment_status: payment_status.value,
      payment_method: payment_method.value,
      transaction_id: transaction_id.value,
      order_date: order_date.value,
    };

    const response = await axios.post('http://127.0.0.1:8000/api/orders', orderData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    $toast.success('Order added successfully!');
    console.log('Order added successfully:', response.data);

    // Optionally, reset the form or redirect
    resetForm();
    // router.push('/orders');
  } catch (error) {
    $toast.error('Error adding order: ' + (error.response?.data?.message || error.message));
    console.error('Error adding order:', error);
  }
};

// Optional: Function to reset the form
const resetForm = () => {
  customer_name.value = '';
  product_id.value = null;
  quantity.value = 1;
  status.value = 'pending';
  user_id.value = '';
  useSelectForUserId.value = false;
  order_number.value = '';
  total_amount.value = 0;
  shipping_address.value = '';
  shipping_city.value = '';
  shipping_state.value = '';
  shipping_postal_code.value = '';
  shipping_country.value = '';
  shipping_phone.value = '';
  payment_status.value = '';
  payment_method.value = '';
  transaction_id.value = '';
  order_date.value = '';
};
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <!-- Customer Name -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="customer_name"
          label="Customer Name"
          placeholder="Enter customer name"
          required
        />
      </VCol>

      <!-- User ID (toggle between select and input) -->
      <VCol cols="12" md="4">
        <VCheckbox
          v-model="useSelectForUserId"
          label="Select User ID"
        />
      </VCol>
      
      <!-- Conditionally render based on the checkbox -->
      <VCol cols="12" md="4">
        <template v-if="useSelectForUserId">
          <VSelect
            v-model="user_id"
            label="Choose User"
            :items="users"
            item-title="name"
            item-value="id"
            required
          />
        </template>
        <template v-else>
          <VTextField
            v-model="user_id"
            label="User ID"
            placeholder="Enter user ID"
            required
          />
        </template>
      </VCol>

      <!-- Product -->
      <VCol cols="12" md="4">
        <VSelect
          v-model="product_id"
          label="Choose Product"
          :items="products"
          item-title="name"
          item-value="id"
          density="compact"
          required
        />
      </VCol>

      <!-- Quantity -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="quantity"
          label="Quantity"
          placeholder="Enter quantity"
          type="number"
          min="1"
          required
        />
      </VCol>

      <!-- Order Status -->
      <VCol cols="12" md="4">
        <VSelect
          v-model="status"
          label="Order Status"
          :items="['pending', 'processing', 'shipped', 'delivered', 'cancelled']"
          required
        />
      </VCol>

      <!-- Order Number -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="order_number"
          label="Order Number"
          placeholder="Enter order number"
          required
        />
      </VCol>

      <!-- Total Amount -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="total_amount"
          label="Total Amount"
          placeholder="Enter total amount"
          type="number"
          required
        />
      </VCol>

      <!-- Shipping Address -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_address"
          label="Shipping Address"
          placeholder="Enter shipping address"
          required
        />
      </VCol>

      <!-- Shipping City -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_city"
          label="Shipping City"
          placeholder="Enter shipping city"
          required
        />
      </VCol>

      <!-- Shipping State -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_state"
          label="Shipping State"
          placeholder="Enter shipping state"
          required
        />
      </VCol>

      <!-- Shipping Postal Code -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_postal_code"
          label="Shipping Postal Code"
          placeholder="Enter shipping postal code"
          required
        />
      </VCol>

      <!-- Shipping Country -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_country"
          label="Shipping Country"
          placeholder="Enter shipping country"
          required
        />
      </VCol>

      <!-- Shipping Phone -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="shipping_phone"
          label="Shipping Phone"
          placeholder="Enter shipping phone"
          required
        />
      </VCol>

      <!-- Payment Status -->
      <VCol cols="12" md="4">
        <VSelect
          v-model="payment_status"
          label="Payment Status"
          :items="['pending', 'completed', 'failed']"
          required
        />
      </VCol>

      <!-- Payment Method -->
      <VCol cols="12" md="4">
        <VSelect
          v-model="payment_method"
          label="Payment Method"
          :items="['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery']"
          required
        />
      </VCol>

      <!-- Transaction ID -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="transaction_id"
          label="Transaction ID"
          placeholder="Enter transaction ID"
          required
        />
      </VCol>

      <!-- Order Date -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="order_date"
          label="Order Date"
          placeholder="Enter order date"
          type="date"
          required
        />
      </VCol>

      <!-- Buttons -->
      <VCol cols="12" class="d-flex gap-4">
        <VBtn type="submit">
          Add Order
        </VBtn>
        <VBtn type="reset" @click="resetForm">
          Reset
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
