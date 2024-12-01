<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';

// Toast notification
const $toast = useToast();

// Form fields
const customer_name = ref('');
const products = ref([{ product_id: null, quantity: 1, stock: 0, error: '' }]);
const status = ref('pending');
const availableProducts = ref([]);
const users = ref([]);
const user_id = ref('');
const useSelectForUserId = ref(true);

// Additional fields
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

// New User Inputs
const newUserEmail = ref('');
const newUserName = ref('');
const newUserPassword = ref('');
const isNewUser = ref(false);

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
    availableProducts.value = productResponse.data;

    // Fetch users if the select dropdown is needed
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
    console.log(userResponse);
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

// Watch products to validate quantity against stock
watch(products, (newProducts) => {
  newProducts.forEach((product, index) => {
    if (product.product_id) {
      const selectedProduct = availableProducts.value.find(p => p.id === product.product_id);
      if (selectedProduct && product.quantity > selectedProduct.stock) {
        product.error = `Only ${selectedProduct.stock} items in stock.`;
      } else {
        product.error = '';
      }
    }
  });
}, { deep: true });

// Add a new product to the array
const addProduct = () => {
  products.value.push({ product_id: null, quantity: 1, stock: 0, error: '' });
};

// Remove a product from the array
const removeProduct = (index) => {
  products.value.splice(index, 1);
};

// Function to generate a random password
const generateRandomPassword = () => {
  const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let password = '';
  for (let i = 0; i < 8; i++) {
    password += charset[Math.floor(Math.random() * charset.length)];
  }
  newUserPassword.value = password;
};

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');
  let createdUserId = user_id.value;

  // Check if any product exceeds stock
  const hasStockError = products.value.some(product => product.error);
  if (hasStockError) {
    $toast.error('Please correct the product quantities before submitting.');
    return;
  }

  try {
    // Prepare orderData with user info conditionally
    const orderData = {
      customer_name: customer_name.value,
      products: products.value.map(product => ({
        product_id: product.product_id,
        quantity: product.quantity,
      })),
      status: status.value,
      user_id: createdUserId,
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

    if (isNewUser.value) {
      // Add new user details to orderData if needed
      Object.assign(orderData, {
        name: newUserName.value,
        email: newUserEmail.value,
        password: newUserPassword.value,
      });
    }

    // Submit the order
    await axios.post('http://127.0.0.1:8000/api/orders', orderData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    $toast.success('Order added successfully!');
    resetForm();
    // Optionally redirect after successful submission
    // router.push('/orders');
  } catch (error) {
    $toast.error('Error adding order: ' + (error.response?.data?.message || error.message));
    console.error('Error adding order:', error);
  }
};

// Function to generate a random order number
const generateOrderNumber = () => {
  const prefix = 'ORD'; // Optional prefix for clarity
  const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase(); // 6 random alphanumeric characters
  order_number.value = `${prefix}-${randomPart}`;
};

// Optional: Function to reset the form
const resetForm = () => {
  customer_name.value = '';
  products.value = [{ product_id: null, quantity: 1, stock: 0, error: '' }];
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
  newUserEmail.value = '';
  newUserName.value = '';
  newUserPassword.value = '';
  isNewUser.value = false;
};
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <!-- New User Checkbox -->
      <VCol cols="12" md="4">
        <VCheckbox v-model="isNewUser" label="New User?" />
      </VCol>

      <!-- Conditionally render based on isNewUser flag -->
      <template v-if="isNewUser">
        <!-- New User Email -->
        <VCol cols="12" md="4">
          <VTextField v-model="newUserEmail" label="Email" placeholder="Enter email" required />
        </VCol>
        <!-- New User Name -->
        <VCol cols="12" md="4">
          <VTextField v-model="newUserName" label="Name" placeholder="Enter name" required />
        </VCol>
        <!-- Random Password -->
        <VCol cols="12" md="4">
          <VTextField v-model="newUserPassword" label="Password" :readonly="true" placeholder="Generated password" />
          <VBtn @click="generateRandomPassword" color="primary" class="mt-2">Generate Password</VBtn>
        </VCol>
      </template>

      <template v-else>
        <!-- Existing User Dropdown -->
        <VCol cols="12" md="4">
          <VSelect
            v-model="user_id"
            label="Choose User"
            :items="users"
            item-title="name"
            item-value="id"
            required
          />
        </VCol>
      </template>

      <!-- Products -->
      <VCol cols="12">
        <h4>Products</h4>
        <VRow v-for="(product, index) in products" :key="index" class="mb-4">
          <!-- Product Select -->
          <VCol cols="6">
            <VSelect
              v-model="product.product_id"
              label="Choose Product"
              :items="availableProducts"
              item-title="name"
              item-value="id"
              required
            />
          </VCol>

          <!-- Quantity -->
          <VCol cols="4">
            <VTextField
              v-model="product.quantity"
              label="Quantity"
              type="number"
              min="1"
              :error="product.error !== ''"
              :error-messages="product.error"
              required
            />
          </VCol>

          <!-- Remove product button -->
          <VCol cols="2" class="d-flex align-center">
            <VBtn @click="removeProduct(index)" icon="ri-close-fill" class="mx-2" color="error" />
          </VCol>
        </VRow>

        <!-- Add Product Button -->
        <VCol cols="12">
          <VBtn @click="addProduct" icon="ri-add-line" color="warning">
          </VBtn>
        </VCol>
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
          placeholder="Generate order number"
          :readonly="true"
          required
        />
        <VBtn @click="generateOrderNumber" color="primary" class="mt-2">
          Generate Order Number
        </VBtn>
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
          :items="['pending', 'paid', 'failed']"
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
          type="date"
          required
        />
      </VCol>

      <!-- Submit and Reset Buttons -->
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
