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
      console.log('[AUTH STORE] fetchUser() called. Token:', this.token ? 'EXISTS' : 'NULL')

      if (!this.token) {
        console.log('[AUTH STORE] No token, skipping fetchUser')
        return null
      }

      this.loading = true

      try {
        const config = useRuntimeConfig()
        console.log('[AUTH STORE] Fetching user from:', config.public.apiBase + '/auth/me')

        const response = await $fetch<{ user: User }>('/auth/me', {
          baseURL: config.public.apiBase,
          method: 'GET',
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        })

        console.log('[AUTH STORE] User fetched successfully:', response.user.first_name, response.user.last_name)
        this.user = response.user
        this.isAuthenticated = true

        return response.user
      } catch (error) {
        console.error('[AUTH STORE] Fetch user error:', error)
        this.clearAuth()
        return null
      } finally {
        this.loading = false
      }
    },

    setAuth(user: User, token: string) {
      console.log('[AUTH STORE] setAuth() called for user:', user.first_name, user.last_name)
      this.user = user
      this.token = token
      this.isAuthenticated = true
      console.log('[AUTH STORE] Token will be persisted to cookies by pinia-plugin-persistedstate')
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
      console.log('[AUTH STORE] restoreAuth() called')
      console.log('[AUTH STORE] Current state - token:', this.token ? 'EXISTS' : 'NULL', 'user:', this.user ? 'EXISTS' : 'NULL')

      // Note: State (user, token) is automatically restored from cookies by pinia-plugin-persistedstate
      // We just need to verify the token is still valid by fetching fresh user data
      if (this.token) {
        console.log('[AUTH STORE] Token found, calling fetchUser()')
        await this.fetchUser()
      } else {
        console.log('[AUTH STORE] No token found, skipping restore')
      }
    },
  },

  // Persist state to cookies (SSR-safe)
  // Only persist token (not user object) to avoid 4KB cookie size limit
  // User will be fetched from server on app init using the token
  persist: {
    storage: piniaPluginPersistedstate.cookies(),
    pick: ['token'], // Only persist token, not user object
  },
})
