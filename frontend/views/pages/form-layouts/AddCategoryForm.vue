<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const name = ref('');
const slug = ref('');
const description = ref('');
const image = ref(null);
const parent_id = ref(null);
const order = ref(0);
const is_active = ref(true);

const router = useRouter();

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
    // resetForm();
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
  is_active.value = true;
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
          placeholder="Category Slug (optional)"
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
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="parent_id"
          label="Parent Category ID"
          placeholder="Parent Category ID (optional)"
          type="number"
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
          :true-value="true"
          :false-value="false"
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
