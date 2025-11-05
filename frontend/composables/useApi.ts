import type { UseFetchOptions } from 'nuxt/app'

export const useApi = () => {
  const config = useRuntimeConfig()

  // Base API URL from runtime config or default to localhost
  const baseURL = config.public.apiBase || 'http://localhost:8000/api'

  const fetchWithAuth = async <T>(
    url: string,
    options: UseFetchOptions<T> = {}
  ) => {
    // Get auth token from localStorage or cookie if needed
    // For now, we'll skip auth in the walking skeleton

    const defaultOptions: UseFetchOptions<T> = {
      baseURL,
      ...options,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers,
      },
    }

    return useFetch<T>(url, defaultOptions)
  }

  return {
    fetchWithAuth,
    baseURL,
  }
}
