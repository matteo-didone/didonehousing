export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuthenticated, isLandlord, restoreAuth } = useAuth()

  // Try to restore auth from cookie
  if (!isAuthenticated.value) {
    await restoreAuth()
  }

  // If not authenticated, redirect to login
  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  // If not a landlord, redirect to home
  if (!isLandlord.value) {
    return navigateTo('/')
  }
})
