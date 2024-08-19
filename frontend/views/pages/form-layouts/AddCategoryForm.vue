<script setup>
import axios from 'axios';
import { ref } from 'vue';

import { useToast } from 'vue-toast-notification'

const $toast = useToast()

const name = ref('')
const router = useRouter()

const handleSubmit = async () => {
  const token = localStorage.getItem('authToken')

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/categories', {
      name: name.value,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    $toast.success('Category added successfully!')
    console.log('Category added successfully:', response.data)
  } catch (error) {
    $toast.error('Error adding category: ' + (error.response?.data?.message || error.message))
    console.error('Error adding category:', error)
  }
}
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <VCol cols="12">
        <VTextField
          v-model="name"
          label="Name"
          placeholder="Category"
        />
      </VCol>

      <VCol
        cols="12"
        class="d-flex gap-4"
      >
        <VBtn type="submit">
          Add
        </VBtn>

      </VCol>
    </VRow>
  </VForm>
</template>
