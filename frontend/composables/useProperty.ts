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

  // Google Maps Integration
  google_place_id?: string
  latitude?: number
  longitude?: number
  distance_from_base_km?: number
  formatted_address?: string

  // Cadastral Data
  cadastral_sheet_number?: string
  cadastral_plot_number?: string
  cadastral_unit_number?: string
  cadastral_tax_evaluation?: number
  cadastral_category?: string

  // Rooms
  living_rooms?: number
  dining_rooms?: number
  bedrooms: number
  bathrooms: number // Computed from full + half
  full_bathrooms: number
  half_bathrooms: number
  kitchen?: number
  basement: boolean
  attic: boolean
  garage: boolean
  yard: boolean

  // Furnishing
  furnishing_status: 'unfurnished' | 'partially_furnished' | 'fully_furnished'

  // Pets
  pets_allowed: boolean
  pets_notes?: string

  // Heating & Cooling
  heating_type?: 'city_gas' | 'lpg_with_coupons' | 'lpg_without_coupons' | 'fuel_oil' | 'electric' | 'heat_pump' | 'wood' | 'other'
  heating_system?: 'centralized' | 'autonomous' | 'shared_with_us' | 'shared_with_italians'
  has_heat_meter: boolean
  heating_notes?: string
  cooling_type?: string

  // Redecoration
  redecoration_fees_required: boolean
  redecoration_fees_amount?: number
  redecoration_date?: string

  // Additional Details
  floor_number?: number
  total_floors?: number
  elevator: boolean
  balcony: boolean
  terrace: boolean
  total_sqm?: number
  energy_class?: 'A4' | 'A3' | 'A2' | 'A1' | 'A' | 'B' | 'C' | 'D' | 'E' | 'F' | 'G'
  year_built?: number

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
