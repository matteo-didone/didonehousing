export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuthenticated, isTenant, restoreAuth } = useAuth()

  // Try to restore auth from cookie
  if (!isAuthenticated.value) {
    await restoreAuth()
  }

  // If not authenticated, redirect to login
  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  // If not a tenant, redirect to home
  if (!isTenant.value) {
    return navigateTo('/')
  }
})
