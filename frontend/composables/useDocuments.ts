import { ref } from 'vue'

export interface Document {
  id: number
  property_id?: number
  user_id: number
  file_name: string
  file_path: string
  file_size: number
  file_type: string
  document_type: 'lease' | 'contract' | 'invoice' | 'receipt' | 'identity' | 'other'
  uploaded_by: string
  created_at: string
  updated_at: string
}

export interface DocumentFilters {
  document_type?: string
  property_id?: number
  page?: number
  per_page?: number
}

export const useDocuments = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchDocuments = async (filters: DocumentFilters = {}) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams()
      if (filters.document_type) params.append('filter[document_type]', filters.document_type)
      if (filters.property_id) params.append('filter[property_id]', filters.property_id.toString())
      if (filters.page) params.append('page', filters.page.toString())
      if (filters.per_page) params.append('per_page', filters.per_page.toString())

      const response = await fetch(
        `${config.public.apiBase}/documents?${params.toString()}`,
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch documents')
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

  const uploadDocument = async (formData: FormData) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/documents`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
        body: formData,
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to upload document')
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

  const deleteDocument = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBase}/documents/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to delete document')
      }

      return await response.json()
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const downloadDocument = async (id: number) => {
    try {
      const response = await fetch(`${config.public.apiBase}/documents/${id}/download`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      if (!response.ok) {
        throw new Error('Failed to download document')
      }

      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'document' // Backend should provide filename in Content-Disposition header
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (err: any) {
      error.value = err.message
      throw err
    }
  }

  return {
    loading,
    error,
    fetchDocuments,
    uploadDocument,
    deleteDocument,
    downloadDocument,
  }
}
