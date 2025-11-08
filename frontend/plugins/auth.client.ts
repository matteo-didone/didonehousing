export default defineNuxtPlugin(async (nuxtApp) => {
  console.log('[AUTH PLUGIN] Starting auth plugin...')
  const authStore = useAuthStore()

  console.log('[AUTH PLUGIN] Auth store state:', {
    token: authStore.token ? 'EXISTS' : 'NULL',
    user: authStore.user ? 'EXISTS' : 'NULL',
    isAuthenticated: authStore.isAuthenticated,
  })

  // Token is automatically restored from cookies by pinia-plugin-persistedstate
  // Now we fetch the full user object from the server using that token
  // This ensures user data is available after page load
  try {
    console.log('[AUTH PLUGIN] Calling restoreAuth()...')
    await authStore.restoreAuth()
    console.log('[AUTH PLUGIN] restoreAuth() completed. User:', authStore.user?.first_name || 'NULL')
  } catch (error) {
    console.error('[AUTH PLUGIN] Failed to restore auth:', error)
  }
})
