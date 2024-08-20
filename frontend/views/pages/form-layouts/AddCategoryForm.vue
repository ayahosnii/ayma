<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const name = ref('');
const slug = ref('');
const description = ref('');
const image = ref(null);
const parent_id = ref(null); // Initially null
const categories = ref([]); // Store categories here
const order = ref(0);
const is_active = ref(1);

const router = useRouter();

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
    categories.value = response.data.data; // Assuming the categories are returned in a `data` property
  } catch (error) {
    $toast.error('Error fetching categories: ' + (error.response?.data?.message || error.message));
    console.error('Error fetching categories:', error);
  }
});

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
    formData.append('parent_id', parent_id.value ? parent_id.value.id : null);
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
  slug.value = '';
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
          label="Slug"
          placeholder="Category Slug"
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
          :items="categories"
          item-text="name"
          item-value="id"
          label="Parent Category"
          placeholder="Select Parent Category (optional)"
          return-object
          :menu-props="{ maxHeight: '400px' }"
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
