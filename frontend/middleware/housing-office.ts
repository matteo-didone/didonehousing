export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuthenticated, isHousingOffice, restoreAuth } = useAuth()

  // Try to restore auth from cookie
  if (!isAuthenticated.value) {
    await restoreAuth()
  }

  // If not authenticated, redirect to login
  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  // If not housing office, redirect to home
  if (!isHousingOffice.value) {
    return navigateTo('/')
  }
})
