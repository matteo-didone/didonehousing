export default defineNuxtPlugin(async (nuxtApp) => {
  const authStore = useAuthStore()

  // Token is automatically restored from cookies by pinia-plugin-persistedstate
  // Now we fetch the full user object from the server using that token
  // This ensures user data is available after page load
  try {
    await authStore.restoreAuth()
  } catch (error) {
    console.error('Failed to restore auth:', error)
  }
})
