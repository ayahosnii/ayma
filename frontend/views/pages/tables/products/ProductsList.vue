<template>
  <div>
    <!-- Container for buttons -->
    <div class="d-flex align-items-center mb-4 ml-4">
      <VBtn size="small" title="Add Product" type="button" color="secondary" href="/products/add">
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
          <th class="text-uppercase text-center">Slug</th>
          <th class="text-uppercase text-center">Price</th>
          <th class="text-uppercase text-center">Discount Price</th>
          <th class="text-uppercase text-center">Stock</th>
          <th class="text-uppercase text-center">Category</th>
          <th class="text-uppercase text-center">Color</th>
          <th class="text-uppercase text-center">Size</th>
          <th class="text-uppercase text-center">Featured</th>
          <th class="text-uppercase text-center">Image</th>
          <th class="text-uppercase text-center">Description</th>
          <th class="text-uppercase text-center">Short Description</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="item in products" :key="item.id">
          <td class="text-center">{{ item.name }}</td>
          <td class="text-center">{{ item.sku }}</td>
          <td class="text-center">{{ item.slug }}</td>
          <td class="text-center">{{ item.price }}</td>
          <td class="text-center">{{ item.discount_price || '-' }}</td>
          <td class="text-center">{{ item.stock }}</td>
          <td class="text-center">{{ item.category ? item.category.name : '-' }}</td>
          <td class="text-center">{{ item.color ? item.color.name : '-' }}</td>
          <td class="text-center">{{ item.size ? item.size.name : '-' }}</td>
          <td class="text-center">{{ item.is_featured ? 'Yes' : 'No' }}</td>
          <td class="text-center">
            <img :src="getImageUrl(item.primaryImage?.image_path)" alt="Product Image" class="slide-image mt-1" />
          </td>
          <td class="text-center">{{ item.description || '-' }}</td>
          <td class="text-center">{{ item.short_description || '-' }}</td>
          <td class="text-center">
            <VBtn size="small" title="Edit" color="warning" @click="openEditModal(item)">
              <i class="ri-edit-fill"></i>
            </VBtn>
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
                <VTextField v-model="editProduct.name" label="Name" placeholder="Product Name" @input="onNameChange" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.sku" label="SKU" placeholder="Product SKU" :disabled="editProduct.skuChanged" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.slug" label="Slug" placeholder="Product Slug" :disabled="slugDisabled" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="4">
                <VTextField v-model="editProduct.price" label="Price" placeholder="Product Price" type="number" min="0" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.discount_price" label="Discount Price" placeholder="Discounted Price (optional)" type="number" min="0" />
              </VCol>
              <VCol cols="4">
                <VTextField v-model="editProduct.stock" label="Stock" placeholder="Available Stock" type="number" min="0" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="4">
                <VSelect v-model="editProduct.category_id" label="Category" :items="categories" item-title="name" item-value="id" density="compact" />
              </VCol>
              <VCol cols="4">
                <VSelect v-model="editProduct.color_id" label="Color" :items="colors" item-title="name" item-value="id" density="compact" />
              </VCol>
              <VCol cols="4">
                <VSelect v-model="editProduct.size_id" label="Size" :items="sizes" item-title="name" item-value="id" density="compact" />
              </VCol>
              
            </VRow>
            <VRow>
              <VCol cols="6">
                <VTextField v-model="editProduct.description" label="Description" placeholder="Product Description (optional)" multiline />
              </VCol>
              <VCol cols="6">
                <VTextField v-model="editProduct.short_description" label="Short Description" placeholder="Short Description (optional)" multiline />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12" md="6">
                <VFileInput v-model="editProduct.image" label="Image" placeholder="Select an image" accept="image/*" @change="handleEditImageUpload" />
              </VCol>
              <VCol cols="12" md="6">
                <VSwitch v-model="editProduct.is_featured" label="Featured" inset />
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
import { onMounted, ref, watch } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const products = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const editModal = ref(false);
const deleteModal = ref(false);
const infoModal = ref(false);

