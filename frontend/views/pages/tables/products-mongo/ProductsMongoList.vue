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

    <!-- Product Table -->
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

    <!-- Pagination -->
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
    <!-- Product Details Modal -->
<VDialog v-model="infoModal" max-width="800px">
  <VCard>
    <VCardTitle>Product Details</VCardTitle>
    <VCardText>
      <!-- Product Information with two items per row -->
      <VRow>
        <VCol cols="6">
          <strong>Name:</strong> {{ infoProduct.name }}
        </VCol>
        <VCol cols="6">
          <strong>SKU:</strong> {{ infoProduct.sku }}
        </VCol>
      </VRow>
      <VRow>
        <VCol cols="6">
          <strong>Price:</strong> ${{ infoProduct.price }}
        </VCol>
        <VCol cols="6">
          <strong>Discount Price:</strong> ${{ infoProduct.discount_price || 'N/A' }}
        </VCol>
      </VRow>
      <VRow>
        <VCol cols="6">
          <strong>Stock:</strong> {{ infoProduct.stock }}
        </VCol>
        <VCol cols="6">
          <strong>Featured Product:</strong> {{ infoProduct.is_featured }}
        </VCol>
      </VRow>
      <VRow>
        <VCol cols="6">
          <strong>Description:</strong> {{ infoProduct.description || 'N/A' }}
        </VCol>
        <VCol cols="6">
          <strong>Category:</strong> {{ infoProduct.category }}
        </VCol>
      </VRow>
      <!-- Additional Attributes Table -->
      <VRow>
        <VCol cols="12">
          <strong>Additional Attributes:</strong>
          <VTable v-if="infoProduct.additional_attributes && Object.keys(infoProduct.additional_attributes).length">
            <thead>
              <tr>
                <th>Attribute</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(value, key) in infoProduct.additional_attributes" :key="key">
                <td>{{ formatKey(key) }}</td>
                <td>{{ value }}</td>
              </tr>
            </tbody>
          </VTable>
          <span v-else>No additional attributes</span>
        </VCol>
      </VRow>

      <!-- Images Section -->
      <VRow v-if="infoProduct.images && infoProduct.images.length">
        <VCol cols="12">
          <strong>Images:</strong>
          <div class="d-flex gap-2 flex-wrap">
            <img v-for="(imgUrl, index) in infoProduct.images" :key="index" :src="imgUrl" alt="Product Image" class="product-image" />
          </div>
        </VCol>
      </VRow>
      <VRow v-else>
        <VCol cols="12">
          <span>No images available</span>
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

const editProduct = ref({});
const infoProduct = ref({});
const baseImageUrl = "http://127.0.0.1:8000/storage/";
const deleteProductId = ref(null);
const categories = ref([]);

// Format keys for display
const formatKey = (key) => {
  const customLabels = {
    is_featured: 'Featured Product',
    discount_price: 'Discounted Price',
    additional_attributes: 'Additional Attributes',
    category: 'Category',
  };
  return customLabels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
};

const fetchProducts = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/products-mongo?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      products.value = response.data.products;
    totalPages.value = response.data.totalPages;
    currentPage.value = response.data.currentPage;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch products:', error);
  }
};


const fetchCategories = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/categories`, {
      headers: {
        'Authorization': `Bearer ${token}`, // Include your token here
      },
    });
    categories.value = response.data;
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
  infoProduct.value = {
    name: item.name,
    sku: item.sku,
    price: item.price,
    discount_price: item.discount_price,
    description: item.description,
    stock: item.stock,
    is_featured: item.is_featured ? 'Yes' : 'No',
    category: item.category_id,
    additional_attributes: item.additional_attributes || {},
    images: item.images ? item.images.map((img) => `${baseImageUrl}${img}`) : []
  };
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
    $toast.success('Product updated successfully');
    editModal.value = false;
    fetchProducts(currentPage.value);
  } catch (error) {
    console.error('Failed to update product:', error);
    $toast.error('Failed to update product');
  }
};

// Delete product
const deleteProduct = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteProductId.value) {
      await axios.delete(`${BASE_URL}/products-mongo/${deleteProductId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      $toast.success('Product deleted successfully!');
      fetchProducts(currentPage.value);
      closeDeleteModal();
    } else {
      console.error('No auth token found or delete product ID missing');
    }
  } catch (error) {
    console.error('Failed to delete product:', error);
    $toast.error('Error deleting product: ' + (error.response?.data?.message || error.message));
  }
};


const onPageChange = (page) => {
  fetchProducts(page);
};

// Initialize component
onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>

<style scoped>
.product-image {
  border-radius: 4px;
  block-size: auto;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 20%);
  inline-size: 200px;
}
</style>
