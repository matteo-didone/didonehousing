import type { Ref } from 'vue'

export interface Property {
  id: number
  landlord_id: number
  status: 'draft' | 'pending_review' | 'approved' | 'rejected'
  // Address
  street_name: string
  house_number: string
  apt_number?: string
  city: string
  province: string
  postal_code: string
  country: string
  // Rooms
  living_rooms?: number
  dining_rooms?: number
  bedrooms: number
  bathrooms: number
  kitchen?: number
  basement: boolean
  attic: boolean
  garage: boolean
  yard: boolean
  // Details
  furnished: boolean
  pets_allowed: boolean
  heating_type?: string
  // Listing info (if has active listing)
  listing?: {
    id: number
    monthly_rent: number
    currency: string
    available_from: string
    is_active: boolean
  }
  // Timestamps
  created_at: string
  updated_at: string
  submitted_at?: string
  reviewed_at?: string
  // Reviewer info
  ho_reviewer?: {
    id: number
    first_name: string
    last_name: string
  }
  rejection_reason?: string
}

export interface PropertyFilters {
  status?: string
  city?: string
  province?: string
  landlord_id?: number
  pending_review?: boolean
  approved?: boolean
  per_page?: number
  page?: number
  sort?: string
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export const useProperty = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  /**
   * Fetch properties with filters
   */
  const fetchProperties = async (filters: PropertyFilters = {}): Promise<PaginatedResponse<Property>> => {
    const params = new URLSearchParams()

    // Add filters
    if (filters.status) params.append('filter[status]', filters.status)
    if (filters.city) params.append('filter[city]', filters.city)
    if (filters.province) params.append('filter[province]', filters.province)
    if (filters.landlord_id) params.append('filter[landlord_id]', filters.landlord_id.toString())
    if (filters.pending_review) params.append('filter[pending_review]', 'true')
    if (filters.approved) params.append('filter[approved]', 'true')
    if (filters.per_page) params.append('per_page', filters.per_page.toString())
    if (filters.page) params.append('page', filters.page.toString())
    if (filters.sort) params.append('sort', filters.sort)

    const queryString = params.toString()
    const url = `/properties${queryString ? `?${queryString}` : ''}`

    const response = await $fetch<PaginatedResponse<Property>>(url, {
      baseURL: config.public.apiBase,
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    return response
  }

  /**
   * Fetch single property by ID
   */
  const fetchProperty = async (id: number): Promise<Property> => {
    const response = await $fetch<Property>(`/properties/${id}`, {
      baseURL: config.public.apiBase,
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    return response
  }

  /**
   * Create new property
   */
  const createProperty = async (data: Partial<Property>): Promise<{ property: Property; message: string }> => {
    const response = await $fetch<{ property: Property; message: string }>('/properties', {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: data,
    })

    return response
  }

  /**
   * Update property
   */
  const updateProperty = async (id: number, data: Partial<Property>): Promise<{ property: Property; message: string }> => {
    const response = await $fetch<{ property: Property; message: string }>(`/properties/${id}`, {
      baseURL: config.public.apiBase,
      method: 'PATCH',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: data,
    })

    return response
  }

  /**
   * Delete property
   */
  const deleteProperty = async (id: number): Promise<{ message: string }> => {
    const response = await $fetch<{ message: string }>(`/properties/${id}`, {
      baseURL: config.public.apiBase,
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    return response
  }

  /**
   * Submit property for HO review
   */
  const submitForReview = async (id: number): Promise<{ property: Property; message: string }> => {
    const response = await $fetch<{ property: Property; message: string }>(`/properties/${id}/submit`, {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    return response
  }

  /**
   * Approve property (HO only)
   */
  const approveProperty = async (id: number, notes?: string): Promise<{ property: Property; message: string }> => {
    const response = await $fetch<{ property: Property; message: string }>(`/properties/${id}/approve`, {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: { notes },
    })

    return response
  }

  /**
   * Reject property (HO only)
   */
  const rejectProperty = async (id: number, reason: string): Promise<{ property: Property; message: string }> => {
    const response = await $fetch<{ property: Property; message: string }>(`/properties/${id}/reject`, {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: { reason },
    })

    return response
  }

  return {
    fetchProperties,
    fetchProperty,
    createProperty,
    updateProperty,
    deleteProperty,
    submitForReview,
    approveProperty,
    rejectProperty,
  }
}
