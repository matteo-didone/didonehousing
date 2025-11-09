<template>
  <div class="h-[calc(100vh-8rem)] flex gap-6">
    <!-- Conversations List -->
    <div class="w-full lg:w-96 flex flex-col border border-border rounded-lg bg-card overflow-hidden">
      <!-- Header -->
      <div class="p-4 border-b border-border">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold">{{ translations.title }}</h2>
          <Button size="sm" @click="showNewMessageModal = true">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            {{ translations.newMessage }}
          </Button>
        </div>
        <Input
          v-model="searchQuery"
          :placeholder="translations.searchConversations"
          class="w-full"
        >
          <template #prefix>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </template>
        </Input>
      </div>

      <!-- Conversations -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="filteredConversations.length > 0">
          <div
            v-for="conversation in filteredConversations"
            :key="conversation.id"
            @click="selectedConversation = conversation"
            :class="[
              'flex items-start gap-3 p-4 cursor-pointer transition-colors border-b border-border',
              selectedConversation?.id === conversation.id
                ? 'bg-primary/10'
                : 'hover:bg-muted/50'
            ]"
          >
            <!-- Avatar -->
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold">
              {{ conversation.participant_name.charAt(0) }}
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between mb-1">
                <h3 class="font-semibold truncate">{{ conversation.participant_name }}</h3>
                <span class="text-xs text-muted-foreground whitespace-nowrap ml-2">
                  {{ formatTime(conversation.last_message_at) }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground line-clamp-1">{{ conversation.last_message }}</p>
              <div class="flex items-center gap-2 mt-1">
                <Badge v-if="conversation.unread_count > 0" variant="destructive" class="text-xs px-1.5 py-0">
                  {{ conversation.unread_count }}
                </Badge>
                <span
                  v-if="conversation.is_online"
                  class="text-xs text-success flex items-center gap-1"
                >
                  <span class="h-2 w-2 rounded-full bg-success"></span>
                  {{ translations.online }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center h-full p-8 text-center">
          <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
          </div>
          <h3 class="font-semibold">{{ translations.noConversations }}</h3>
          <p class="text-sm text-muted-foreground mt-2">{{ translations.noConversationsDesc }}</p>
        </div>
      </div>
    </div>

    <!-- Message Thread -->
    <div class="flex-1 hidden lg:flex flex-col border border-border rounded-lg bg-card overflow-hidden">
      <div v-if="selectedConversation">
        <!-- Thread Header -->
        <div class="p-4 border-b border-border">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold">
                {{ selectedConversation.participant_name.charAt(0) }}
              </div>
              <div>
                <h3 class="font-semibold">{{ selectedConversation.participant_name }}</h3>
                <p class="text-xs text-muted-foreground">
                  {{ selectedConversation.is_online ? translations.online : translations.offline }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <Button size="sm" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="1" />
                  <circle cx="12" cy="5" r="1" />
                  <circle cx="12" cy="19" r="1" />
                </svg>
              </Button>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div
            v-for="message in selectedConversation.messages"
            :key="message.id"
            :class="[
              'flex',
              message.sender_id === user?.id ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-[70%] rounded-lg p-3',
                message.sender_id === user?.id
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-muted'
              ]"
            >
              <p class="text-sm">{{ message.content }}</p>
              <p
                :class="[
                  'text-xs mt-1',
                  message.sender_id === user?.id ? 'text-primary-foreground/70' : 'text-muted-foreground'
                ]"
              >
                {{ formatTime(message.created_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Send Message Form -->
        <div class="p-4 border-t border-border">
          <form @submit.prevent="handleSendMessage" class="flex gap-2">
            <Input
              v-model="messageText"
              :placeholder="translations.typeMessage"
              class="flex-1"
              :disabled="sending"
            />
            <Button type="submit" :disabled="!messageText.trim() || sending">
              <svg v-if="!sending" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m22 2-7 20-4-9-9-4Z" />
                <path d="M22 2 11 13" />
              </svg>
              <span v-if="sending">{{ translations.sending }}</span>
              <span v-else>{{ translations.send }}</span>
            </Button>
          </form>
        </div>
      </div>

      <!-- No Conversation Selected -->
      <div v-else class="flex flex-col items-center justify-center h-full p-8 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-muted mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold">{{ translations.noMessagesSelected }}</h3>
        <p class="text-sm text-muted-foreground mt-2">{{ translations.noMessagesSelectedDesc }}</p>
      </div>
    </div>

    <!-- New Message Modal -->
    <div
      v-if="showNewMessageModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showNewMessageModal = false"
    >
      <Card class="w-full max-w-md">
        <CardHeader>
          <CardTitle>{{ translations.newMessage }}</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleNewMessage" class="space-y-4">
            <div class="space-y-2">
              <Label for="recipient">{{ translations.recipient }}</Label>
              <Select id="recipient" v-model="newMessageForm.recipient" required>
                <option value="">{{ translations.selectRecipient }}</option>
                <option value="landlord">{{ translations.landlord }} - Maria Rossi</option>
                <option value="tenant">{{ translations.tenant }} - John Doe</option>
                <option value="ho">{{ translations.housingOffice }}</option>
              </Select>
            </div>

            <div class="space-y-2">
              <Label for="message">{{ translations.message }}</Label>
              <Textarea
                id="message"
                v-model="newMessageForm.message"
                placeholder="Type your message..."
                rows="4"
                required
              />
            </div>

            <div class="flex gap-3 justify-end">
              <Button type="button" variant="outline" @click="showNewMessageModal = false">
                Cancel
              </Button>
              <Button type="submit" :disabled="sending">
                <span v-if="sending">{{ translations.sending }}</span>
                <span v-else>{{ translations.send }}</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'
import Textarea from '@/components/ui/Textarea.vue'
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
})

const { t, locale } = useI18n()
const { fetchConversations, fetchConversationMessages, sendMessage, createConversation, markAsRead, loading: apiLoading } = useMessages()

// Translations
const translations = ref({
  title: '',
  newMessage: '',
  searchConversations: '',
  typeMessage: '',
  send: '',
  sending: '',
  recipient: '',
  message: '',
  selectRecipient: '',
  landlord: '',
  tenant: '',
  housingOffice: '',
  noConversations: '',
  noConversationsDesc: '',
  noMessagesSelected: '',
  noMessagesSelectedDesc: '',
  yesterday: '',
  today: '',
  online: '',
  offline: '',
})

// Load translations function
const loadTranslations = () => {
  translations.value = {
    title: t('messages.title'),
    newMessage: t('messages.newMessage'),
    searchConversations: t('messages.searchConversations'),
    typeMessage: t('messages.typeMessage'),
    send: t('messages.send'),
    sending: t('messages.sending'),
    recipient: t('messages.recipient'),
    message: t('messages.message'),
    selectRecipient: t('messages.selectRecipient'),
    landlord: t('messages.landlord'),
    tenant: t('messages.tenant'),
    housingOffice: t('messages.housingOffice'),
    noConversations: t('messages.noConversations'),
    noConversationsDesc: t('messages.noConversationsDesc'),
    noMessagesSelected: t('messages.noMessagesSelected'),
    noMessagesSelectedDesc: t('messages.noMessagesSelectedDesc'),
    yesterday: t('messages.yesterday'),
    today: t('messages.today'),
    online: t('messages.online'),
    offline: t('messages.offline'),
  }
}

// Watch for locale changes and reload translations
watch(locale, () => {
  loadTranslations()
})

// State
const searchQuery = ref('')
const selectedConversation = ref<any>(null)
const messageText = ref('')
const sending = ref(false)
const showNewMessageModal = ref(false)
const conversations = ref<any[]>([])
const loadingConversations = ref(false)
const { user } = useAuth()

const newMessageForm = ref({
  recipient: '',
  message: '',
})

// Load conversations
const loadConversations = async () => {
  loadingConversations.value = true
  try {
    const response = await fetchConversations({ per_page: 50 })
    conversations.value = response.data || response.conversations || []
  } catch (err: any) {
    console.error('Error loading conversations:', err)
  } finally {
    loadingConversations.value = false
  }
}

// Load messages for a conversation
const loadConversationMessages = async (conversationId: number) => {
  try {
    const response = await fetchConversationMessages(conversationId)
    if (selectedConversation.value && selectedConversation.value.id === conversationId) {
      selectedConversation.value.messages = response.data || response.messages || []
    }
    // Mark as read
    await markAsRead(conversationId)
  } catch (err: any) {
    console.error('Error loading messages:', err)
  }
}

// Watch for conversation selection
watch(selectedConversation, (newVal) => {
  if (newVal && newVal.id) {
    loadConversationMessages(newVal.id)
  }
})

// Computed
const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value

  const query = searchQuery.value.toLowerCase()
  return conversations.value.filter(c =>
    c.participant_name.toLowerCase().includes(query) ||
    c.last_message.toLowerCase().includes(query)
  )
})

