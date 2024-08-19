<template>
  <v-container>
    <v-card>
      <v-card-title class="headline">Add Grammar Question</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="addQuestion">
          <v-row>
            <v-col cols="12">
              <v-select
                v-model="gameId"
                :items="gameOptions"
                item-text="title"
                item-value="value"
                label="Game Title"
                required
              >
              </v-select>
            </v-col>
            <v-col cols="12">
              <v-text-field v-model="questionText" label="Question Text" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText">
              <v-text-field v-model="options[0]" label="Option 1" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0]">
              <v-text-field v-model="options[1]" label="Option 2" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0] && options[1]">
              <v-text-field v-model="options[2]" label="Option 3" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0] && options[1] && options[2]">
              <v-select v-model="correctOption" :items="options" label="Correct Option" required></v-select>
            </v-col>
            <v-col cols="12" class="d-flex justify-end">
              <v-btn color="primary" type="submit" :disabled="!formCompleted">Submit</v-btn>
              <v-btn type="reset" color="secondary" @click="resetForm">Reset</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { BASE_URL } from '@/config/apiConfig';

const gameId = ref('');
const gameOptions = ref([]);
const questionText = ref('');
const options = ref(['', '', '']);
const correctOption = ref('');
const router = useRouter();

// Check if all required fields are filled
const formCompleted = computed(() => {
  return questionText.value && options.value.every(option => option !== '');
});

const addQuestion = async () => {
  try {
    const token = localStorage.getItem('authToken');

    const response = await axios.post(`${BASE_URL}/grammar-questions`, {
      text: questionText.value,
      gameId: gameId.value,
      answer_options: options.value.map((option, index) => ({
        option,
        is_correct: option.toString() === correctOption.value ? 1 : 0
      }))
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(gameId);
    console.log(response.data);
    alert('Question added successfully!');
    // Clear form fields after successful submission
    resetForm();
  } catch (error) {
    console.error(error);
    alert('Failed to add question. Please try again.');
  }
};
const getGrammarGames = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/get-grammar-games`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });

      if (response.data) {
        gameOptions.value = response.data.map(game => ({
          title: game.title,
          value: game.id
        }));
      } else {
        console.error('Invalid response format:', response);
        alert('Failed to load grammar games. Please try again later.');
      }
    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch levels:', error);
    alert('Failed to load grammar games. Please try again.');
  }
};

const resetForm = () => {
  questionText.value = '';
  options.value = ['', '', ''];
  correctOption.value = '';
};

// Fetch grammar games when the component mounts
onMounted(() => {
  getGrammarGames();
});
</script>

<style scoped>
/* Add scoped styles if needed */
</style>
