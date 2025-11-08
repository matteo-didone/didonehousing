export default defineNuxtPlugin((nuxtApp) => {
  if (process.dev) {
    // Esponi funzioni di debug globalmente
    window.debugAuth = {
      // Ottieni lo store
      getStore: () => {
        return useAuthStore()
      },

      // Diagnostico completo
      diagnostic: async () => {
        console.log('=== 🔍 COMPLETE AUTH DIAGNOSTIC ===\n')

        const authStore = useAuthStore()
        const config = useRuntimeConfig()

        console.log('1️⃣ STORE STATE:')
        console.log('  - isAuthenticated:', authStore.isAuthenticated)
        console.log('  - token:', authStore.token ? `✅ EXISTS (${authStore.token.length} chars)` : '❌ NULL')
        console.log('  - user:', authStore.user ? '✅ EXISTS' : '❌ NULL')
        if (authStore.user) {
          console.log('    - first_name:', authStore.user.first_name)
          console.log('    - last_name:', authStore.user.last_name)
          console.log('    - email:', authStore.user.email)
        }

        console.log('\n2️⃣ COOKIES:')
        const cookies = document.cookie.split(';').map(c => c.trim())
        if (cookies.length === 0 || (cookies.length === 1 && cookies[0] === '')) {
          console.log('  ❌ NO COOKIES FOUND')
        } else {
          cookies.forEach(c => console.log('  -', c))
        }

        console.log('\n3️⃣ CONFIGURATION:')
        console.log('  - apiBase:', config.public.apiBase)

        console.log('\n4️⃣ TESTING restoreAuth():')
        try {
          await authStore.restoreAuth()
          console.log('  ✅ restoreAuth completed')
        } catch (error) {
          console.log('  ❌ restoreAuth failed:', error.message)
        }

        console.log('\n5️⃣ FINAL STATE:')
        console.log('  - token:', authStore.token ? '✅ EXISTS' : '❌ NULL')
        console.log('  - user:', authStore.user ? '✅ EXISTS' : '❌ NULL')
        if (authStore.user) {
          console.log('    - name:', authStore.user.first_name, authStore.user.last_name)
        }

        console.log('\n=== END DIAGNOSTIC ===')
      },

      // Test restoreAuth
      testRestore: async () => {
        const authStore = useAuthStore()
        console.log('=== BEFORE restoreAuth ===')
        console.log('token:', authStore.token ? 'EXISTS' : 'NULL')
        console.log('user:', authStore.user ? 'EXISTS' : 'NULL')

        await authStore.restoreAuth()

        console.log('=== AFTER restoreAuth ===')
        console.log('token:', authStore.token ? 'EXISTS' : 'NULL')
        console.log('user:', authStore.user)
      },

      // Test fetchUser
      testFetch: async () => {
        const authStore = useAuthStore()
        console.log('=== Testing fetchUser ===')
        console.log('Current token:', authStore.token ? 'EXISTS' : 'NULL')

        const result = await authStore.fetchUser()
        console.log('Result:', result)
        console.log('User after fetch:', authStore.user)
      },

      // Verifica cookies
      checkCookies: () => {
        console.log('=== ALL COOKIES ===')
        document.cookie.split(';').forEach(c => console.log(c.trim()))

        console.log('\n=== AUTH COOKIE ===')
        const authCookie = document.cookie.split(';').find(c => c.includes('auth'))
        console.log(authCookie || 'NO AUTH COOKIE FOUND')
      },

      // Verifica dimensione stato
      checkSize: () => {
        const authStore = useAuthStore()
        const state = { token: authStore.token, user: authStore.user }
        const stateJSON = JSON.stringify(state)
        const sizeInBytes = new Blob([stateJSON]).size

        console.log('=== STATE SIZE ===')
        console.log('Full state size:', sizeInBytes, 'bytes')
        console.log('Cookie limit:', 4096, 'bytes')
        console.log('Would fit in cookie?', sizeInBytes < 4096)

        // Solo token
        const tokenOnly = { token: authStore.token }
        const tokenJSON = JSON.stringify(tokenOnly)
        const tokenSize = new Blob([tokenJSON]).size
        console.log('Token-only size:', tokenSize, 'bytes')
      },

      // Mostra stato corrente
      showState: () => {
        const authStore = useAuthStore()
        console.log('=== CURRENT STATE ===')
        console.log('isAuthenticated:', authStore.isAuthenticated)
        console.log('token:', authStore.token)
        console.log('user:', authStore.user)
      },

      // Help
      help: () => {
        console.log('=== DEBUG AUTH HELPERS ===')
        console.log('Available commands:')
        console.log('  debugAuth.diagnostic()     - Run complete diagnostic')
        console.log('  debugAuth.testRestore()    - Test restoreAuth()')
        console.log('  debugAuth.testFetch()      - Test fetchUser()')
        console.log('  debugAuth.checkCookies()   - Check all cookies')
        console.log('  debugAuth.checkSize()      - Check state size')
        console.log('  debugAuth.showState()      - Show current state')
        console.log('  debugAuth.getStore()       - Get auth store instance')
        console.log('  debugAuth.help()           - Show this help')
      }
    }

    console.log('🔧 Debug helpers loaded! Type debugAuth.help() for available commands')
  }
})
