import { ref } from 'vue'

export interface MaintenanceTicket {
  id: number
  property_id?: number
  user_id: number
  subject: string
  description: string
  priority: 'low' | 'medium' | 'high' | 'urgent'
  category: 'plumbing' | 'electrical' | 'hvac' | 'appliances' | 'structural' | 'other'
  status: 'open' | 'inProgress' | 'resolved' | 'closed'
  created_by: string
  assigned_to?: string
  resolution_notes?: string
  created_at: string
  updated_at: string
}

export interface TicketFilters {
  status?: string
  priority?: string
  category?: string
  property_id?: number
  page?: number
  per_page?: number
}

export const useMaintenance = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchTickets = async (filters: TicketFilters = {}) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams()
      if (filters.status) params.append('filter[status]', filters.status)
      if (filters.priority) params.append('filter[priority]', filters.priority)
      if (filters.category) params.append('filter[category]', filters.category)
      if (filters.property_id) params.append('filter[property_id]', filters.property_id.toString())
      if (filters.page) params.append('page', filters.page.toString())
      if (filters.per_page) params.append('per_page', filters.per_page.toString())

      const response = await fetch(
        `${config.public.apiBase}/maintenance-tickets?${params.toString()}`,
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch tickets')
      }

      const data = await response.json()
      return data
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchTicket = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/maintenance-tickets/${id}`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch ticket')
      }

      const data = await response.json()
      return data
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const createTicket = async (ticketData: Partial<MaintenanceTicket>) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/maintenance-tickets`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(ticketData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to create ticket')
      }

      const data = await response.json()
      return data
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateTicket = async (id: number, updates: Partial<MaintenanceTicket>) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/maintenance-tickets/${id}`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(updates),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update ticket')
      }

      const data = await response.json()
      return data
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const closeTicket = async (id: number, resolutionNotes?: string) => {
    return updateTicket(id, {
      status: 'closed',
      resolution_notes: resolutionNotes,
    })
  }

  const reopenTicket = async (id: number) => {
    return updateTicket(id, { status: 'open' })
  }

  const deleteTicket = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/maintenance-tickets/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to delete ticket')
      }

      return await response.json()
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
    fetchTickets,
    fetchTicket,
    createTicket,
    updateTicket,
    closeTicket,
    reopenTicket,
    deleteTicket,
  }
}
