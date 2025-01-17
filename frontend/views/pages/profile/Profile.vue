<script setup>
import { BASE_URL } from '@/config/apiConfig';
import avatar1 from '@images/avatars/avatar-1.png';
import axios from 'axios';
import { onMounted, ref } from 'vue';

//const accountDataLocal = ref(null)
const fetchProfileData = async () => {
  const token = localStorage.getItem('authToken');

  try {
    const response = await axios.get(`${BASE_URL}/profile`, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    });

    accountDataLocal.value = response.data
    console.log(accountDataLocal.value)
  } catch (error) {
    console.error('Failed to fetch profile data:', error)
  }
}
const accountData = {
  avatarImg: avatar1,
  firstName: 'john',
  lastName: 'Doe',
  email: 'johnDoe@example.com',
  org: 'ThemeSelection',
  phone: '+1 (917) 543-9876',
  address: '123 Main St, New York, NY 10001',
  state: 'New York',
  zip: '10001',
  country: 'USA',
  language: 'English',
  timezone: '(GMT-11:00) International Date Line West',
  currency: 'USD',
}

const refInputEl = ref()
const accountDataLocal = ref(structuredClone(accountData))
const isAccountDeactivated = ref(false)

const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountDataLocal.value.avatarImg = fileReader.result
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  accountDataLocal.value.avatarImg = accountData.avatarImg
}

const timezones = [
  '(GMT-11:00) International Date Line West',
  '(GMT-11:00) Midway Island',
  '(GMT-10:00) Hawaii',
  '(GMT-09:00) Alaska',
  '(GMT-08:00) Pacific Time (US & Canada)',
  '(GMT-08:00) Tijuana',
  '(GMT-07:00) Arizona',
  '(GMT-07:00) Chihuahua',
  '(GMT-07:00) La Paz',
  '(GMT-07:00) Mazatlan',
  '(GMT-07:00) Mountain Time (US & Canada)',
  '(GMT-06:00) Central America',
  '(GMT-06:00) Central Time (US & Canada)',
  '(GMT-06:00) Guadalajara',
  '(GMT-06:00) Mexico City',
  '(GMT-06:00) Monterrey',
  '(GMT-06:00) Saskatchewan',
  '(GMT-05:00) Bogota',
  '(GMT-05:00) Eastern Time (US & Canada)',
  '(GMT-05:00) Indiana (East)',
  '(GMT-05:00) Lima',
  '(GMT-05:00) Quito',
  '(GMT-04:00) Atlantic Time (Canada)',
  '(GMT-04:00) Caracas',
  '(GMT-04:00) La Paz',
  '(GMT-04:00) Santiago',
  '(GMT-03:30) Newfoundland',
  '(GMT-03:00) Brasilia',
  '(GMT-03:00) Buenos Aires',
  '(GMT-03:00) Georgetown',
  '(GMT-03:00) Greenland',
  '(GMT-02:00) Mid-Atlantic',
  '(GMT-01:00) Azores',
  '(GMT-01:00) Cape Verde Is.',
  '(GMT+00:00) Casablanca',
  '(GMT+00:00) Dublin',
  '(GMT+00:00) Edinburgh',
  '(GMT+00:00) Lisbon',
  '(GMT+00:00) London',
]

const currencies = [
  'USD',
  'EUR',
  'GBP',
  'AUD',
  'BRL',
  'CAD',
  'CNY',
  'CZK',
  'DKK',
  'HKD',
  'HUF',
  'INR',
]

const editDialog = ref(false);

const saveProfile = () => {
  const profileId = accountDataLocal.value.id; // Ensure this is part of your profile data
  console.log(accountDataLocal.value)
  updateProfile(profileId);
};

