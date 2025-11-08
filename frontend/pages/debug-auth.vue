<template>
  <div class="container mx-auto p-8 space-y-6">
    <h1 class="text-3xl font-bold">Auth Debug Page</h1>

    <!-- Store State -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">Auth Store State</h2>
      <div class="space-y-2 font-mono text-sm">
        <div><strong>isAuthenticated:</strong> {{ authStore.isAuthenticated }}</div>
        <div><strong>token:</strong> {{ authStore.token ? `${authStore.token.substring(0, 20)}...` : 'null' }}</div>
        <div><strong>user:</strong></div>
        <pre class="bg-white dark:bg-gray-800 p-2 rounded overflow-auto">{{ JSON.stringify(authStore.user, null, 2) }}</pre>
      </div>
    </div>

    <!-- Cookies -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">Cookies</h2>
      <div class="space-y-2 font-mono text-sm">
        <div v-for="cookie in parsedCookies" :key="cookie.name">
          <strong>{{ cookie.name }}:</strong> {{ cookie.value }}
        </div>
      </div>
    </div>

    <!-- Composable User -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">useAuth() Composable</h2>
      <div class="space-y-2 font-mono text-sm">
        <div><strong>isAuthenticated:</strong> {{ isAuthenticated }}</div>
        <div><strong>user from composable:</strong></div>
        <pre class="bg-white dark:bg-gray-800 p-2 rounded overflow-auto">{{ JSON.stringify(user, null, 2) }}</pre>
      </div>
    </div>

    <!-- Actions -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">Test Actions</h2>
      <div class="space-y-3">
        <button
          @click="testRestoreAuth"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          :disabled="loading"
        >
          Test restoreAuth()
        </button>

        <button
          @click="testFetchUser"
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
          :disabled="loading"
        >
          Test fetchUser()
        </button>

        <button
          @click="clearAllCookies"
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
        >
          Clear All Cookies
        </button>

        <button
          @click="refreshPage"
          class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600"
        >
          Refresh Page
        </button>
      </div>
    </div>

    <!-- Logs -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">Action Logs</h2>
      <div class="bg-black text-green-400 p-4 rounded font-mono text-xs space-y-1 max-h-96 overflow-auto">
        <div v-for="(log, index) in logs" :key="index">
          [{{ log.time }}] {{ log.message }}
        </div>
      </div>
    </div>

    <!-- Plugin Execution Check -->
    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
      <h2 class="text-xl font-semibold mb-4">Plugin Check</h2>
      <div class="font-mono text-sm">
        Check browser console for "Failed to restore auth" errors
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const { user, isAuthenticated } = useAuth()

const loading = ref(false)
const logs = ref<Array<{ time: string, message: string }>>([])

const addLog = (message: string) => {
  const time = new Date().toLocaleTimeString()
  logs.value.push({ time, message })
  console.log(`[${time}] ${message}`)
}

// Parse cookies
const parsedCookies = computed(() => {
  if (process.client) {
    return document.cookie.split(';').map(c => {
      const [name, ...rest] = c.trim().split('=')
      return { name, value: rest.join('=') }
    })
  }
  return []
})

const testRestoreAuth = async () => {
  loading.value = true
  addLog('Testing restoreAuth()...')
  try {
    await authStore.restoreAuth()
    addLog('✓ restoreAuth() completed successfully')
    addLog(`User after restore: ${authStore.user?.first_name || 'null'}`)
  } catch (error: any) {
    addLog(`✗ restoreAuth() failed: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const testFetchUser = async () => {
  loading.value = true
  addLog('Testing fetchUser()...')
  addLog(`Current token: ${authStore.token ? 'EXISTS' : 'NULL'}`)
  try {
    const result = await authStore.fetchUser()
    addLog('✓ fetchUser() completed')
    addLog(`User fetched: ${result?.first_name || 'null'}`)
  } catch (error: any) {
    addLog(`✗ fetchUser() failed: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const clearAllCookies = () => {
  if (process.client) {
    document.cookie.split(";").forEach((c) => {
      document.cookie = c
        .replace(/^ +/, "")
        .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/")
    })
    addLog('✓ All cookies cleared')
  }
}

const refreshPage = () => {
  if (process.client) {
    window.location.reload()
  }
}

// On mount
onMounted(() => {
  addLog('Page mounted')
  addLog(`Store token: ${authStore.token ? 'EXISTS' : 'NULL'}`)
  addLog(`Store user: ${authStore.user?.first_name || 'NULL'}`)
  addLog(`isAuthenticated: ${authStore.isAuthenticated}`)
})
</script>
