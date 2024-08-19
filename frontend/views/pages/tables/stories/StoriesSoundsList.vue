<template>
  <div>
      <VBtn class="mb-4 ml-4" size="small" title="Add" type="Add" color="secondary" @click="openAddModal"><i class="ri-add-circle-line"></i> Add Sound</VBtn>
    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase text-center">Sound</th>
          <th class="text-uppercase text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="sound in sounds" :key="sound.id">
          <td class="text-center">
            <audio :ref="'audio-' + sound.id" :src="getSoundSrc(sound.sound)" controls class="mt-2"></audio>
          </td>
          <td class="text-center">
            <VBtn size="small" title="delete" type="delete" color="error" @click="openDeleteModal(sound)">
              <i class="ri-delete-bin-line"></i>
            </VBtn>
          </td>
        </tr>
      </tbody>
    </VTable>
    <VPagination v-model="currentPage" :length="totalPages" @page-change="onPageChange" />
    <VDialog v-model="deleteSoundModal" max-width="500px">
      <VCard>
        <VCardTitle>Delete Sound</VCardTitle>
        <VCardText>Are you sure you want to delete this sound?</VCardText>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeDeleteModal">Cancel</VBtn>
            <VBtn color="success" text @click="deleteSound">Delete</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
    
    <VDialog v-model="addSoundModal" max-width="500px">
      <VCard>
        <VCardTitle>Add Sound</VCardTitle>
        <VCardText>
          <input type="file" @change="onFileChange" accept="audio/mpeg, audio/mp3, audio/wav, audio/aac" />
        </VCardText>
        <VCardActions>
          <VCol cols="12">
            <VBtn color="error" text @click="closeAddModal">Cancel</VBtn>
            <VBtn color="success" text @click="addSound">Add</VBtn>
          </VCol>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script>
import { BASE_URL, PUBLIC_URL } from '@/config/apiConfig';
import axios from 'axios';

export default {
  name: 'StoriesSoundsList',
  data() {
    return {
      sounds: [],
      currentPage: 1,
      totalPages: 1,
      deleteSoundModal: false,
      addSoundModal: false,
      soundToDelete: null,
      newSoundFile: null,
    };
  },
  computed: {
    id() {
      return this.$route.params.id;
    }
  },
  methods: {
    async fetchStorySounds() {
      try {
        const token = localStorage.getItem('authToken');
        const response = await axios.get(`${BASE_URL}/stories/${this.id}/sounds`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.sounds = response.data;
        this.totalPages = response.data.totalPages || 1;
      } catch (error) {
        console.error('Error fetching story sounds:', error);
      }
    },
    getSoundSrc(file) {
      return `${PUBLIC_URL}/storage/${file}`;
    },
    onPageChange(page) {
      this.currentPage = page;
      this.fetchStorySounds();
    },
    openDeleteModal(sound) {
      this.soundToDelete = sound;
      this.deleteSoundModal = true;
    },
    closeDeleteModal() {
      this.deleteSoundModal = false;
      this.soundToDelete = null;
    },
    async deleteSound() {
      if (!this.soundToDelete) return;
      try {
        const token = localStorage.getItem('authToken');
        await axios.delete(`${BASE_URL}/stories/${this.id}/sounds/${this.soundToDelete.id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.sounds = this.sounds.filter(sound => sound.id !== this.soundToDelete.id);
        this.closeDeleteModal();
      } catch (error) {
        console.error('Error deleting sound:', error);
      }
    },
    openAddModal() {
      this.addSoundModal = true;
    },
    closeAddModal() {
      this.addSoundModal = false;
      this.newSoundFile = null;
    },
    onFileChange(event) {
      this.newSoundFile = event.target.files[0];
      console.log('File selected:', this.newSoundFile);
    },
    async addSound() {
      if (!this.newSoundFile) return;

      // Log the file type and details
      console.log('File Type:', this.newSoundFile.type);
      console.log('File Details:', this.newSoundFile);

      const formData = new FormData();
      formData.append('sound', this.newSoundFile);

      try {
        const token = localStorage.getItem('authToken');
        const response = await axios.post(`${BASE_URL}/stories/${this.id}/sounds`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data',
          },
        });
        this.sounds.push(response.data.sound);
        this.closeAddModal();
      } catch (error) {
        console.error('Error adding sound:', error);
        if (error.response && error.response.data) {
          console.error('Error details:', error.response.data);
        }
      }
    },
  },
  mounted() {
    this.fetchStorySounds();
  }
};
</script>

<style scoped>
.text-uppercase {
  text-transform: uppercase;
}

</style>
