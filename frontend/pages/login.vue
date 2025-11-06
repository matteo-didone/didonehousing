<template>
  <div>
    <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
      Sign in to your account
    </h2>

    <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
      <!-- Error Message -->
      <div v-if="errorMessage" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
        <p class="text-sm text-red-800 dark:text-red-200">
          {{ errorMessage }}
        </p>
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

      <!-- Password -->
      <div>
        <label for="password" class="form-label">Password</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          autocomplete="current-password"
          class="form-input"
          :class="{ 'border-red-500': errors.password }"
        />
        <p v-if="errors.password" class="form-error">{{ errors.password[0] }}</p>
      </div>

      <!-- Submit Button -->
      <div>
        <button
          type="submit"
          :disabled="loading"
          class="btn btn-primary w-full"
        >
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </div>

      <!-- Register Link -->
      <div class="text-center text-sm">
        <span class="text-gray-600 dark:text-gray-400">Don't have an account?</span>
        <NuxtLink to="/register" class="ml-1 font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
          Sign up
        </NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const { login } = useAuth()

const form = ref({
  email: '',
  password: '',
})

const loading = ref(false)
const errorMessage = ref('')
const errors = ref<Record<string, string[]>>({})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  const result = await login(form.value)

  if (!result.success) {
    errorMessage.value = result.message
    errors.value = result.errors || {}
  }

  loading.value = false
}

// Clear errors on input
watch(() => form.value.email, () => {
  delete errors.value.email
  errorMessage.value = ''
})

watch(() => form.value.password, () => {
  delete errors.value.password
  errorMessage.value = ''
})
</script>
