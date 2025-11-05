import type { Property, PropertySearchFilters } from '~/types/property'

export const useProperties = () => {
  const { fetchWithAuth } = useApi()

  const getProperties = async (filters?: PropertySearchFilters) => {
    const queryParams = new URLSearchParams()

    if (filters) {
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          queryParams.append(key, String(value))
        }
      })
    }

    const url = `/properties${queryParams.toString() ? `?${queryParams.toString()}` : ''}`

    return fetchWithAuth<{ data: Property[], meta?: any }>(url)
  }

  const getProperty = async (id: number | string) => {
    return fetchWithAuth<{ data: Property }>(`/properties/${id}`)
  }

  const searchProperties = async (query: string, filters?: PropertySearchFilters) => {
    const params = {
      search: query,
      ...filters,
    }
    return getProperties(params)
  }

  return {
    getProperties,
    getProperty,
    searchProperties,
  }
}
