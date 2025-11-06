<template>
  <div>
    <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
      Create your account
    </h2>

    <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
      <!-- Error Message -->
      <div v-if="errorMessage" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
        <p class="text-sm text-red-800 dark:text-red-200">
          {{ errorMessage }}
        </p>
      </div>

      <!-- First Name -->
      <div>
        <label for="first_name" class="form-label">First Name</label>
        <input
          id="first_name"
          v-model="form.first_name"
          type="text"
          required
          class="form-input"
          :class="{ 'border-red-500': errors.first_name }"
        />
        <p v-if="errors.first_name" class="form-error">{{ errors.first_name[0] }}</p>
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="form-label">Last Name</label>
        <input
          id="last_name"
          v-model="form.last_name"
          type="text"
          required
          class="form-input"
          :class="{ 'border-red-500': errors.last_name }"
        />
        <p v-if="errors.last_name" class="form-error">{{ errors.last_name[0] }}</p>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="form-label">Email address</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          autocomplete="email"
          class="form-input"
          :class="{ 'border-red-500': errors.email }"
        />
        <p v-if="errors.email" class="form-error">{{ errors.email[0] }}</p>
      </div>

      <!-- Phone -->
      <div>
        <label for="phone" class="form-label">Phone (optional)</label>
        <input
          id="phone"
          v-model="form.phone"
          type="tel"
          class="form-input"
          :class="{ 'border-red-500': errors.phone }"
        />
        <p v-if="errors.phone" class="form-error">{{ errors.phone[0] }}</p>
      </div>

      <!-- Role -->
      <div>
        <label for="role" class="form-label">I am a</label>
        <select
          id="role"
          v-model="form.role"
          required
          class="form-input"
          :class="{ 'border-red-500': errors.role }"
        >
          <option value="">Select a role...</option>
          <option value="tenant">Tenant (Military Personnel)</option>
          <option value="landlord">Landlord (Property Owner)</option>
          <option value="vendor">Vendor (Service Provider)</option>
        </select>
        <p v-if="errors.role" class="form-error">{{ errors.role[0] }}</p>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="form-label">Password</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          autocomplete="new-password"
          class="form-input"
          :class="{ 'border-red-500': errors.password }"
        />
        <p v-if="errors.password" class="form-error">{{ errors.password[0] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Minimum 8 characters
        </p>
      </div>

      <!-- Password Confirmation -->
      <div>
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          required
          autocomplete="new-password"
          class="form-input"
          :class="{ 'border-red-500': errors.password_confirmation }"
        />
        <p v-if="errors.password_confirmation" class="form-error">{{ errors.password_confirmation[0] }}</p>
      </div>

      <!-- Locale -->
      <div>
        <label for="locale" class="form-label">Preferred Language</label>
        <select
          id="locale"
          v-model="form.locale"
          required
          class="form-input"
        >
          <option value="en">English</option>
          <option value="it">Italiano</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div>
        <button
          type="submit"
          :disabled="loading"
          class="btn btn-primary w-full"
        >
          <span v-if="loading">Creating account...</span>
          <span v-else">Create account</span>
        </button>
      </div>

      <!-- Login Link -->
      <div class="text-center text-sm">
        <span class="text-gray-600 dark:text-gray-400">Already have an account?</span>
        <NuxtLink to="/login" class="ml-1 font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
          Sign in
        </NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { RegisterData } from '~/types/auth'

definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const { register } = useAuth()
const { locale: currentLocale } = useI18n()

const form = ref<RegisterData>({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  locale: currentLocale.value as 'en' | 'it',
  role: '' as 'tenant' | 'landlord' | 'vendor',
})

const loading = ref(false)
const errorMessage = ref('')
const errors = ref<Record<string, string[]>>({})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  const result = await register(form.value)

  if (!result.success) {
    errorMessage.value = result.message
    errors.value = result.errors || {}
  }

  loading.value = false
}

// Clear errors on input
const clearError = (field: string) => {
  delete errors.value[field]
  errorMessage.value = ''
}

watch(() => form.value.email, () => clearError('email'))
watch(() => form.value.password, () => clearError('password'))
watch(() => form.value.password_confirmation, () => clearError('password_confirmation'))
</script>
