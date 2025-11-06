export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuthenticated, restoreAuth } = useAuth()

  // Try to restore auth from cookie
  if (!isAuthenticated.value) {
    await restoreAuth()
  }

  // If still not authenticated, redirect to login
  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }
})
