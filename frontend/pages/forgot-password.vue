<template>
  <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
      <Card>
        <CardHeader class="space-y-1">
          <CardTitle class="text-2xl font-bold text-center">
            {{ t('auth.forgotPasswordTitle') }}
          </CardTitle>
          <CardDescription class="text-center">
            {{ t('auth.forgotPasswordSubtitle') }}
          </CardDescription>
        </CardHeader>

        <CardContent>
          <!-- Success Message -->
          <div
            v-if="successMessage"
            class="mb-6 rounded-md bg-success/10 border border-success/20 p-4"
          >
            <div class="flex items-start">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-2 mt-0.5 flex-shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <path d="m9 11 3 3L22 4" />
              </svg>
              <div>
                <p class="text-sm font-semibold text-success">
                  {{ t('auth.resetLinkSent') }}
                </p>
                <p class="text-sm text-success/80 mt-1">
                  {{ t('auth.resetLinkSentMessage') }}
                </p>
              </div>
            </div>
          </div>

          <form v-if="!successMessage" @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Error Message -->
            <div
              v-if="errorMessage"
              class="rounded-md bg-destructive/10 border border-destructive/20 p-3"
            >
              <p class="text-sm text-destructive">
                {{ errorMessage }}
              </p>
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
              <Label for="email">
                {{ t('auth.email') }}
              </Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                :placeholder="t('auth.emailPlaceholder')"
                :class="{ 'border-destructive': errors.email }"
              />
              <p v-if="errors.email" class="text-sm text-destructive">
                {{ errors.email[0] }}
              </p>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              :disabled="loading"
              class="w-full"
              size="lg"
            >
              <span v-if="loading">{{ t('auth.sendingResetLink') }}</span>
              <span v-else>{{ t('auth.sendResetLink') }}</span>
            </Button>
          </form>

          <!-- Back to Login Link -->
          <div class="mt-6 text-center text-sm">
            <NuxtLink
              to="/login"
              class="font-medium text-primary hover:underline"
            >
              {{ t('auth.backToLogin') }}
            </NuxtLink>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'

definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const { t } = useI18n()

const form = ref({
  email: '',
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref(false)
const errors = ref<Record<string, string[]>>({})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  try {
    // Simulate API call - replace with actual password reset endpoint
    await new Promise((resolve) => setTimeout(resolve, 1500))

    // For now, just show success message
    // In production, this would call the backend API:
    // const response = await $fetch('/api/auth/forgot-password', {
    //   method: 'POST',
    //   body: { email: form.value.email }
    // })

    successMessage.value = true
  } catch (error: any) {
    errorMessage.value = error?.message || 'An error occurred. Please try again.'
    console.error('Password reset error:', error)
  } finally {
    loading.value = false
  }
}

// Clear errors on input
watch(() => form.value.email, () => {
  delete errors.value.email
  errorMessage.value = ''
})
</script>
