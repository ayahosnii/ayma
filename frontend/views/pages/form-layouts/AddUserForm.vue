<script setup>
import { BASE_URL } from '@/config/apiConfig';
import avatar1 from '@images/avatars/avatar-1.png';
import axios from 'axios';
import countriesAndTimezones from 'countries-and-timezones'; // Import the package
import { Country, State } from 'country-state-city';
import currencyCodes from 'currency-codes';

import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const accountData = {
  avatarImg: avatar1,
  name: '',
  email: '',
  password: '',
  phone: '',
  addressLineOne: '',
  addressLineTwo: '',
  state: '',
  zip: '',
  country: 'USA',
  language: '',
  timezone: '',
  currency: 'USD',
  status: 'active',
  role: 'Customer', // Default role
  employerData: {
    hire_date: '',
    job_title: '',
    department: '',
    salary: '',
  },
  CustomerData: {
  }
}

const refInputEl = ref()
const accountDataLocal = ref(structuredClone(accountData))
const isAccountDeactivated = ref(false)
const currencies = ref([])
const countries = ref([])
const states = ref([])
const selectedCountryCode = ref('')
const phoneCode = ref('')
const languages = ref([
  'English', 'Spanish', 'French', 'German', 'Chinese', 'Hindi', 'Arabic', 'Portuguese', 'Russian', 'Japanese',
  'Italian', 'Korean', 'Dutch', 'Turkish', 'Vietnamese', 'Polish', 'Thai', 'Swedish', 'Greek', 'Czech',
  'Romanian', 'Hungarian', 'Danish', 'Finnish', 'Norwegian', 'Hebrew', 'Indonesian', 'Malay', 'Swahili', 'Bengali'
])

const loadCountries = () => {
  countries.value = Country.getAllCountries().map(country => ({
    name: country.name,
    code: country.isoCode,
    phoneCode: country.phonecode,
  }))
}

// const loadLanguages = () => {
//   const list = new LanguagesList();
//   languages.value = Object.values(list.getAll());
//   console.log(languages.value);  // Check if the languages are loaded correctly
// };

const updateStates = (countryCode) => {
  states.value = State.getStatesOfCountry(countryCode).map(state => ({
    name: state.name,
    code: state.isoCode,
  }))
}

const updatePhoneCode = (countryCode) => {
  const selectedCountry = countries.value.find(country => country.code === countryCode);
  phoneCode.value = selectedCountry ? `+${selectedCountry.phoneCode}` : '';
};

watch(() => accountDataLocal.value.country, (newCountry) => {
  // Update country code based on country selection
  selectedCountryCode.value = countries.value.find(c => c.name === newCountry)?.code || '';
  updateStates(selectedCountryCode.value);
  updatePhoneCode(selectedCountryCode.value);  // This is the key part to ensure phone code is updated
});

const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
        accountDataLocal.value.avatarImg = fileReader.result
      }
    }
  }
}

const resetAvatar = () => {
  accountDataLocal.value.avatarImg = accountData.avatarImg
}

// Generate the list of time zones dynamically
const timezones = Object.values(countriesAndTimezones.getAllTimezones()).map(tz => `${tz.name} (GMT${tz.utcOffsetStr})`)


// Fetch currencies dynamically using `currency-codes` package
const loadCurrencies = () => {
  currencies.value = currencyCodes.data.map(
    currency => `${currency.code} - ${currency.currency}`
  )
}

