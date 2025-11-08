<template>
  <div class="mx-auto max-w-4xl space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
    </div>

    <!-- Tabs -->
    <div class="border-b border-border">
      <nav class="flex gap-4">
        <button
          @click="activeTab = 'personal'"
          :class="[
            'px-4 py-3 text-sm font-medium transition-colors border-b-2',
            activeTab === 'personal'
              ? 'border-primary text-primary'
              : 'border-transparent text-muted-foreground hover:text-foreground'
          ]"
        >
          {{ translations.personalInfo }}
        </button>
        <button
          @click="activeTab = 'security'"
          :class="[
            'px-4 py-3 text-sm font-medium transition-colors border-b-2',
            activeTab === 'security'
              ? 'border-primary text-primary'
              : 'border-transparent text-muted-foreground hover:text-foreground'
          ]"
        >
          {{ translations.accountSettings }}
        </button>
        <button
          @click="activeTab = 'notifications'"
          :class="[
            'px-4 py-3 text-sm font-medium transition-colors border-b-2',
            activeTab === 'notifications'
              ? 'border-primary text-primary'
              : 'border-transparent text-muted-foreground hover:text-foreground'
          ]"
        >
          {{ translations.notifications }}
        </button>
      </nav>
    </div>

    <!-- Personal Information Tab -->
    <Card v-if="activeTab === 'personal'">
      <CardHeader>
        <CardTitle>{{ translations.personalInfo }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleUpdateProfile" class="space-y-4">
          <!-- Name Fields -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="firstName">{{ translations.firstName }}</Label>
              <Input
                id="firstName"
                v-model="profileForm.firstName"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="lastName">{{ translations.lastName }}</Label>
              <Input
                id="lastName"
                v-model="profileForm.lastName"
                required
              />
            </div>
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <Label for="email">{{ translations.email }}</Label>
            <Input
              id="email"
              v-model="profileForm.email"
              type="email"
              required
            />
          </div>

          <!-- Phone -->
          <div class="space-y-2">
            <Label for="phone">{{ translations.phone }}</Label>
            <Input
              id="phone"
              v-model="profileForm.phone"
              type="tel"
            />
          </div>

          <!-- Language -->
          <div class="space-y-2">
            <Label for="language">{{ translations.language }}</Label>
            <Select id="language" v-model="profileForm.language">
              <option value="en">{{ t('language.englishUS') }}</option>
              <option value="it">{{ t('language.italiano') }}</option>
            </Select>
          </div>

          <!-- Role (Read-only) -->
          <div class="space-y-2">
            <Label for="role">{{ translations.role }}</Label>
            <Input
              id="role"
              :value="user?.roles.join(', ')"
              disabled
              class="bg-muted"
            />
          </div>

          <!-- Member Since (Read-only) -->
          <div class="space-y-2">
            <Label for="memberSince">{{ translations.memberSince }}</Label>
            <Input
              id="memberSince"
              :value="formatDate(user?.created_at)"
              disabled
              class="bg-muted"
            />
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="updatingProfile">
              <span v-if="updatingProfile">{{ translations.updating }}</span>
              <span v-else>{{ translations.updateProfile }}</span>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Security Tab -->
    <Card v-if="activeTab === 'security'">
      <CardHeader>
        <CardTitle>{{ translations.changePassword }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleUpdatePassword" class="space-y-4">
          <div class="space-y-2">
            <Label for="currentPassword">{{ translations.currentPassword }}</Label>
            <Input
              id="currentPassword"
              v-model="passwordForm.current"
              type="password"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="newPassword">{{ translations.newPassword }}</Label>
            <Input
              id="newPassword"
              v-model="passwordForm.new"
              type="password"
              required
              minlength="8"
            />
          </div>

          <div class="space-y-2">
            <Label for="confirmPassword">{{ translations.confirmPassword }}</Label>
            <Input
              id="confirmPassword"
              v-model="passwordForm.confirm"
              type="password"
              required
              minlength="8"
            />
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="updatingPassword">
              <span v-if="updatingPassword">{{ translations.updating }}</span>
              <span v-else>{{ translations.updatePassword }}</span>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Notifications Tab -->
    <Card v-if="activeTab === 'notifications'">
      <CardHeader>
        <CardTitle>{{ translations.emailNotifications }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleSaveSettings" class="space-y-6">
          <!-- Property Updates -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <p class="font-medium">{{ translations.propertyUpdates }}</p>
              <p class="text-sm text-muted-foreground">{{ translations.propertyUpdatesDesc }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="notificationsForm.propertyUpdates"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
          </div>

          <!-- Maintenance Alerts -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <p class="font-medium">{{ translations.maintenanceAlerts }}</p>
              <p class="text-sm text-muted-foreground">{{ translations.maintenanceAlertsDesc }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="notificationsForm.maintenanceAlerts"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
          </div>

          <!-- Payment Reminders -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <p class="font-medium">{{ translations.paymentReminders }}</p>
              <p class="text-sm text-muted-foreground">{{ translations.paymentRemindersDesc }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="notificationsForm.paymentReminders"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
          </div>

          <!-- Message Notifications -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <p class="font-medium">{{ translations.messageNotifications }}</p>
              <p class="text-sm text-muted-foreground">{{ translations.messageNotificationsDesc }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="notificationsForm.messageNotifications"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="savingSettings">
              <span v-if="savingSettings">{{ translations.saving }}</span>
              <span v-else>{{ translations.saveSettings }}</span>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
})

const { user } = useAuth()
const { t, setLocale } = useI18n()

// Translations
const translations = ref({
  title: '',
  personalInfo: '',
  accountSettings: '',
  notifications: '',
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  language: '',
  role: '',
  memberSince: '',
  updateProfile: '',
  updating: '',
  changePassword: '',
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  updatePassword: '',
  emailNotifications: '',
  propertyUpdates: '',
  propertyUpdatesDesc: '',
  maintenanceAlerts: '',
  maintenanceAlertsDesc: '',
  paymentReminders: '',
  paymentRemindersDesc: '',
  messageNotifications: '',
  messageNotificationsDesc: '',
  saveSettings: '',
  saving: '',
  success: '',
  passwordSuccess: '',
  settingsSuccess: '',
})

onMounted(() => {
  translations.value = {
    title: t('profile.title'),
    personalInfo: t('profile.personalInfo'),
    accountSettings: t('profile.accountSettings'),
    notifications: t('profile.notifications'),
    firstName: t('profile.firstName'),
    lastName: t('profile.lastName'),
    email: t('profile.email'),
    phone: t('profile.phone'),
    language: t('profile.language'),
    role: t('profile.role'),
    memberSince: t('profile.memberSince'),
    updateProfile: t('profile.updateProfile'),
    updating: t('profile.updating'),
    changePassword: t('profile.changePassword'),
    currentPassword: t('profile.currentPassword'),
    newPassword: t('profile.newPassword'),
    confirmPassword: t('profile.confirmPassword'),
    updatePassword: t('profile.updatePassword'),
    emailNotifications: t('profile.emailNotifications'),
    propertyUpdates: t('profile.propertyUpdates'),
    propertyUpdatesDesc: t('profile.propertyUpdatesDesc'),
    maintenanceAlerts: t('profile.maintenanceAlerts'),
    maintenanceAlertsDesc: t('profile.maintenanceAlertsDesc'),
    paymentReminders: t('profile.paymentReminders'),
    paymentRemindersDesc: t('profile.paymentRemindersDesc'),
    messageNotifications: t('profile.messageNotifications'),
    messageNotificationsDesc: t('profile.messageNotificationsDesc'),
    saveSettings: t('profile.saveSettings'),
    saving: t('profile.saving'),
    success: t('profile.success'),
    passwordSuccess: t('profile.passwordSuccess'),
    settingsSuccess: t('profile.settingsSuccess'),
  }

  // Initialize forms with user data
  if (user.value) {
    profileForm.value = {
      firstName: user.value.first_name,
      lastName: user.value.last_name,
      email: user.value.email,
      phone: user.value.phone || '',
      language: user.value.preferred_language || 'en',
    }
  }
})

// State
const activeTab = ref('personal')
const updatingProfile = ref(false)
const updatingPassword = ref(false)
const savingSettings = ref(false)

const profileForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  language: 'en',
})

const passwordForm = ref({
  current: '',
  new: '',
  confirm: '',
})

const notificationsForm = ref({
  propertyUpdates: true,
  maintenanceAlerts: true,
  paymentReminders: true,
  messageNotifications: true,
})

// Methods
const formatDate = (date: string | undefined) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const handleUpdateProfile = async () => {
  updatingProfile.value = true
  try {
    // TODO: API call to update profile
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Update language if changed
    if (profileForm.value.language !== user.value?.preferred_language) {
      setLocale(profileForm.value.language)
    }

    alert(translations.value.success)
  } catch (error) {
    console.error('Error updating profile:', error)
    alert('Error updating profile')
  } finally {
    updatingProfile.value = false
  }
}

const handleUpdatePassword = async () => {
  if (passwordForm.value.new !== passwordForm.value.confirm) {
    alert('Passwords do not match')
    return
  }

  updatingPassword.value = true
  try {
    // TODO: API call to update password
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Clear form
    passwordForm.value = {
      current: '',
      new: '',
      confirm: '',
    }

    alert(translations.value.passwordSuccess)
  } catch (error) {
    console.error('Error updating password:', error)
    alert('Error updating password')
  } finally {
    updatingPassword.value = false
  }
}

const handleSaveSettings = async () => {
  savingSettings.value = true
  try {
    // TODO: API call to save notification settings
    await new Promise((resolve) => setTimeout(resolve, 1000))
    alert(translations.value.settingsSuccess)
  } catch (error) {
    console.error('Error saving settings:', error)
    alert('Error saving settings')
  } finally {
    savingSettings.value = false
  }
}
</script>