// Methods
const handleSendMessage = async () => {
  if (!messageText.value.trim() || !selectedConversation.value) return

  sending.value = true
  try {
    const response = await sendMessage(selectedConversation.value.id, messageText.value)

    // Add message to local array
    if (selectedConversation.value.messages) {
      selectedConversation.value.messages.push(response.message)
    } else {
      selectedConversation.value.messages = [response.message]
    }

    // Update last message in conversation list
    const conversation = conversations.value.find(c => c.id === selectedConversation.value.id)
    if (conversation) {
      conversation.last_message = messageText.value
      conversation.last_message_at = response.message.created_at
    }

    messageText.value = ''
  } catch (error: any) {
    console.error('Error sending message:', error)
    alert(error.message || 'Error sending message')
  } finally {
    sending.value = false
  }
}

const handleNewMessage = async () => {
  if (!newMessageForm.value.recipient || !newMessageForm.value.message.trim()) {
    alert('Please select a recipient and enter a message')
    return
  }

  sending.value = true
  try {
    const participantId = parseInt(newMessageForm.value.recipient)
    const response = await createConversation(participantId, newMessageForm.value.message)

    // Reload conversations
    await loadConversations()

    // Select the new conversation
    selectedConversation.value = response.conversation

    // Reset form
    newMessageForm.value = {
      recipient: '',
      message: '',
    }

    showNewMessageModal.value = false
    alert('Message sent successfully')
  } catch (error: any) {
    console.error('Error sending new message:', error)
    alert(error.message || 'Error sending new message')
  } finally {
    sending.value = false
  }
}

// Helper function to format time
const formatTime = (dateString: string): string => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = (now.getTime() - date.getTime()) / (1000 * 60 * 60)

  if (diffInHours < 24) {
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
  } else if (diffInHours < 48) {
    return 'Yesterday'
  } else {
    return Math.floor(diffInHours / 24) + ' days ago'
  }
}

// Load conversations on mount
onMounted(() => {
  loadTranslations()
  loadConversations()
})
</script>
