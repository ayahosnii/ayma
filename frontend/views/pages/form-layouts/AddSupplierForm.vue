<template>
  <VForm @submit.prevent="handleSubmit">
    <VRow>
      <!-- Supplier Name -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="name"
          label="Supplier Name"
          placeholder="Enter supplier name"
          required
        />
      </VCol>

      <!-- Contact Person -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="contact_person"
          label="Contact Person"
          placeholder="Enter contact person's name"
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

      <!-- Contact Phone -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="phone"
          label="Contact Phone"
          placeholder="Enter phone number"
        />
      </VCol>

      <!-- Address -->
      <VCol cols="12" md="6">
        <VTextField
          v-model="address"
          label="Address"
          placeholder="Enter address"
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

      <!-- Postal Code -->
      <VCol cols="12" md="4">
        <VTextField
          v-model="postal_code"
          label="Postal Code"
          placeholder="Enter postal code"
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

      <!-- Notes -->
      <VCol cols="12">
        <VTextarea
          v-model="additional_notes"
          label="Notes"
          placeholder="Enter additional notes"
        />
      </VCol>
      <VCol cols="12">
        <VSelect
          v-model="supplier_type"
          :items="['local', 'international', 'wholesale', 'retail', 'factory']"
          label="Supplier Type"
          required
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

// Form fields for supplier
const name = ref('');
const contact_person = ref('');
const email = ref('');
const phone = ref('');
const address = ref('');
const city = ref('');
const state = ref('');
const postal_code = ref('');
const country = ref('');
const additional_notes = ref('');
const supplier_type = ref('');

// Handle form submission
const handleSubmit = async () => {
  const token = localStorage.getItem('authToken');

  try {
    // Prepare supplier data
    const supplierData = {
      name: name.value,
      contact_person: contact_person.value,
      email: email.value,
      phone: phone.value,
      address: address.value,
      city: city.value,
      state: state.value,
      postal_code: postal_code.value,
      country: country.value,
      notes: additional_notes.value,
      supplier_type: supplier_type.value,
    };

    // Submit the supplier data
    await axios.post('http://127.0.0.1:8000/api/suppliers', supplierData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    $toast.success('Supplier added successfully!');
    resetForm();
  } catch (error) {
    $toast.error('Error adding supplier: ' + (error.response?.data?.message || error.message));
    console.error('Error adding supplier:', error);
  }
};

// Function to reset the form
const resetForm = () => {
  name.value = '';
  contact_person.value = '';
  email.value = '';
  phone.value = '';
  address.value = '';
  city.value = '';
  state.value = '';
  postal_code.value = '';
  supplier_type.value = '';
  country.value = '';
  additional_notes.value = '';
};
</script>
