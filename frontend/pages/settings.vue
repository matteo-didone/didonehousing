<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ t('profile.title') }}</h1>
      <p class="mt-2 text-muted-foreground">{{ t('profile.accountSettings') }}</p>
    </div>

    <!-- Personal Information -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.personalInfo') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="firstName">{{ t('profile.firstName') }}</Label>
              <Input
                id="firstName"
                v-model="profileForm.firstName"
                type="text"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="lastName">{{ t('profile.lastName') }}</Label>
              <Input
                id="lastName"
                v-model="profileForm.lastName"
                type="text"
                required
              />
            </div>
          </div>

          <div class="space-y-2">
            <Label for="email">{{ t('profile.email') }}</Label>
            <Input
              id="email"
              v-model="profileForm.email"
              type="email"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="phone">{{ t('profile.phone') }}</Label>
            <Input
              id="phone"
              v-model="profileForm.phone"
              type="tel"
            />
          </div>

          <div class="space-y-2">
            <Label for="language">{{ t('profile.language') }}</Label>
            <Select v-model="profileForm.language">
              <option value="en">{{ t('language.englishUS') }}</option>
              <option value="it">{{ t('language.italiano') }}</option>
            </Select>
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="updatingProfile">
              {{ updatingProfile ? t('profile.updating') : t('profile.updateProfile') }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Change Password -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.changePassword') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="updatePassword" class="space-y-4">
          <div class="space-y-2">
            <Label for="currentPassword">{{ t('profile.currentPassword') }}</Label>
            <Input
              id="currentPassword"
              v-model="passwordForm.currentPassword"
              type="password"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="newPassword">{{ t('profile.newPassword') }}</Label>
            <Input
              id="newPassword"
              v-model="passwordForm.newPassword"
              type="password"
              required
            />
            <p class="text-sm text-muted-foreground">{{ t('auth.passwordHint') }}</p>
          </div>

          <div class="space-y-2">
            <Label for="confirmPassword">{{ t('profile.confirmPassword') }}</Label>
            <Input
              id="confirmPassword"
              v-model="passwordForm.confirmPassword"
              type="password"
              required
            />
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="updatingPassword">
              {{ updatingPassword ? t('profile.updating') : t('profile.updatePassword') }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Notification Preferences -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.notifications') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="updateNotifications" class="space-y-6">
          <div class="space-y-4">
            <!-- Email Notifications Header -->
            <div>
              <h3 class="text-sm font-medium">{{ t('profile.emailNotifications') }}</h3>
            </div>

            <!-- Property Updates -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <label for="propertyUpdates" class="text-sm font-medium">
                  {{ t('profile.propertyUpdates') }}
                </label>
                <p class="text-sm text-muted-foreground">
                  {{ t('profile.propertyUpdatesDesc') }}
                </p>
              </div>
              <input
                id="propertyUpdates"
                type="checkbox"
                v-model="notificationForm.propertyUpdates"
                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Maintenance Alerts -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <label for="maintenanceAlerts" class="text-sm font-medium">
                  {{ t('profile.maintenanceAlerts') }}
                </label>
                <p class="text-sm text-muted-foreground">
                  {{ t('profile.maintenanceAlertsDesc') }}
                </p>
              </div>
              <input
                id="maintenanceAlerts"
                type="checkbox"
                v-model="notificationForm.maintenanceAlerts"
                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Payment Reminders -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <label for="paymentReminders" class="text-sm font-medium">
                  {{ t('profile.paymentReminders') }}
                </label>
                <p class="text-sm text-muted-foreground">
                  {{ t('profile.paymentRemindersDesc') }}
                </p>
              </div>
              <input
                id="paymentReminders"
                type="checkbox"
                v-model="notificationForm.paymentReminders"
                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Message Notifications -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <label for="messageNotifications" class="text-sm font-medium">
                  {{ t('profile.messageNotifications') }}
                </label>
                <p class="text-sm text-muted-foreground">
                  {{ t('profile.messageNotificationsDesc') }}
                </p>
              </div>
              <input
                id="messageNotifications"
                type="checkbox"
                v-model="notificationForm.messageNotifications"
                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
              />
            </div>
          </div>

          <div class="flex justify-end">
            <Button type="submit" :disabled="updatingNotifications">
              {{ updatingNotifications ? t('profile.saving') : t('profile.saveSettings') }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
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
  middleware: ['auth'],
})

const { user } = useAuth()
const { t, locale, setLocale } = useI18n()

// Profile form
const profileForm = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  language: 'en',
})

// Password form
const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
})

// Notification form
const notificationForm = reactive({
  propertyUpdates: true,
  maintenanceAlerts: true,
  paymentReminders: true,
  messageNotifications: true,
})

const updatingProfile = ref(false)
const updatingPassword = ref(false)
const updatingNotifications = ref(false)

// Initialize form with user data
onMounted(() => {
  if (user.value) {
    profileForm.firstName = user.value.first_name || ''
    profileForm.lastName = user.value.last_name || ''
    profileForm.email = user.value.email || ''
    profileForm.phone = user.value.phone || ''
    profileForm.language = locale.value
  }
})

// Update profile
const updateProfile = async () => {
  updatingProfile.value = true

  try {
    // TODO: Implement API call to update profile
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call

    // Update language if changed
    if (profileForm.language !== locale.value) {
      setLocale(profileForm.language)
    }

    // Show success message (TODO: add toast notification)
    console.log(t('profile.success'))
  } catch (error) {
    console.error('Failed to update profile:', error)
  } finally {
    updatingProfile.value = false
  }
}

// Update password
const updatePassword = async () => {
  // Validate passwords match
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    // TODO: Show error message
    console.error('Passwords do not match')
    return
  }

  updatingPassword.value = true

  try {
    // TODO: Implement API call to update password
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call

    // Clear form
    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.confirmPassword = ''

    // Show success message (TODO: add toast notification)
    console.log(t('profile.passwordSuccess'))
  } catch (error) {
    console.error('Failed to update password:', error)
  } finally {
    updatingPassword.value = false
  }
}

// Update notifications
const updateNotifications = async () => {
  updatingNotifications.value = true

  try {
    // TODO: Implement API call to update notification preferences
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call

    // Show success message (TODO: add toast notification)
    console.log(t('profile.settingsSuccess'))
  } catch (error) {
    console.error('Failed to update notifications:', error)
  } finally {
    updatingNotifications.value = false
  }
}
</script>
