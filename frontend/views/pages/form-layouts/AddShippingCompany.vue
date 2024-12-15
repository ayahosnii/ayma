<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <!-- Shipping Company Name -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="name"
          label="Shipping Company Name"
          placeholder="Enter shipping company name"
          required
        />
      </VCol>

      <!-- Shipping Company Code -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="code"
          label="Shipping Company Code"
          placeholder="Enter shipping company code"
          required
        />
      </VCol>

      <!-- Contact Phone -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="phone"
          label="Contact Phone"
          placeholder="Enter phone number"
        />
      </VCol>

      <!-- Contact Email -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="email"
          label="Contact Email"
          type="email"
          placeholder="Enter email"
        />
      </VCol>

      <!-- Website -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="website"
          label="Website"
          placeholder="Enter website URL"
        />
      </VCol>

      <!-- Address Line 1 -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="address_line_1"
          label="Address Line 1"
          placeholder="Enter address line 1"
        />
      </VCol>

      <!-- Address Line 2 -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="address_line_2"
          label="Address Line 2"
          placeholder="Enter address line 2 (optional)"
        />
      </VCol>

      <!-- City -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="city"
          label="City"
          placeholder="Enter city"
        />
      </VCol>

      <!-- State -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="state"
          label="State"
          placeholder="Enter state"
        />
      </VCol>

      <!-- Zip Code -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="zip_code"
          label="Zip Code"
          placeholder="Enter zip code"
        />
      </VCol>

      <!-- Country -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="country"
          label="Country"
          placeholder="Enter country"
        />
      </VCol>

      <!-- Description/Notes -->
      <VCol cols="12">
        <VTextarea
          v-model="description"
          label="Description"
          placeholder="Enter additional notes or description"
        />
      </VCol>

      <!-- Submit Button -->
      <VCol cols="12">
        <VBtn type="submit" color="primary">Submit</VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useToast } from 'vue-toast-notification';

// Toast notification
const $toast = useToast();

// Form fields for shipping company
const name = ref('');
const code = ref('');
const phone = ref('');
const email = ref('');
const website = ref('');
const address_line_1 = ref('');
const address_line_2 = ref('');
const city = ref('');
const state = ref('');
const zip_code = ref('');
const country = ref('');
const description = ref('');

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');

  try {
    // Prepare shipping company data
    const shippingCompanyData = {
      name: name.value,
      code: code.value,
      phone: phone.value,
      email: email.value,
      website: website.value,
      address_line_1: address_line_1.value,
      address_line_2: address_line_2.value,
      city: city.value,
      state: state.value,
      zip_code: zip_code.value,
      country: country.value,
      description: description.value,
    };

    // Submit the shipping company data
    await axios.post('http://127.0.0.1:8000/api/shipping-companies', shippingCompanyData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    $toast.success('Shipping company added successfully!');
    resetForm();
  } catch (error) {
    $toast.error('Error adding shipping company: ' + (error.response?.data?.message || error.message));
    console.error('Error adding shipping company:', error);
  }
};

// Function to reset the form
const resetForm = () => {
  name.value = '';
  code.value = '';
  phone.value = '';
  email.value = '';
  website.value = '';
  address_line_1.value = '';
  address_line_2.value = '';
  city.value = '';
  state.value = '';
  zip_code.value = '';
  country.value = '';
  description.value = '';
};
</script>
