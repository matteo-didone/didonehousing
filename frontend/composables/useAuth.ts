import { useAuthStore } from '~/stores/auth'
import type { LoginCredentials, RegisterData } from '~/types/auth'

export const useAuth = () => {
  const authStore = useAuthStore()

  const getDefaultRoute = () => {
    const { user } = authStore

    if (!user) return '/login'

    // Redirect based on primary role
    if (user.roles.includes('admin')) return '/admin/dashboard'
    if (user.roles.includes('ho')) return '/ho/dashboard'
    if (user.roles.includes('landlord')) return '/landlord/dashboard'
    if (user.roles.includes('tenant')) return '/tenant/dashboard'
    if (user.roles.includes('vendor')) return '/vendor/dashboard'

    return '/dashboard'
  }

  const login = async (credentials: LoginCredentials) => {
    const result = await authStore.login(credentials)

    if (result.success) {
      // Redirect based on role
      const redirectPath = getDefaultRoute()
      await navigateTo(redirectPath)
    }

    return result
  }

  const register = async (data: RegisterData) => {
    const result = await authStore.register(data)

    if (result.success) {
      // Redirect based on role
      const redirectPath = getDefaultRoute()
      await navigateTo(redirectPath)
    }

    return result
  }

  const logout = async () => {
    await authStore.logout()
    await navigateTo('/login')
  }

  const can = (permission: string): boolean => {
    return authStore.hasPermission(permission)
  }

  const canAny = (permissions: string[]): boolean => {
    return authStore.hasAnyPermission(permissions)
  }

  const canAll = (permissions: string[]): boolean => {
    return authStore.hasAllPermissions(permissions)
  }

  const hasRole = (role: string): boolean => {
    return authStore.userRoles.includes(role)
  }

  const hasAnyRole = (roles: string[]): boolean => {
    return roles.some(role => authStore.userRoles.includes(role))
  }

  return {
    // State
    user: computed(() => authStore.user),
    token: computed(() => authStore.token),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    loading: computed(() => authStore.loading),

    // Role getters
    isLandlord: computed(() => authStore.isLandlord),
    isTenant: computed(() => authStore.isTenant),
    isHousingOffice: computed(() => authStore.isHousingOffice),
    isVendor: computed(() => authStore.isVendor),
    isAdmin: computed(() => authStore.isAdmin),

    // Actions
    login,
    register,
    logout,
    fetchUser: authStore.fetchUser,
    restoreAuth: authStore.restoreAuth,

    // Permission helpers
    can,
    canAny,
    canAll,
    hasRole,
    hasAnyRole,
    getDefaultRoute,
  }
}
