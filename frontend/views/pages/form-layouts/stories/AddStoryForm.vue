<script setup>
import { BASE_URL } from '@/config/apiConfig';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const title = ref('');
const levelId = ref(null); // Initialize levelId as null
const slides = ref([{ image: '', context: '' }]);
const sounds = ref([{ sound: '' }]);
const levels = ref([]); // Initialize levels as an empty array
const levelsName = ref([]); // Initialize levelsName as an empty array
const router = useRouter();

const fetchLevels = async () => {
  try {
    const token = localStorage.getItem('authToken');

    const response = await axios.get(`${BASE_URL}/getAllLevels`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log('Fetched levels:', response.data);
    levels.value = response.data;
    levelsName.value = levels.value.map(level => level.name);
  } catch (error) {
    console.error('Error fetching levels:', error);
  }
};

onMounted(() => {
  fetchLevels();
});

const addSlide = () => {
  slides.value.push({ image: '', context: '' });
};

const removeSlide = (index) => {
  slides.value.splice(index, 1);
};

const addSound = () => {
  sounds.value.push({ sound: '' });
};

const removeSound = (index) => {
  sounds.value.splice(index, 1);
};


const handleSubmit = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('level_id', levelId.value);



    slides.value.forEach((slide, index) => {
      formData.append(`slides[${index}][image]`, slide.image);
      formData.append(`slides[${index}][context]`, slide.context);
    });


    sounds.value.forEach((sound, index) => {
      formData.append(`sounds[${index}][sound]`, sound.sound);

    });

    const response = await axios.post(`${BASE_URL}/stories`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      },
    });

    console.log('Story added successfully:', response.data);
  } catch (error) {
    console.error('Error adding story:', error);
  }
};

const handleImageUpload = (event, index) => {
  const file = event.target.files[0];

  slides.value[index].image = file;
};

const handleSoundUpload = (event, index) => {
  const file = event.target.files[0];
  sounds.value[index].sound = file;
};

</script>

<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <VCol cols="6">
        <VTextField
          v-model="title"
          label="Story Title"
          placeholder="Enter story title"
          required
        />
      </VCol>

      <VCol cols="6">
        <VSelect
          v-model="levelId"
          :items="levels"
          item-text="name"
          item-value="id"
          label="Level"
          placeholder="Select a level"
          required
        />
      </VCol>

      <VCol cols="12">
        <h3 class="mb-1">Slides</h3>
        <div v-for="(slide, index) in slides" :key="index" class="mb-4">
          <VCol cols="12">
            <VFileInput
              label="Image"
              placeholder="Choose image file"
              accept="image/*"
              @change="handleImageUpload($event, index)"
              required
            />
          </VCol>
          <VCol cols="12">
            <VTextarea
              v-model="slide.context"
              label="Context"
              placeholder="Enter context"
              required
            />
          </VCol>
          <VBtn
            color="error"
            @click="removeSlide(index)"
            v-if="slides.length > 1"
          >
            Remove Slide
          </VBtn>
        </div>
        <VCol cols="12">
          <VBtn @click="addSlide">
            Add Another Slide
          </VBtn>
        </VCol>
      </VCol>

      <VCol cols="12">
        <h3 class="mb-1">Sounds</h3>
        <div v-for="(sound, index) in sounds" :key="index" class="mb-4">
          <VCol cols="12">
            <VFileInput
              label="Sound"
              placeholder="Choose sound file"
              accept="audio/*"
              @change="handleSoundUpload($event, index)"
              required
            />
          </VCol>
          <VBtn
            color="error"
            @click="removeSound(index)"
            v-if="sounds.length > 1"
          >
            Remove Sound
          </VBtn>
        </div>
        <VCol cols="12">
          <VBtn @click="addSound">
            Add Another Sound
          </VBtn>
        </VCol>
      </VCol>

      <VCol
        cols="12"
        class="d-flex gap-4"
      >
        <VBtn type="submit">
          Add Story
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
</template>
