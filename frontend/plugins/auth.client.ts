export default defineNuxtPlugin(async (nuxtApp) => {
  const authStore = useAuthStore()

  // Restore auth from persisted state (localStorage/cookie)
  // This ensures user object is available immediately after page load
  try {
    await authStore.restoreAuth()
  } catch (error) {
    console.error('Failed to restore auth:', error)
  }
})