const updateProfile = async (profileId) => {
  const token = localStorage.getItem('authToken');

  try {
    const response = await axios.put(`${BASE_URL}/profile/${profileId}`, accountDataLocal.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log('Profile updated successfully:', response.data);
    editDialog.value = false; // Close the dialog after successful update
  } catch (error) {
    console.error('Failed to update profile:', error);
  }
};


onMounted(() => {
  fetchProfileData() // Fetch profile data when component mounts
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Account Details">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded="lg"
            size="100"
            class="me-6"
            :image="accountDataLocal.avatarImg"
          />


        </VCardText>

          <!-- Edit Button -->
      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="editDialog = true">Edit Profile</VBtn>
      </VCardActions>


        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                md="12"
                cols="12"
              >
                <p>
                  <strong>Name:</strong>
                  {{accountDataLocal.name}}
                </p>
              </VCol>



              <!-- 👉 Email -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Email:</strong>
                  {{accountDataLocal.email}}
                </p>
              </VCol>

              <!-- 👉 Organization -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Organization:</strong>
                  {{accountDataLocal.org}}
                </p>
              </VCol>

              <!-- 👉 Phone -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Phone:</strong>
                  {{accountDataLocal.phone}}
                </p>
              </VCol>

              <!-- 👉 Address -->
              <VCol
                cols="12"
                md="6"
              >

                <p>
                  <strong>Address:</strong>
                  {{accountDataLocal.address}}
                </p>
              </VCol>

              <!-- 👉 State -->
              <VCol
                cols="12"
                md="6"
              >

                <p>
                  <strong>State:</strong>
                  {{accountDataLocal.state}}
                </p>
              </VCol>

              <!-- 👉 Zip Code -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Zip Code:</strong>
                  {{accountDataLocal.zip}}
                </p>
              </VCol>

              <!-- 👉 Country -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Country:</strong>
                  {{accountDataLocal.country}}
                </p>

              </VCol>

              <!-- 👉 Language -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Language:</strong>
                  {{accountDataLocal.language}}
                </p>
              </VCol>

              <!-- 👉 Timezone -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Timezone:</strong>
                  {{accountDataLocal.timezone}}
                </p>
              </VCol>

              <!-- 👉 Currency -->
              <VCol
                cols="12"
                md="6"
              >
                <p>
                  <strong>Currency:</strong>
                  {{accountDataLocal.currency}}
                </p>
              </VCol>


            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <VDialog v-model="editDialog" max-width="600">
    <VCard>
      <VCardTitle>Edit Profile</VCardTitle>

      <VCardText>
        <VForm ref="editForm" @submit.prevent="saveProfile">
          <VRow>
            <!-- Name and Last Name -->
            <VCol cols="12" md="6">
              <VTextField
                label="First Name"
                v-model="accountDataLocal.name"
                outlined
                dense
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                label="Last Name"
                v-model="accountDataLocal.lastName"
                outlined
                dense
              />
            </VCol>

            <!-- Email and Organization -->
            <VCol cols="12" md="6">
              <VTextField
                label="Email"
                v-model="accountDataLocal.email"
                outlined
                dense
                required
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                label="Organization"
                v-model="accountDataLocal.org"
                outlined
                dense
              />
            </VCol>

            <!-- Phone and Address -->
            <VCol cols="12" md="6">
              <VTextField
                label="Phone"
                v-model="accountDataLocal.phone"
                outlined
                dense
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                label="Address"
                v-model="accountDataLocal.address"
                outlined
                dense
              />
            </VCol>

            <!-- State and Zip Code -->
            <VCol cols="12" md="6">
              <VTextField
                label="State"
                v-model="accountDataLocal.state"
                outlined
                dense
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                label="Zip Code"
                v-model="accountDataLocal.zip"
                outlined
                dense
              />
            </VCol>

            <!-- Country and Language -->
            <VCol cols="12" md="6">
              <VTextField
                label="Country"
                v-model="accountDataLocal.country"
                outlined
                dense
              />
            </VCol>

            <VCol cols="12" md="6">
              <VTextField
                label="Language"
                v-model="accountDataLocal.language"
                outlined
                dense
              />
            </VCol>

            <!-- Timezone and Currency -->
            <VCol cols="12" md="6">
              <VSelect
                :items="timezones"
                label="Timezone"
                v-model="accountDataLocal.timezone"
                outlined
                dense
              />
            </VCol>

            <VCol cols="12" md="6">
              <VSelect
                :items="currencies"
                label="Currency"
                v-model="accountDataLocal.currency"
                outlined
                dense
              />
            </VCol>
          </VRow>
        </VForm>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn text @click="editDialog = false">Cancel</VBtn>
        <VBtn color="primary" @click="saveProfile">Save</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

</template>
