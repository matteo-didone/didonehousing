import { ref } from 'vue'

export interface ProfileUpdateData {
  first_name?: string
  last_name?: string
  email?: string
  phone?: string
  preferred_language?: string
}

export interface PasswordUpdateData {
  current_password: string
  new_password: string
  new_password_confirmation: string
}

export interface NotificationSettings {
  property_updates?: boolean
  maintenance_alerts?: boolean
  payment_reminders?: boolean
  message_notifications?: boolean
}

export const useProfile = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const updateProfile = async (data: ProfileUpdateData) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/profile`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(data),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update profile')
      }

      const result = await response.json()
      return result
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const updatePassword = async (data: PasswordUpdateData) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/profile/password`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(data),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update password')
      }

      const result = await response.json()
      return result
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateNotificationSettings = async (settings: NotificationSettings) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/profile/notification-settings`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(settings),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update notification settings')
      }

      const result = await response.json()
      return result
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    updateProfile,
    updatePassword,
    updateNotificationSettings,
  }
}
