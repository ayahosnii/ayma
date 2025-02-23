<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <!-- Row for Name and SKU -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="name"
          label="Name"
          placeholder="Product Name"
          required
        />
      </VCol>

      <VCol cols="12" md="6" class="d-flex align-center">
        <VTextField
          v-model="sku"
          label="SKU"
          placeholder="Product SKU"
          required
          class="flex-grow-1"
        />
        <VBtn class="ml-2" @click="generateSKU">Generate SKU</VBtn>
      </VCol>

      <!-- Row for Slug and Is Featured -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="slug"
          label="Slug"
          placeholder="Product Slug"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VSwitch
          v-model="is_featured"
          label="Featured"
          inset
        />
      </VCol>

      <!-- Row for Price and Discount Price -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="price"
          label="Price"
          placeholder="Product Price"
          type="number"
          min="0"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="discount_price"
          label="Discount Price"
          placeholder="Discount Price (optional)"
          type="number"
          min="0"
        />
      </VCol>

      <!-- Row for Stock and Low Stock Threshold -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="stock"
          label="Stock"
          placeholder="Available Stock"
          type="number"
          min="0"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="low_stock_threshold"
          label="Low Stock Threshold"
          placeholder="Low Stock Threshold (optional)"
          type="number"
          min="0"
        />
      </VCol>

      <!-- Row for Purchase Price and Date -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="purchase_price"
          label="Purchase Price"
          placeholder="Purchase Price"
          type="number"
          min="0"
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="purchase_date"
          label="Purchase Date"
          placeholder="Purchase Date"
          type="date"
        />
      </VCol>

      <!-- Row for Category -->
      <VCol cols="12" md="6">
        <VSelect
          v-model="category_id"
          label="Category"
          :items="categories"
          item-title="name"
          item-value="id"
          density="compact"
          required
        />
      </VCol>

      <!-- Row for Color and Size -->
      <VCol cols="12" md="6">
        <VSelect
          v-model="color_id"
          label="Color"
          :items="colors"
          item-title="name"
          item-value="id"
          density="compact"
        />
      </VCol>

      <VCol cols="12" md="6">
        <VSelect
          v-model="size_id"
          label="Size"
          :items="sizes"
          item-title="name"
          item-value="id"
          density="compact"
        />
      </VCol>

      <!-- Row for Description and Short Description -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="description"
          label="Description"
          placeholder="Product Description (optional)"
          multiline
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="short_description"
          label="Short Description"
          placeholder="Short Description (optional)"
          multiline
        />
      </VCol>

      <!-- Row for Supplier -->
      <VCol cols="12" md="6">
        <VSelect
          v-model="supplier_id"
          label="Supplier"
          :items="suppliers"
          item-title="name"
          item-value="id"
          density="compact"
          required
        />
      </VCol>

      <!-- Row for Images -->
      <VCol cols="12" md="6">
        <VFileInput
          v-model="images"
          label="Images"
          placeholder="Select images"
          accept="image/*"
          multiple
          @change="handleImageUpload"
        />
      </VCol>

      <!-- Row for Buttons -->
      <VCol cols="12" class="d-flex gap-4">
        <VBtn type="submit">
          Add Product
        </VBtn>
        <VBtn type="reset" @click="resetForm">
          Reset
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

// Toast notification
const $toast = useToast();

// Form fields
const name = ref('');
const sku = ref('');
const is_featured = ref(false);
const price = ref('');
const discount_price = ref('');
const stock = ref('');
const low_stock_threshold = ref('');
const category_id = ref(null);
const color_id = ref(null);
const size_id = ref(null);
const images = ref([]);
const categories = ref([]);
const colors = ref([]);
const sizes = ref([]);
const suppliers = ref([]);
const description = ref('');
const short_description = ref('');
const supplier_id = ref(null);
const purchase_price = ref(''); // New field for purchase price
const purchase_date = ref(''); // New field for purchase date

// Generate slug automatically based on name
const slug = computed(() => {
  return name.value
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric characters with hyphens
    .replace(/(^-|-$)/g, ''); // Remove leading and trailing hyphens
});

// Fetch categories, colors, sizes, and suppliers when the component mounts
onMounted(async () => {
  const token = localStorage.getItem('authToken');

  try {
    const [categoriesResponse, colorsResponse, sizesResponse, suppliersResponse] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/categories', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }),
      axios.get('http://127.0.0.1:8000/api/colors', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }),
      axios.get('http://127.0.0.1:8000/api/sizes', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }),
      axios.get('http://127.0.0.1:8000/api/suppliers', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }),
    ]);

    categories.value = categoriesResponse.data;
    colors.value = colorsResponse.data;
    sizes.value = sizesResponse.data;
    suppliers.value = suppliersResponse.data.data;
  } catch (error) {
    $toast.error('Error fetching data: ' + (error.response?.data?.message || error.message));
    console.error('Error fetching data:', error);
  }
});

// Generate SKU
const generateSKU = () => {
  if (name.value) {
    sku.value = `${name.value.substring(0, 3).toUpperCase()}-${Date.now().toString().slice(-4)}`;
  } else {
    $toast.warning('Please enter a product name to generate SKU.');
  }
};

// Handle image upload
const handleImageUpload = (event) => {
  images.value = Array.from(event.target.files);
};

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');

  try {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('sku', sku.value);
    formData.append('slug', slug.value);
    formData.append('is_featured', is_featured.value ? '1' : '0');
    formData.append('price', price.value);
    formData.append('discount_price', discount_price.value || '');
    formData.append('stock', stock.value);
    formData.append('low_stock_threshold', low_stock_threshold.value || '');
    formData.append('category_id', category_id.value || '');
    formData.append('color_id', color_id.value || '');
    formData.append('size_id', size_id.value || '');
    formData.append('description', description.value || '');
    formData.append('short_description', short_description.value || '');
    formData.append('supplier_id', supplier_id.value || '');
    formData.append('purchase_price', purchase_price.value || '');
    formData.append('purchase_date', purchase_date.value || '');

    images.value.forEach((file, index) => {
      formData.append(`images[${index}]`, file);
    });

    await axios.post('http://127.0.0.1:8000/api/products', formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      },
    });

    $toast.success('Product added successfully!');
    resetForm();
  } catch (error) {
    $toast.error('Error adding product: ' + (error.response?.data?.message || error.message));
    console.error('Error adding product:', error);
  }
};

// Reset form
const resetForm = () => {
  name.value = '';
  slug.value = '';
  sku.value = '';
  is_featured.value = false;
  price.value = '';
  discount_price.value = '';
  stock.value = '';
  low_stock_threshold.value = '';
  category_id.value = null;
  color_id.value = null;
  size_id.value = null;
  description.value = '';
  short_description.value = '';
  supplier_id.value = null;
  purchase_price.value = '';
  purchase_date.value = '';
  images.value = [];
};
</script>
