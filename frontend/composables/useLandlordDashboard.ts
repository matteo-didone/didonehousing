import { ref } from 'vue'

export interface LandlordStats {
  totalProperties: number
  activeListings: number
  pendingReview: number
  monthlyRevenue: number
  propertyStatusDistribution?: {
    available: number
    pending: number
    rented: number
  }
  revenueComparison?: {
    currentMonth: number
    previousMonth: number
    percentageChange: number
  }
}

export interface RecentActivity {
  id: number
  title: string
  description: string
  time: string
  status: string
  statusText: string
  created_at: string
}

export const useLandlordDashboard = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchStats = async (): Promise<LandlordStats> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/landlord/dashboard/stats`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch landlord stats')
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

  const fetchRecentActivity = async (): Promise<RecentActivity[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(
        `${config.public.apiBase}/landlord/dashboard/recent-activity`,
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch recent activity')
      }

      const data = await response.json()
      return data.activities || data.data || []
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
    fetchRecentActivity,
  }
}
