<template>
  <div>
    <!-- Header Buttons -->
    <div class="d-flex align-items-center mb-4 ml-4">
      <VBtn size="small" title="Add Inventory Update" color="secondary" @click="openAddModal">
        <i class="ri-add-circle-line"></i> Add Inventory Update
      </VBtn>
    </div>

    <!-- Inventory Updates Table -->
    <VTable>
      <thead>
      <tr>
        <th class="text-uppercase text-center">Supplier</th>
        <th class="text-uppercase text-center">Product</th>
        <th class="text-uppercase text-center">Purchase Price</th>
        <th class="text-uppercase text-center">Purchase Date</th>
        <th class="text-uppercase text-center">Quantity</th>
        <th class="text-uppercase text-center">Actions</th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="item in inventory" :key="item.id">
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

    <VPagination v-model="currentPage" :length="totalPages" @page-change="fetchInventory" />

    <!-- Add/Edit Modal -->
    <VDialog v-model="inventoryModal" max-width="800px">
      <VCard>
        <VCardTitle>{{ modalTitle }}</VCardTitle>
        <VCardText>
          <VForm ref="inventoryForm">
            <VRow>
              <VCol cols="6">
                <VSelect v-model="inventoryData.supplier_id" :items="suppliers" label="Supplier" item-title="name" item-value="id" />
              </VCol>
              <VCol cols="6">
                <VSelect v-model="inventoryData.product_id" :items="products" label="Product" item-title="name" item-value="id" />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="6">
                <VTextField v-model="inventoryData.purchase_price" label="Purchase Price" type="number" min="0" />
              </VCol>
              <VCol cols="6">
                <VTextField v-model="inventoryData.quantity" label="Quantity" type="number" min="0" />
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
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="success" @click="saveInventory">Save</VBtn>
            <VBtn color="error" @click="closeModal">Cancel</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- View Modal -->
    <VDialog v-model="viewModal" max-width="800px">
      <VCard>
        <VCardTitle>Inventory Details</VCardTitle>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <strong>Supplier:</strong> {{ inventoryData.supplier.name|| 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Product:</strong> {{ inventoryData.product.name || 'N/A' }}
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="6">
              <strong>Purchase Price:</strong> ${{ inventoryData.purchase_price || 'N/A' }}
            </VCol>
            <VCol cols="6">
              <strong>Quantity:</strong> {{ inventoryData.quantity || 'N/A' }}
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12">
              <strong>Purchase Date:</strong> {{ inventoryData.purchase_date || 'N/A' }}
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex justify-end">
            <VBtn color="secondary" @click="closeViewModal">Close</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Modal -->
    <VDialog v-model="deleteModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Inventory</VCardTitle>
        <VCardText>Are you sure you want to delete this inventory record?</VCardText>
        <VCardActions>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteInventoryConfirm">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const inventory = ref([]);
const suppliers = ref([]);
const products = ref([]);
const inventoryData = ref({ supplier_id: null, product_id: null, purchase_price: '', purchase_date: '', quantity: 0 });
const currentPage = ref(1);
const totalPages = ref(1);

const inventoryModal = ref(false);
const viewModal = ref(false);
const deleteModal = ref(false);
const modalTitle = ref('Add Inventory Update');

const deleteInventoryId = ref(null);

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
    suppliers.value = []; // Ensure these remain arrays
    products.value = [];
  }
};


const openAddModal = () => {
  modalTitle.value = 'Add Inventory Update';
  inventoryData.value = { supplier_id: null, product_id: null, purchase_price: '', purchase_date: '', quantity: 0 };
  inventoryModal.value = true;
};

const viewInventory = (item) => {
  modalTitle.value = 'View Inventory Details';
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
      // Update existing inventory
      await axios.put(`${BASE_URL}/inventory/${inventoryData.value.id}`, inventoryData.value, {
        headers: { Authorization: `Bearer ${token}` }
      });
      $toast.success('Inventory updated successfully!');
    } else {
      // Add new inventory
      await axios.post(`${BASE_URL}/inventory`, inventoryData.value, {
        headers: { Authorization: `Bearer ${token}` }
      });
      $toast.success('Inventory added successfully!');
    }
    inventoryModal.value = false;
    fetchInventory();
  } catch (error) {
    console.error('Failed to save inventory:', error);
    $toast.error('Error saving inventory data.');
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

const closeModal = () => {
  inventoryModal.value = false;
};

const closeViewModal = () => {
  viewModal.value = false;
};

const closeDeleteModal = () => {
  deleteModal.value = false;
};

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
