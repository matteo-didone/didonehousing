import { ref } from 'vue'

export interface HOStats {
  pendingApprovals: number
  totalProperties: number
  activeTenants: number
  openTickets: number
}

export const useHODashboard = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchStats = async (): Promise<HOStats> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/ho/dashboard/stats`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch HO stats')
      }

      const data = await response.json()
      return data.stats || data
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    fetchStats,
  }
}
