<template>
  <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl space-y-8">
      <Card>
        <CardHeader class="space-y-1">
          <CardTitle class="text-2xl font-bold text-center">
            {{ translations.registerTitle }}
          </CardTitle>
          <CardDescription class="text-center">
            {{ translations.registerSubtitle }}
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

            <!-- Name Fields (Grid 2 Columns) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <!-- First Name -->
              <div class="space-y-2">
                <Label for="first_name">
                  {{ translations.firstName }}
                </Label>
                <Input
                  id="first_name"
                  v-model="form.first_name"
                  type="text"
                  required
                  :placeholder="translations.firstNamePlaceholder"
                  :class="{ 'border-destructive': errors.first_name }"
                />
                <p v-if="errors.first_name" class="text-sm text-destructive">
                  {{ errors.first_name[0] }}
                </p>
              </div>

              <!-- Last Name -->
              <div class="space-y-2">
                <Label for="last_name">
                  {{ translations.lastName }}
                </Label>
                <Input
                  id="last_name"
                  v-model="form.last_name"
                  type="text"
                  required
                  :placeholder="translations.lastNamePlaceholder"
                  :class="{ 'border-destructive': errors.last_name }"
                />
                <p v-if="errors.last_name" class="text-sm text-destructive">
                  {{ errors.last_name[0] }}
                </p>
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <Label for="email">
                {{ translations.email }}
              </Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                :placeholder="translations.emailPlaceholder"
                :class="{ 'border-destructive': errors.email }"
              />
              <p v-if="errors.email" class="text-sm text-destructive">
                {{ errors.email[0] }}
              </p>
            </div>

            <!-- Phone -->
            <div class="space-y-2">
              <Label for="phone">
                {{ translations.phone }}
              </Label>
              <Input
                id="phone"
                v-model="form.phone"
                type="tel"
                :placeholder="translations.phonePlaceholder"
                :class="{ 'border-destructive': errors.phone }"
              />
              <p v-if="errors.phone" class="text-sm text-destructive">
                {{ errors.phone[0] }}
              </p>
            </div>

            <!-- Role -->
            <div class="space-y-2">
              <Label for="role">
                {{ translations.role }}
              </Label>
              <Select
                id="role"
                v-model="form.role"
                required
                :class="{ 'border-destructive': errors.role }"
              >
                <option value="">{{ translations.roleSelect }}</option>
                <option value="tenant">{{ translations.roleTenant }}</option>
                <option value="landlord">{{ translations.roleLandlord }}</option>
                <option value="vendor">{{ translations.roleVendor }}</option>
              </Select>
              <p v-if="errors.role" class="text-sm text-destructive">
                {{ errors.role[0] }}
              </p>
            </div>

            <!-- Password Fields (Grid 2 Columns) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <!-- Password -->
              <div class="space-y-2">
                <Label for="password">
                  {{ translations.password }}
                </Label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  autocomplete="new-password"
                  :placeholder="translations.passwordPlaceholder"
                  :class="{ 'border-destructive': errors.password }"
                />
                <p v-if="errors.password" class="text-sm text-destructive">
                  {{ errors.password[0] }}
                </p>
                <p class="text-xs text-muted-foreground">
                  {{ translations.passwordHint }}
                </p>
              </div>

              <!-- Password Confirmation -->
              <div class="space-y-2">
                <Label for="password_confirmation">
                  {{ translations.confirmPassword }}
                </Label>
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  autocomplete="new-password"
                  :placeholder="translations.confirmPasswordPlaceholder"
                  :class="{ 'border-destructive': errors.password_confirmation }"
                />
                <p v-if="errors.password_confirmation" class="text-sm text-destructive">
                  {{ errors.password_confirmation[0] }}
                </p>
              </div>
            </div>

            <!-- Preferred Language -->
            <div class="space-y-2">
              <Label for="locale">
                {{ translations.preferredLanguage }}
              </Label>
              <Select
                id="locale"
                v-model="form.locale"
                required
              >
                <option value="en">English (US)</option>
                <option value="it">Italiano</option>
              </Select>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              :disabled="loading"
              class="w-full"
              size="lg"
            >
              <span v-if="loading">{{ translations.creatingAccount }}</span>
              <span v-else>{{ translations.createAccount }}</span>
            </Button>

            <!-- Login Link -->
            <div class="text-center text-sm">
              <span class="text-muted-foreground">{{ translations.hasAccount }}</span>
              <NuxtLink
                to="/login"
                class="ml-1 font-medium text-primary hover:underline"
              >
                {{ translations.login }}
              </NuxtLink>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { RegisterData } from '~/types/auth'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Label from '@/components/ui/Label.vue'

definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const { register } = useAuth()
const { t, locale } = useI18n()

// Use onMounted to ensure translations are loaded client-side
const translations = ref({
  registerTitle: '',
  registerSubtitle: '',
  firstName: '',
  firstNamePlaceholder: '',
  lastName: '',
  lastNamePlaceholder: '',
  email: '',
  emailPlaceholder: '',
  phone: '',
  phonePlaceholder: '',
  role: '',
  roleSelect: '',
  roleTenant: '',
  roleLandlord: '',
  roleVendor: '',
  password: '',
  passwordPlaceholder: '',
  confirmPassword: '',
  confirmPasswordPlaceholder: '',
  passwordHint: '',
  preferredLanguage: '',
  creatingAccount: '',
  createAccount: '',
  hasAccount: '',
  login: '',
})

onMounted(() => {
  translations.value = {
    registerTitle: t('auth.registerTitle'),
    registerSubtitle: t('auth.registerSubtitle'),
    firstName: t('auth.firstName'),
    firstNamePlaceholder: t('auth.firstNamePlaceholder'),
    lastName: t('auth.lastName'),
    lastNamePlaceholder: t('auth.lastNamePlaceholder'),
    email: t('auth.email'),
    emailPlaceholder: t('auth.emailPlaceholder'),
    phone: t('auth.phone'),
    phonePlaceholder: t('auth.phonePlaceholder'),
    role: t('auth.role'),
    roleSelect: t('auth.roleSelect'),
    roleTenant: t('auth.roleTenant'),
    roleLandlord: t('auth.roleLandlord'),
    roleVendor: t('auth.roleVendor'),
    password: t('auth.password'),
    passwordPlaceholder: t('auth.passwordPlaceholder'),
    confirmPassword: t('auth.confirmPassword'),
    confirmPasswordPlaceholder: t('auth.confirmPasswordPlaceholder'),
    passwordHint: t('auth.passwordHint'),
    preferredLanguage: t('auth.preferredLanguage'),
    creatingAccount: t('auth.creatingAccount'),
    createAccount: t('auth.createAccount'),
    hasAccount: t('auth.hasAccount'),
    login: t('auth.login'),
  }
})

// Watch for locale changes
watch(locale, () => {
  translations.value = {
    registerTitle: t('auth.registerTitle'),
    registerSubtitle: t('auth.registerSubtitle'),
    firstName: t('auth.firstName'),
    firstNamePlaceholder: t('auth.firstNamePlaceholder'),
    lastName: t('auth.lastName'),
    lastNamePlaceholder: t('auth.lastNamePlaceholder'),
    email: t('auth.email'),
    emailPlaceholder: t('auth.emailPlaceholder'),
    phone: t('auth.phone'),
    phonePlaceholder: t('auth.phonePlaceholder'),
    role: t('auth.role'),
    roleSelect: t('auth.roleSelect'),
    roleTenant: t('auth.roleTenant'),
    roleLandlord: t('auth.roleLandlord'),
    roleVendor: t('auth.roleVendor'),
    password: t('auth.password'),
    passwordPlaceholder: t('auth.passwordPlaceholder'),
    confirmPassword: t('auth.confirmPassword'),
    confirmPasswordPlaceholder: t('auth.confirmPasswordPlaceholder'),
    passwordHint: t('auth.passwordHint'),
    preferredLanguage: t('auth.preferredLanguage'),
    creatingAccount: t('auth.creatingAccount'),
    createAccount: t('auth.createAccount'),
    hasAccount: t('auth.hasAccount'),
    login: t('auth.login'),
  }
})

const form = ref<RegisterData>({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  locale: locale.value as 'en' | 'it',
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
    errorMessage.value = result.message || t('auth.loginError')
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
