export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuthenticated, getDefaultRoute } = useAuth()

  // If authenticated, redirect to dashboard
  if (isAuthenticated.value) {
    return navigateTo(getDefaultRoute())
  }
})
