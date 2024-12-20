<template>
  <div>
    <!-- Header: Add Inventory Button -->
    <div class="d-flex align-items-center justify-between gap-2 mb-4">
      <VBtn class="mb-4 ml-4" size="small" color="secondary" @click="openAddModal">
        <i class="ri-add-circle-line"></i> Add Inventory Update
      </VBtn>
    </div>

    <!-- Filter Section -->
    <VRow class="mb-4">
      <VCol cols="12" md="4" class="mx-4">
        <VSelect
          v-model="selectedSupplier"
          :items="suppliers"
          label="Filter by Supplier"
          item-title="name"
          item-value="id"
          clearable
        />
      </VCol>
    </VRow>

    <!-- Inventory Updates Table -->
    <VTable>
      <thead>
      <tr>
        <th class="text-center text-uppercase">Supplier</th>
        <th class="text-center text-uppercase">Product</th>
        <th class="text-center text-uppercase">Purchase Price</th>
        <th class="text-center text-uppercase">Purchase Date</th>
        <th class="text-center text-uppercase">Quantity</th>
        <th class="text-center text-uppercase">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="item in filteredInventory" :key="item.id">
        <td class="text-center">{{ item.supplier.name }}</td>
        <td class="text-center">{{ item.product.name }}</td>
        <td class="text-center">${{ item.purchase_price }}</td>
        <td class="text-center">{{ item.purchase_date || 'N/A' }}</td>
        <td class="text-center">{{ item.quantity }}</td>
        <td class="text-center">
          <VBtn size="small" color="info" @click="viewInventory(item)">
            <i class="ri-information-line"></i>
          </VBtn>&nbsp;
          <VBtn size="small" color="warning" @click="editInventory(item)">
            <i class="ri-edit-fill"></i>
          </VBtn>&nbsp;
          <VBtn size="small" color="error" @click="deleteInventory(item.id)">
            <i class="ri-delete-bin-line"></i>
          </VBtn>
        </td>
      </tr>
      </tbody>
    </VTable>

    <!-- Pagination -->
    <VPagination v-model="currentPage" :length="totalPages" @page-change="fetchInventory" />

    <!-- Add/Edit Modal -->
    <VDialog v-model="inventoryModal" max-width="600px">
      <VCard>
        <VCardTitle>{{ modalTitle }}</VCardTitle>
        <VCardText>
          <VForm ref="inventoryForm">
            <VRow>
              <VCol cols="6">
                <VSelect
                  v-model="inventoryData.supplier_id"
                  :items="suppliers"
                  label="Supplier"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol cols="6">
                <VSelect
                  v-model="inventoryData.product_id"
                  :items="products"
                  label="Product"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="6">
                <VTextField v-model="inventoryData.purchase_price" label="Purchase Price" type="number" />
              </VCol>
              <VCol cols="6">
                <VTextField v-model="inventoryData.quantity" label="Quantity" type="number" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VTextField v-model="inventoryData.purchase_date" label="Purchase Date" type="date" />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VBtn color="success" @click="saveInventory">Save</VBtn>
          <VBtn color="error" @click="closeModal">Cancel</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- View Modal -->
    <VDialog v-model="viewModal" max-width="500px">
      <VCard>
        <VCardTitle>Inventory Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <strong>Supplier:</strong> {{ inventoryData.supplier?.name || 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Product:</strong> {{ inventoryData.product?.name || 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Purchase Price:</strong> ${{ inventoryData.purchase_price || 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Quantity:</strong> {{ inventoryData.quantity || 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Purchase Date:</strong> {{ inventoryData.purchase_date || 'N/A' }}
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VBtn color="secondary" @click="closeViewModal">Close</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Confirmation Modal -->
    <VDialog v-model="deleteModal" max-width="400px">
      <VCard>
        <VCardTitle>Delete Inventory</VCardTitle>
        <VCardText>Are you sure you want to delete this inventory?</VCardText>
        <VCardActions>
          <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
          <VBtn color="success" text @click="deleteInventoryConfirm">Delete</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const inventory = ref([]);
const suppliers = ref([]);
const products = ref([]);
const inventoryData = ref({
  id: null,
  supplier_id: null,
  product_id: null,
  purchase_price: '',
  purchase_date: '',
  quantity: 0
});

const selectedSupplier = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);

const inventoryModal = ref(false);
const viewModal = ref(false);
const deleteModal = ref(false);
const modalTitle = ref('Add Inventory Update');
const deleteInventoryId = ref(null);


const filteredInventory = computed(() => {
  if (!selectedSupplier.value) return inventory.value;
  return inventory.value.filter(item => item.supplier.id === selectedSupplier.value);
});

const fetchInventory = async () => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`${BASE_URL}/inventory?page=${currentPage.value}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    inventory.value = response.data.data;
    totalPages.value = response.data.last_page || 1;
  } catch (error) {
    console.error('Failed to fetch inventory:', error);
    $toast.error('Failed to load inventory data.');
  }
};

const fetchDropdowns = async () => {
  const token = localStorage.getItem('authToken');
  try {
    const [supplierRes, productRes] = await Promise.all([
      axios.get(`${BASE_URL}/suppliers`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(`${BASE_URL}/products`, { headers: { Authorization: `Bearer ${token}` } })
    ]);
    suppliers.value = supplierRes.data.data || [];
    products.value = productRes.data || [];
  } catch (error) {
    console.error('Failed to fetch suppliers or products:', error);
    $toast.error('Failed to load dropdown data.');
  }
};

const openAddModal = () => {
  modalTitle.value = 'Add Inventory Update';
  inventoryData.value = { supplier_id: null, product_id: null, purchase_price: '', purchase_date: '', quantity: 0 };
  inventoryModal.value = true;
};

const viewInventory = (item) => {
  inventoryData.value = { ...item };
  viewModal.value = true;
};

const editInventory = (item) => {
  modalTitle.value = 'Edit Inventory Update';
  inventoryData.value = { ...item };
  inventoryModal.value = true;
};

const saveInventory = async () => {
  const token = localStorage.getItem('authToken');
  try {
    if (inventoryData.value.id) {
      await axios.put(`${BASE_URL}/inventory/${inventoryData.value.id}`, inventoryData.value, {
        headers: { Authorization: `Bearer ${token}` }
      });
      $toast.success('Inventory updated successfully!');
    } else {
      await axios.post(`${BASE_URL}/inventory`, inventoryData.value, {
        headers: { Authorization: `Bearer ${token}` }
      });
      $toast.success('Inventory added successfully!');
    }
    inventoryModal.value = false;
    fetchInventory();
  } catch (error) {
    console.error('Failed to save inventory:', error);
    $toast.error('Error saving inventory.');
  }
};

const deleteInventory = (id) => {
  deleteInventoryId.value = id;
  deleteModal.value = true;
};

const deleteInventoryConfirm = async () => {
  const token = localStorage.getItem('authToken');
  try {
    await axios.delete(`${BASE_URL}/inventory/${deleteInventoryId.value}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    $toast.success('Inventory deleted successfully!');
    deleteModal.value = false;
    fetchInventory();
  } catch (error) {
    console.error('Failed to delete inventory:', error);
    $toast.error('Error deleting inventory.');
  }
};

const closeModal = () => (inventoryModal.value = false);
const closeViewModal = () => (viewModal.value = false);
const closeDeleteModal = () => (deleteModal.value = false);

onMounted(() => {
  fetchInventory();
  fetchDropdowns();
});
</script>


<style scoped>
.text-uppercase {
  text-transform: uppercase;
}

.text-center {
  text-align: center;
}

</style>
