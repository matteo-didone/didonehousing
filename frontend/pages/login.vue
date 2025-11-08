<template>
  <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
      <Card>
        <CardHeader class="space-y-1">
          <CardTitle class="text-2xl font-bold text-center">
            {{ $t('auth.loginTitle') }}
          </CardTitle>
          <CardDescription class="text-center">
            {{ $t('auth.loginSubtitle') }}
          </CardDescription>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
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
                {{ $t('auth.email') }}
              </Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                :placeholder="$t('auth.emailPlaceholder')"
                :class="{ 'border-destructive': errors.email }"
              />
              <p v-if="errors.email" class="text-sm text-destructive">
                {{ errors.email[0] }}
              </p>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <Label for="password">
                  {{ $t('auth.password') }}
                </Label>
                <NuxtLink
                  to="/forgot-password"
                  class="text-sm font-medium text-primary hover:underline"
                >
                  {{ $t('auth.forgotPassword') }}
                </NuxtLink>
              </div>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="$t('auth.passwordPlaceholder')"
                :class="{ 'border-destructive': errors.password }"
              />
              <p v-if="errors.password" class="text-sm text-destructive">
                {{ errors.password[0] }}
              </p>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="form.rememberMe"
                type="checkbox"
                class="h-4 w-4 rounded border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2"
              />
              <Label for="remember-me" class="ml-2 cursor-pointer">
                {{ $t('auth.rememberMe') }}
              </Label>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              :disabled="loading"
              class="w-full"
              size="lg"
            >
              <span v-if="loading">{{ $t('auth.signingIn') }}</span>
              <span v-else>{{ $t('auth.login') }}</span>
            </Button>

            <!-- Register Link -->
            <div class="text-center text-sm">
              <span class="text-muted-foreground">{{ $t('auth.noAccount') }}</span>
              <NuxtLink
                to="/register"
                class="ml-1 font-medium text-primary hover:underline"
              >
                {{ $t('auth.createAccount') }}
              </NuxtLink>
            </div>
          </form>
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

const { login } = useAuth()
const { t } = useI18n()

const form = ref({
  email: '',
  password: '',
  rememberMe: false,
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
    errorMessage.value = result.message || t('auth.loginError')
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
