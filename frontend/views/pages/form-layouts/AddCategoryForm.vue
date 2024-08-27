<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';

// Toast notification
const $toast = useToast();

// Form fields
const name = ref('');
const description = ref('');
const image = ref(null);
const parent_id = ref(); // Initially null
const categories2 = ref([]);
const order = ref(0);
const is_active = ref(1);

// Router instance
const router = useRouter();

// Generate slug automatically based on name
const slug = computed(() => {
  return name.value
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric characters with hyphens
    .replace(/(^-|-$)/g, ''); // Remove leading and trailing hyphens
});

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  image.value = file;
};

// Fetch categories when the component mounts
onMounted(async () => {
  const token = localStorage.getItem('authToken');

  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    categories2.value = response.data;
    console.log(categories2.value)
  } catch (error) {
    $toast.error('Error fetching categories: ' + (error.response?.data?.message || error.message));
    console.error('Error fetching categories:', error);
  }
});

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');

  try {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('slug', slug.value || '');
    formData.append('description', description.value || '');
    if (image.value) {
      formData.append('image', image.value);
    }
    formData.append('parent_id', parent_id.value);
    formData.append('order', order.value);
    formData.append('is_active', is_active.value);

    const response = await axios.post('http://127.0.0.1:8000/api/categories', formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data', // Important for file uploads
      },
    });

    $toast.success('Category added successfully!');
    console.log('Category added successfully:', response.data);

    // Optionally, reset the form or redirect
    resetForm();
    // router.push('/categories');
  } catch (error) {
    $toast.error('Error adding category: ' + (error.response?.data?.message || error.message));
    console.error('Error adding category:', error);
  }
};

// Optional: Function to reset the form
const resetForm = () => {
  name.value = '';
  description.value = '';
  image.value = null;
  parent_id.value = null;
  order.value = 0;
  is_active.value = 1;
};
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <VCol cols="12">
        <VTextField
          v-model="name"
          label="Name"
          placeholder="Category Name"
          required
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="slug"
          :value="slug"
          label="Slug"
          placeholder="Category Slug"
          readonly
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="description"
          label="Description"
          placeholder="Category Description (optional)"
        />
      </VCol>

      <VCol cols="12">
        <VFileInput
          v-model="image"
          label="Image"
          placeholder="Select an image"
          accept="image/*"
          @change="handleImageUpload"
        />
      </VCol>

      <VCol cols="12">
        <VSelect
          v-model="parent_id"
          label="Choose Category"
          :items="categories2"
          item-title="name"
          item-value="id"
          density="compact"
          class="me-3"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="order"
          label="Order"
          placeholder="Display Order"
          type="number"
          min="0"
        />
      </VCol>

      <VCol cols="12">
        <VCheckbox
          v-model="is_active"
          label="Is Active?"
          :true-value="1"
          :false-value="0"
        />
      </VCol>

      <VCol cols="12" class="d-flex gap-4">
        <VBtn type="submit">
          Add Category
        </VBtn>
        <VBtn type="reset" @click="resetForm">
          Reset
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
