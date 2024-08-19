<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { BASE_URL } from '@/config/apiConfig';
import Vue3Toastify, { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const name = ref('')
const description = ref('')
const router = useRouter()



const handleSubmit = async () => {
  try {
    const token = localStorage.getItem('authToken')

    const response = await axios.post(`${BASE_URL}/levels`, {
      name: name.value,
      description: description.value,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log('Level added successfully:', response.data)
    toast.success('Level added successfully!', options)

    // Optional: Navigate to another page or show success message
    // router.push('/some-page');
  } catch (error) {
    console.error('Error adding level:', error)
    toast.error('Error adding level. Please try again.', options)

  }
}
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <VCol cols="12">
        <VTextField
          v-model="name"
          label="Level Name"
          placeholder="Level 1"
        />
      </VCol>
      <VCol cols="12">
        <VTextField
          v-model="description"
          label="Level Description"
          placeholder="Level 1 is easy..."
        />
      </VCol>

      <VCol
        cols="12"
        class="d-flex gap-4"
      >
        <VBtn type="submit">
          Add
        </VBtn>

        <VBtn
          type="reset"
          color="secondary"
          variant="outlined"
        >
          Reset
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
  <Vue3Toastify />
</template>