const editProduct = ref({
  id: '', name: '', sku: '', slug: '', price: '', discount_price: '', stock: '', category_id: null, color_id: null, size_id: null, is_featured: 0, description: '', short_description: '', image: ''
});
const originalSlug = ref('');
const deleteProductId = ref(null);
const infoProduct = ref({
  id: '', name: '', sku: '', slug: '', price: '', discount_price: '', stock: '', category: null, color: null, size: null, is_featured: 0, description: '', short_description: '', primaryImage: null
});
const colors = ref([]);
const sizes = ref([]);
const categories = ref([]);

const fetchProducts = async (page = 1) => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/products?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      products.value = response.data; // Adjusted to match typical API response structure
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch products:', error);
  }
};

const fetchColorsAndSizes = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const [colorsResponse, sizesResponse, categoriesResponse] = await Promise.all([
      axios.get(`${BASE_URL}/colors`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(`${BASE_URL}/sizes`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(`${BASE_URL}/categories`, { headers: { Authorization: `Bearer ${token}` } })
    ]);

    colors.value = colorsResponse.data;
    sizes.value = sizesResponse.data;
    categories.value = categoriesResponse.data;
  } catch (error) {
    console.error('Failed to fetch colors, sizes, and categories:', error);
  }
};

const getImageUrl = (path) => {
  return path ? `http://127.0.0.1:8000/storage/${path}` : 'placeholder_image_url';
};

const generateSlug = (name) => {
  return name
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const openEditModal = (item) => {
  editProduct.value = { ...item, image: '', category_id: item.category_id || null, color_id: item.color_id || null, size_id: item.size_id || null };
  originalSlug.value = item.slug;
  editModal.value = true;
};

const closeEditModal = () => {
  editModal.value = false;
};

const handleEditImageUpload = (event) => {
  const file = event.target.files[0];
  editProduct.value.image = file;
};

const updateProduct = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const productData = {
        name: editProduct.value.name,
        sku: editProduct.value.sku,
        slug: editProduct.value.slug,
        price: editProduct.value.price,
        discount_price: editProduct.value.discount_price || null,
        stock: editProduct.value.stock,
        category_id: editProduct.value.category_id,
        color_id: editProduct.value.color_id,
        size_id: editProduct.value.size_id,
        is_featured: editProduct.value.is_featured,
        description: editProduct.value.description,
        short_description: editProduct.value.short_description
      };

      if (editProduct.value.slug === originalSlug.value) {
        delete productData.slug;
      }

      if (editProduct.value.image instanceof File) {
        const formData = new FormData();
        Object.keys(productData).forEach(key => {
          formData.append(key, productData[key]);
        });
        formData.append('image', editProduct.value.image);

        await axios.put(`${BASE_URL}/products/${editProduct.value.id}`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data',
          },
        });
      } else {
        await axios.put(`${BASE_URL}/products/${editProduct.value.id}`, productData, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      }

      $toast.success('Product updated successfully!');
      fetchProducts(currentPage.value);
      closeEditModal();
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to update product:', error);
    $toast.error('Error updating product: ' + (error.response?.data?.message || error.message));
  }
};

const openDeleteModal = (item) => {
  deleteProductId.value = item.id;
  deleteModal.value = true;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

const deleteProduct = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token && deleteProductId.value) {
      await axios.delete(`${BASE_URL}/products/${deleteProductId.value}`, {
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

const openInfoModal = (item) => {
  infoProduct.value = { ...item };
  infoModal.value = true;
};

const closeInfoModal = () => {
  infoModal.value = false;
};

const onPageChange = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  fetchProducts(currentPage.value);
  fetchColorsAndSizes();
});

watch(currentPage, (newPage) => fetchProducts(newPage));

watch(() => editProduct.value.name, (newName) => {
  if (!editModal.value || editProduct.value.slug === originalSlug.value) {
    editProduct.value.slug = generateSlug(newName);
  }
});
</script>

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}

.text-center {
  text-align: center;
}

.slide-image {
  max-block-size: 50px;
  max-inline-size: 50px;
}
</style>
