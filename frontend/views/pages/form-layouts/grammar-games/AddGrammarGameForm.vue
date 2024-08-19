<script setup>
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Vue3Toastify, { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const title = ref('');
const levelId = ref('');
const levels = ref([]);  // Initialize with an empty array
const router = useRouter();

// Fetch levels from backend
const fetchLevels = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (!token) {
      throw new Error('No auth token found');
    }

    const response = await axios.get(`${BASE_URL}/levels`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log('Fetched levels:', response.data);

    // Ensure response.data.data is an array and map to desired format
    if (response.data && Array.isArray(response.data.data)) {
      levels.value = response.data.data.map(level => ({
        id: level.id,
        name: level.name
      }));
    } else {
      console.error('Unexpected response structure:', response.data);
      toast.error('Unexpected response structure. Please try again.');
    }
  } catch (error) {
    console.error('Error fetching levels:', error);
    toast.error('Error fetching levels. Please try again.');
  }
};

const handleSubmit = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (!token) {
      throw new Error('No auth token found');
    }

    const response = await axios.post(`${BASE_URL}/grammar-games`, {
      title: title.value,
      level_id: levelId.value,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log('Grammar game added successfully:', response.data);
    toast.success('Grammar game added successfully!');

    // Optional: Navigate to another page or show success message
    // router.push('/some-page');
  } catch (error) {
    console.error('Error adding grammar game:', error);
    toast.error('Error adding grammar game. Please try again.');
  }
};

// Fetch levels on component mount
onMounted(fetchLevels);
</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <VCol cols="12">
        <VTextField
          v-model="title"
          label="Game Title"
          placeholder="Enter game title"
          required
        />
      </VCol>
      <VCol cols="12">
        <VSelect
          v-model="levelId"
          :items="levels"
          item-value="value"
          item-text="name"
          label="Select Level"
          placeholder="Choose a level"
          required
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

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}
</style>
