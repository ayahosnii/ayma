<template>
  <div>
    <!-- Container for action buttons -->
    <div class="d-flex align-items-center mb-4 ml-4">
      <VBtn size="small" title="Add Product" type="button" color="secondary" href="/products-mongo/add">
        <i class="ri-add-circle-line"></i> Add Product
      </VBtn>
      <VBtn size="small" title="Colors" type="button" color="secondary" href="/colors/list" class="ml-2">
        <i class="ri-palette-line"></i> Colors
      </VBtn>
      <VBtn size="small" title="Sizes" type="button" color="secondary" href="/sizes/list" class="ml-2">
        <i class="ri-ruler-line"></i> Sizes
      </VBtn>
    </div>

    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase text-center">Name</th>
          <th class="text-uppercase text-center">SKU</th>
          <th class="text-uppercase text-center">Price</th>
          <th class="text-uppercase text-center">Stock</th>
          <th class="text-uppercase text-center">Category</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in products" :key="item._id">
          <td class="text-center">{{ item.name }}</td>
          <td class="text-center">{{ item.sku }}</td>
          <td class="text-center">${{ item.price }}</td>
          <td class="text-center">{{ item.stock }}</td>
          <td class="text-center">{{ item.category_id }}</td>
          <td class="text-center">
            <VBtn size="small" title="Info" color="info" @click="openInfoModal(item)">
              <i class="ri-information-line"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(item)">
              <i class="ri-edit-fill"></i>
            </VBtn>&nbsp;
            <VBtn size="small" title="Delete" color="error" @click="openDeleteModal(item)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>

    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />

    <!-- Edit Modal -->
    <VDialog v-model="editModal" max-width="800px">
      <VCard>
        <VCardTitle>Edit Product</VCardTitle>
        <VCardText>
          <VForm ref="editForm">
            <VRow>
              <VCol cols="4">
                <VTextField v-model="editProduct.name" label="Name" placeholder="Product Name" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.sku" label="SKU" placeholder="Product SKU" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.price" label="Price" placeholder="Product Price" type="number" min="0" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="4">
                <VTextField v-model="editProduct.discount_price" label="Discount Price" placeholder="Discount Price" type="number" min="0" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.stock" label="Stock" placeholder="Available Stock" type="number" min="0" />
              </VCol>
              <VCol cols="4">
                <VSelect v-model="editProduct.category_id" label="Category" :items="categories" item-title="name" item-value="_id" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField v-model="editProduct.description" label="Description" placeholder="Product Description" multiline />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="updateProduct">Save</VBtn>
            <VBtn color="error" @click="closeEditModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Product Details Modal -->
    <VDialog v-model="infoModal" max-width="800px">
      <VCard>
        <VCardTitle>Product Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <div><strong>Name:</strong> {{ infoProduct.name }}</div>
              <div><strong>SKU:</strong> {{ infoProduct.sku }}</div>
              <div><strong>Price:</strong> ${{ infoProduct.price }}</div>
              <div><strong>Stock:</strong> {{ infoProduct.stock }}</div>
              <div><strong>Category:</strong> {{ infoProduct.category_id }}</div>
              <div><strong>Description:</strong> {{ infoProduct.description || 'No description available.' }}</div>
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex justify-end">
            <VBtn color="secondary" @click="closeInfoModal">Close</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Product</VCardTitle>
        <VCardText>Are you sure you want to delete this product?</VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteProduct">Delete</VBtn>
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

const products = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false);

const editProduct = ref({
  _id: '', name: '', sku: '', price: 0, discount_price: null, stock: 0, category_id: null, description: ''
});
const infoProduct = ref({});
const deleteProductId = ref(null);
const categories = ref([]);

// Fetch products from the server
const fetchProducts = async (page = 1) => {
  try {
    const response = await axios.get(`${BASE_URL}/products-mongo?page=${page}`);
    products.value = response.data.products; // Adjust based on your API response
    totalPages.value = response.data.totalPages; // Adjust based on your API response
    currentPage.value = response.data.currentPage; // Adjust based on your API response
  } catch (error) {
    console.error('Failed to fetch products:', error);
    $toast.error('Failed to fetch products');
  }
};

// Fetch categories (if needed)
const fetchCategories = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/categories`);
    categories.value = response.data; // Adjust based on your API response
  } catch (error) {
    console.error('Failed to fetch categories:', error);
    $toast.error('Failed to fetch categories');
  }
};

// Open modals
const openEditModal = (item) => {
  editProduct.value = { ...item };
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const openInfoModal = (item) => {
  infoProduct.value = { ...item };
  infoModal.value = true;
};

const closeInfoModal = () => {
  infoModal.value = false;
};

const openDeleteModal = (item) => {
  deleteProductId.value = item._id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

// Update product
const updateProduct = async () => {
    try {
        await axios.put(`${BASE_URL}/products-mongo/${editProduct.value._id}`, editProduct.value);
        $toast.success('Product updated successfully!');
        fetchProducts(currentPage.value);
        closeEditModal();
    } catch (error) {
        console.error('Failed to update product:', error);
        $toast.error('Error updating product');
    }
};

// Delete product
const deleteProduct = async () => {
    try {
        await axios.delete(`${BASE_URL}/products-mongo/${deleteProductId.value}`);
        $toast.success('Product deleted successfully!');
        fetchProducts(currentPage.value);
        closeDeleteModal();
    } catch (error) {
        console.error('Failed to delete product:', error);
        $toast.error('Error deleting product');
    }
};

// Handle page change
const onPageChange = (page) => {
  fetchProducts(page);
};

// Fetch data on component mount
onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>

<style scoped>
/* Add any necessary styles here */
</style>
