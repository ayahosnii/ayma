<template>
  <v-container>
    <v-card>
      <v-card-title class="headline">Edit Grammar Question</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="editQuestion">
          <v-row>
            <v-col cols="12">
              <v-text-field v-model="gameTitle" label="Game Title" disabled></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field v-model="questionText" label="Question Text" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText">
              <v-text-field v-model="options[0].option" label="Option 1" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0].option">
              <v-text-field v-model="options[1].option" label="Option 2" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0].option && options[1].option">
              <v-text-field v-model="options[2].option" label="Option 3" required></v-text-field>
            </v-col>
            <v-col cols="12" v-if="questionText && options[0].option && options[1].option && options[2].option">
              <v-select
                v-model="correctOption"
                :items="options.map(option => option.option)"
                label="Correct Option"
                required
              ></v-select>
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
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { BASE_URL } from '@/config/apiConfig';

const gameTitle = ref('');
const gameId = ref('');
const gameOptions = ref([]);
const questionText = ref('');
const options = ref([{ option: '', is_correct: 0 }, { option: '', is_correct: 0 }, { option: '', is_correct: 0 }]);
const correctOption = ref('');
const router = useRouter();
const route = useRoute();
const id = route.params.id as string;

// Check if all required fields are filled
const formCompleted = computed(() => {
  return questionText.value && options.value.every(option => option.option !== '');
});

const editQuestion = async () => {
  try {
    const token = localStorage.getItem('authToken');

    const response = await axios.put(`${BASE_URL}/grammar-questions/${id}`, {
      text: questionText.value,
      gameId: gameId.value,
      answer_options: options.value.map(option => ({
        option: option.option,
        is_correct: option.option === correctOption.value ? 1 : 0
      }))
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    alert('Question updated successfully!');
    // Clear form fields after successful submission
    resetForm();
  } catch (error) {
    console.error(error);
    alert('Failed to update question. Please try again.');
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
    console.error('Failed to fetch grammar games:', error);
    alert('Failed to load grammar games. Please try again.');
  }
};

const getGrammarEdit = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await axios.get(`${BASE_URL}/grammar-questions/${id}/edit`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });

      if (response.data) {
        questionText.value = response.data.data.text;
        options.value = response.data.data.options;
        gameTitle.value = response.data.data.game.title;
        const correct = options.value.find(option => option.is_correct);
        correctOption.value = correct ? correct.option : '';
      } else {
        console.error('Invalid response format:', response);
        alert('Failed to load grammar question. Please try again later.');
      }

    } else {
      console.error('No auth token found');
    }
  } catch (error) {
    console.error('Failed to fetch grammar question:', error);
    alert('Failed to load grammar question. Please try again.');
  }
};

const resetForm = () => {
  questionText.value = '';
  options.value = [{ option: '', is_correct: 0 }, { option: '', is_correct: 0 }, { option: '', is_correct: 0 }];
  correctOption.value = '';
  gameTitle.value = '';
};

// Fetch grammar games and the question details when the component mounts
onMounted(() => {
  getGrammarGames();
  getGrammarEdit();
});
</script>

<style scoped>
/* Add scoped styles if needed */
</style>
