import { defineStore } from 'pinia'
import type { User, LoginCredentials, RegisterData, AuthResponse } from '~/types/auth'

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  loading: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false,
  }),

  getters: {
    currentUser: (state) => state.user,
    userRoles: (state) => state.user?.roles || [],
    userPermissions: (state) => state.user?.permissions || [],

    // Role checks
    isLandlord: (state) => state.user?.roles.includes('landlord') || false,
    isTenant: (state) => state.user?.roles.includes('tenant') || false,
    isHousingOffice: (state) => state.user?.roles.includes('ho') || false,
    isVendor: (state) => state.user?.roles.includes('vendor') || false,
    isAdmin: (state) => state.user?.roles.includes('admin') || false,

    // Permission checks
    hasPermission: (state) => {
      return (permission: string) => {
        return state.user?.permissions.includes(permission) || false
      }
    },

    hasAnyPermission: (state) => {
      return (permissions: string[]) => {
        return permissions.some(permission =>
          state.user?.permissions.includes(permission)
        ) || false
      }
    },

    hasAllPermissions: (state) => {
      return (permissions: string[]) => {
        return permissions.every(permission =>
          state.user?.permissions.includes(permission)
        ) || false
      }
    },
  },

  actions: {
    async login(credentials: LoginCredentials) {
      this.loading = true

      try {
        const config = useRuntimeConfig()
        const response = await $fetch<AuthResponse>('/auth/login', {
          baseURL: config.public.apiBase,
          method: 'POST',
          body: credentials,
        })

        this.setAuth(response.user, response.token)

        return { success: true, message: response.message }
      } catch (error: any) {
        console.error('Login error:', error)
        return {
          success: false,
          message: error.data?.message || 'Login failed',
          errors: error.data?.errors || {},
        }
      } finally {
        this.loading = false
      }
    },

    async register(data: RegisterData) {
      this.loading = true

      try {
        const config = useRuntimeConfig()
        const response = await $fetch<AuthResponse>('/auth/register', {
          baseURL: config.public.apiBase,
          method: 'POST',
          body: data,
        })

        this.setAuth(response.user, response.token)

        return { success: true, message: response.message }
      } catch (error: any) {
        console.error('Registration error:', error)
        return {
          success: false,
          message: error.data?.message || 'Registration failed',
          errors: error.data?.errors || {},
        }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true

      try {
        if (this.token) {
          const config = useRuntimeConfig()
          await $fetch('/auth/logout', {
            baseURL: config.public.apiBase,
            method: 'POST',
            headers: {
              Authorization: `Bearer ${this.token}`,
            },
          })
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) {
        return null
      }

      this.loading = true

      try {
        const config = useRuntimeConfig()
        const response = await $fetch<{ user: User }>('/auth/me', {
          baseURL: config.public.apiBase,
          method: 'GET',
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        })

        this.user = response.user
        this.isAuthenticated = true

        return response.user
      } catch (error) {
        console.error('Fetch user error:', error)
        this.clearAuth()
        return null
      } finally {
        this.loading = false
      }
    },

    setAuth(user: User, token: string) {
      this.user = user
      this.token = token
      this.isAuthenticated = true
      // Note: State is automatically persisted to cookies by pinia-plugin-persistedstate
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      // Note: State is automatically cleared from cookies by pinia-plugin-persistedstate
    },

    // Restore auth from persisted state (cookies) and verify token validity
    async restoreAuth() {
      // Note: State (user, token) is automatically restored from cookies by pinia-plugin-persistedstate
      // We just need to verify the token is still valid by fetching fresh user data
      if (this.token) {
        await this.fetchUser()
      }
    },
  },

  // Persist state to cookies (SSR-safe)
  persist: {
    storage: piniaPluginPersistedstate.cookies({
      sameSite: 'lax',
      maxAge: 60 * 60 * 24 * 7, // 7 days
    }),
  },
})