const saveAccountDetails = async () => {
  try {
    const formData = new FormData()
    formData.append('name', accountDataLocal.value.name)
    formData.append('email', accountDataLocal.value.email)
    formData.append('password', accountDataLocal.value.password)
    formData.append('phone_number', accountDataLocal.value.phone)
    formData.append('status', accountDataLocal.value.status)
    formData.append('timezone', accountDataLocal.value.timezone)
    formData.append('language', accountDataLocal.value.language)
    formData.append('address_line1', accountDataLocal.value.addressLineOne)
    formData.append('address_line2', accountDataLocal.value.addressLineTwo)
    formData.append('state', accountDataLocal.value.state)
    formData.append('postal_code', accountDataLocal.value.zip)
    formData.append('country', accountDataLocal.value.country)
    formData.append('default_currency', accountDataLocal.value.currency)
    formData.append('role', accountDataLocal.value.role)

    // Add role-specific fields
    if (accountDataLocal.value.role === 'employer') {
      formData.append('hire_date', accountDataLocal.value.employerData.hire_date)
      formData.append('job_title', accountDataLocal.value.employerData.job_title)
      formData.append('department', accountDataLocal.value.employerData.department)
      formData.append('salary', accountDataLocal.value.employerData.salary)
    }

    const token = localStorage.getItem('authToken')

    const response = await axios.post(`${BASE_URL}/users`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${token}`,
      },
    })

    if (response.status === 201) {
      toast.success('Account details saved successfully!')
      resetForm()
    } else {
      toast.error(response.data.message || 'Failed to save account details')
    }
  } catch (error) {
    console.error('Error saving account details:', error)

    // Check if the error is from the server
    if (error.response) {
      // Specific error from the server
      const errorMessage = error.response.data.message || 'An unexpected error occurred'
      toast.error(errorMessage)
    } else if (error.request) {
      // Network error or no response from the server
      toast.error('Network error: Unable to reach the server')
    } else {
      // Any other errors
      toast.error('An unexpected error occurred')
    }
  }
}

onMounted(() => {
  // loadLanguages()
  loadCurrencies()
  loadCountries()
  updatePhoneCode(accountDataLocal.value.country); 
  
  
})

</script>

<template>
  <v-form @submit.prevent="saveAccountDetails" class="pa-4">
    <v-card>
      <v-card-title>
        <v-avatar size="80" class="mr-4">
          <img :src="accountDataLocal.avatarImg" alt="User Avatar" />
        </v-avatar>
        <div>
          <v-btn @click="refInputEl?.click()" color="primary" class="mr-2">Upload Avatar</v-btn>
          <input ref="refInputEl" type="file" hidden @input="changeAvatar" />
        </div>
      </v-card-title>
      <v-card-subtitle class="pt-0">Fill in your details below:</v-card-subtitle>

      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field label="Full Name" v-model="accountDataLocal.name" placeholder="John Doe" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Email" v-model="accountDataLocal.email" placeholder="johndoe@gmail.com" type="email" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Password" v-model="accountDataLocal.password" placeholder="Enter Password" type="password" />
          </v-col>
          <v-col cols="12" md="6">
            <v-select 
              label="Country" 
              v-model="accountDataLocal.country" 
              :items="countries.map(c => c.name)" 
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Address Line One" v-model="accountDataLocal.addressLineOne" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Address Line Two" v-model="accountDataLocal.addressLineTwo" />
          </v-col>
          <v-col cols="12" md="6">
            <v-select 
              label="State" 
              v-model="accountDataLocal.state" 
              :items="states.map(s => s.name)" 
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Zip" v-model="accountDataLocal.zip" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field 
              label="Phone Number" 
              v-model="accountDataLocal.phone" 
              :prepend-inner="phoneCode" 
              placeholder="Enter phone number" 
              type="tel" 
              @input="validatePhoneInput"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-select label="Language" v-model="accountDataLocal.language" :items="languages" />
          </v-col>
          <v-col cols="12" md="6">
            <v-select label="Timezone" v-model="accountDataLocal.timezone" :items="timezones" placeholder="Asia/Beirut (GMT+02:00)"/>
          </v-col>
          <v-col cols="12" md="6">
            <v-select label="Currency" v-model="accountDataLocal.currency" :items="currencies" placeholder="USD - US Dollar"/>
          </v-col>
          <v-col cols="12" md="6">
            <v-select label="Role" v-model="accountDataLocal.role" :items="['Customer', 'employer']" />
          </v-col>
        </v-row>

        <!-- Conditional Employer Fields -->
        <v-row v-if="accountDataLocal.role === 'employer'">
          <v-col cols="12" md="6">
            <v-text-field label="Hire Date" v-model="accountDataLocal.employerData.hire_date" type="date" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Job Title" v-model="accountDataLocal.employerData.job_title" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Department" v-model="accountDataLocal.employerData.department" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field label="Salary" v-model="accountDataLocal.employerData.salary" type="number" />
          </v-col>
        </v-row>

      </v-card-text>

      <v-card-actions>
        <v-btn color="primary" type="submit">Save Changes</v-btn>
        <v-btn text @click="resetForm">Reset</v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>
