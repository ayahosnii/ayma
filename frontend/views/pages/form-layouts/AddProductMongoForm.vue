<template>
  <VForm ref="form" @submit.prevent="handleSubmit" v-model="formValid">
    <VRow>
      <!-- Common fields for Name and SKU -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="name"
          label="Name"
          placeholder="Product Name"
          :rules="[rules.required]"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="sku"
          label="SKU"
          placeholder="Product SKU"
          :rules="[rules.required]"
          required
        />
      </VCol>

      <!-- Slug and Is Featured -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="slug"
          label="Slug"
          placeholder="Product Slug"
          :rules="[rules.required]"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VSwitch v-model="is_featured" label="Featured" inset />
      </VCol>

      <!-- Price and Discount Price -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="price"
          label="Price"
          placeholder="Product Price"
          type="number"
          :rules="[rules.required, rules.minValue(0)]"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VTextField
          v-model="discount_price"
          label="Discount Price"
          placeholder="Discount Price (optional)"
          type="number"
          :rules="[rules.minValue(0)]"
        />
      </VCol>

      <!-- Stock and Category -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="stock"
          label="Stock"
          placeholder="Available Stock"
          type="number"
          :rules="[rules.required, rules.minValue(0)]"
          required
        />
      </VCol>

      <VCol cols="12" md="6">
        <VSelect
          v-model="category_id"
          label="Category"
          :items="categories"
          item-title="name"
          item-value="id"
          :rules="[rules.required]"
          @change="loadSpecifications"
          required
        />
      </VCol>

      <!-- Dynamic Additional Attributes Section -->
      <VCol cols="12">
        <h4>Additional Attributes</h4>

        <div v-for="(attribute, index) in additional_attributes" :key="index" class="mb-4">
          <VRow>
            <VCol cols="5">
              <VTextField
                v-model="attribute.key"
                label="Attribute Key"
                placeholder="Enter attribute key"
                :rules="[rules.required]"
                required
              />
            </VCol>

            <VCol cols="5">
              <VSelect
                v-if="multiValueAttributes.includes(attribute.key)"
                v-model="attribute.value"
                :label="attribute.key.charAt(0).toUpperCase() + attribute.key.slice(1)"
                :items="getAttributeOptions(attribute.key)"
                multiple
                :rules="[rules.required]"
                required
              />
              <VTextField
                v-else
                v-model="attribute.value"
                :label="attribute.key.charAt(0).toUpperCase() + attribute.key.slice(1)"
                placeholder="Enter attribute value"
                :rules="[rules.required]"
                required
              />
            </VCol>

            <VCol cols="2" class="d-flex align-items-center">
              <VBtn title="Delete" color="error" icon @click="removeAttribute(index)">
                <i class="ri-delete-bin-line"></i>
              </VBtn>
            </VCol>
          </VRow>

          <!-- Dynamic Option Management for Colors, Sizes, etc. -->
          <div v-if="multiValueAttributes.includes(attribute.key)">
            <VRow>
              <VCol cols="9">
                <VTextField
                  v-model="newOption"
                  :label="'Add New ' + attribute.key.charAt(0).toUpperCase() + attribute.key.slice(1)"
                  placeholder="Enter new option"
                />
              </VCol>
              <VCol cols="3" class="d-flex align-items-center">
                <VBtn @click="addOption(attribute.key, newOption); newOption = ''">
                  Add Option
                </VBtn>
              </VCol>
            </VRow>
            
            <VChip
              v-for="option in getAttributeOptions(attribute.key)"
              :key="option"
              class="mr-2"
              close
              @click:close="removeOption(attribute.key, option)"
            >
              {{ option }}
            </VChip>
          </div>
        </div>

        <VBtn @click="addAttribute">Add Attribute</VBtn>
      </VCol>

      <!-- Buttons for Submit and Reset -->
      <VCol cols="12" class="d-flex gap-4">
        <VBtn type="submit" :disabled="!formValid">Add Product</VBtn>
        <VBtn type="reset" @click="resetForm">Reset</VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();
const name = ref('');
const sku = ref('');
const is_featured = ref(false);
const price = ref('');
const discount_price = ref('');
const stock = ref('');
const category_id = ref(null);
const categories = ref([]);
const additional_attributes = ref([]);
const newOption = ref('');
const formValid = ref(false);
const form = ref(null);

const multiValueAttributes = ['color', 'size'];
const dynamicOptions = ref({
  color: ["red", "blue", "green"],
  size: ["S", "M", "L"]
});

const rules = {
  required: value => !!value || 'Required.',
  minValue: min => value => (value >= min) || `Must be greater than or equal to ${min}.`,
};

const slug = computed(() => {
  return name.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

onMounted(async () => {
  try {
    const token = localStorage.getItem('authToken');
    const response = await axios.get('http://127.0.0.1:8000/api/categories', {
      headers: { Authorization: `Bearer ${token}` },
    });
    categories.value = response.data;
  } catch (error) {
    $toast.error('Error fetching categories: ' + (error.response?.data?.message || error.message));
  }
});

const loadSpecifications = () => {
  additional_attributes.value = [];

  if (category_id.value === 'electronics') {
    additional_attributes.value.push({ key: 'Processor', value: '' });
    additional_attributes.value.push({ key: 'RAM', value: '' });
  } else if (category_id.value === 'clothing') {
    additional_attributes.value.push({ key: 'Size', value: [] });
    additional_attributes.value.push({ key: 'Color', value: [] });
  }
};

const addOption = (attributeKey, newOption) => {
  if (newOption && !dynamicOptions.value[attributeKey].includes(newOption)) {
    dynamicOptions.value[attributeKey].push(newOption);
  }
};

const removeOption = (attributeKey, option) => {
  const index = dynamicOptions.value[attributeKey].indexOf(option);
  if (index > -1) {
    dynamicOptions.value[attributeKey].splice(index, 1);
  }
};

const getAttributeOptions = (attributeKey) => {
  return dynamicOptions.value[attributeKey] || [];
};

const addAttribute = () => {
  additional_attributes.value.push({ key: '', value: '' });
};

const removeAttribute = (index) => {
  additional_attributes.value.splice(index, 1);
};

const handleSubmit = async () => {
  const valid = await form.value.validate();
  if (!valid) {
    return;
  }

  const token = localStorage.getItem('authToken');
  try {
    const formData = {
      name: name.value,
      sku: sku.value,
      slug: slug.value,
      is_featured: is_featured.value ? '1' : '0',
      price: price.value,
      discount_price: discount_price.value || null,
      stock: stock.value,
      category_id: category_id.value,
      additional_attributes: Object.fromEntries(
        additional_attributes.value.map(attr => [attr.key, attr.value])
      )
    };

    await axios.post('http://127.0.0.1:8000/api/products-mongo', formData, {
      headers: { Authorization: `Bearer ${token}` },
    });

    $toast.success('Product added successfully!');
    resetForm();
  } catch (error) {
    $toast.error('Error adding product: ' + (error.response?.data?.message || error.message));
  }
};

const resetForm = () => {
  name.value = '';
  sku.value = '';
  slug.value = '';
  is_featured.value = false;
  price.value = '';
  discount_price.value = '';
  stock.value = '';
  category_id.value = null;
  additional_attributes.value = [];
  form.value.resetValidation();
};
</script>
