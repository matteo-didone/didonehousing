import { ref } from 'vue'

export interface Message {
  id: number
  conversation_id: number
  sender_id: number
  sender_name: string
  content: string
  read: boolean
  created_at: string
  updated_at: string
}

export interface Conversation {
  id: number
  participant_id: number
  participant_name: string
  participant_role: string
  last_message: string
  last_message_at: string
  unread_count: number
  is_online: boolean
  messages?: Message[]
}

export interface ConversationFilters {
  unread?: boolean
  page?: number
  per_page?: number
}

export const useMessages = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchConversations = async (filters: ConversationFilters = {}) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams()
      if (filters.unread !== undefined) params.append('filter[unread]', filters.unread.toString())
      if (filters.page) params.append('page', filters.page.toString())
      if (filters.per_page) params.append('per_page', filters.per_page.toString())

      const response = await fetch(
        `${config.public.apiBaseUrl}/api/conversations?${params.toString()}`,
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch conversations')
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

  const fetchConversationMessages = async (conversationId: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(
        `${config.public.apiBaseUrl}/api/conversations/${conversationId}/messages`,
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to fetch messages')
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

  const sendMessage = async (conversationId: number, content: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(
        `${config.public.apiBaseUrl}/api/conversations/${conversationId}/messages`,
        {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token.value}`,
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify({ content }),
        }
      )

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to send message')
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

  const createConversation = async (participantId: number, initialMessage: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${config.public.apiBaseUrl}/api/conversations`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          participant_id: participantId,
          message: initialMessage,
        }),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to create conversation')
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

  const markAsRead = async (conversationId: number) => {
    try {
      const response = await fetch(
        `${config.public.apiBaseUrl}/api/conversations/${conversationId}/mark-read`,
        {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: 'application/json',
          },
        }
      )

      if (!response.ok) {
        throw new Error('Failed to mark conversation as read')
      }

      return await response.json()
    } catch (err: any) {
      console.error('Error marking conversation as read:', err)
    }
  }

  return {
    loading,
    error,
    fetchConversations,
    fetchConversationMessages,
    sendMessage,
    createConversation,
    markAsRead,
  }
}
