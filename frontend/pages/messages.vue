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
              {{ conversation.name.charAt(0) }}
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between mb-1">
                <h3 class="font-semibold truncate">{{ conversation.name }}</h3>
                <span class="text-xs text-muted-foreground whitespace-nowrap ml-2">
                  {{ conversation.time }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground line-clamp-1">{{ conversation.lastMessage }}</p>
              <div class="flex items-center gap-2 mt-1">
                <Badge v-if="conversation.unread" variant="destructive" class="text-xs px-1.5 py-0">
                  {{ conversation.unread }}
                </Badge>
                <span
                  v-if="conversation.online"
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
                {{ selectedConversation.name.charAt(0) }}
              </div>
              <div>
                <h3 class="font-semibold">{{ selectedConversation.name }}</h3>
                <p class="text-xs text-muted-foreground">
                  {{ selectedConversation.online ? translations.online : translations.offline }}
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
              message.sender === 'me' ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-[70%] rounded-lg p-3',
                message.sender === 'me'
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-muted'
              ]"
            >
              <p class="text-sm">{{ message.text }}</p>
              <p
                :class="[
                  'text-xs mt-1',
                  message.sender === 'me' ? 'text-primary-foreground/70' : 'text-muted-foreground'
                ]"
              >
                {{ message.time }}
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
              <Label for="recipient">Recipient</Label>
              <Select id="recipient" v-model="newMessageForm.recipient" required>
                <option value="">Select recipient...</option>
                <option value="landlord">Landlord - Maria Rossi</option>
                <option value="tenant">Tenant - John Doe</option>
                <option value="ho">Housing Office</option>
              </Select>
            </div>

            <div class="space-y-2">
              <Label for="message">Message</Label>
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
import { ref, computed, onMounted } from 'vue'
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

const { t } = useI18n()

// Translations
const translations = ref({
  title: '',
  newMessage: '',
  searchConversations: '',
  typeMessage: '',
  send: '',
  sending: '',
  noConversations: '',
  noConversationsDesc: '',
  noMessagesSelected: '',
  noMessagesSelectedDesc: '',
  yesterday: '',
  today: '',
  online: '',
  offline: '',
})

onMounted(() => {
  translations.value = {
    title: t('messages.title'),
    newMessage: t('messages.newMessage'),
    searchConversations: t('messages.searchConversations'),
    typeMessage: t('messages.typeMessage'),
    send: t('messages.send'),
    sending: t('messages.sending'),
    noConversations: t('messages.noConversations'),
    noConversationsDesc: t('messages.noConversationsDesc'),
    noMessagesSelected: t('messages.noMessagesSelected'),
    noMessagesSelectedDesc: t('messages.noMessagesSelectedDesc'),
    yesterday: t('messages.yesterday'),
    today: t('messages.today'),
    online: t('messages.online'),
    offline: t('messages.offline'),
  }
})

// State
const searchQuery = ref('')
const selectedConversation = ref<any>(null)
const messageText = ref('')
const sending = ref(false)
const showNewMessageModal = ref(false)

const newMessageForm = ref({
  recipient: '',
  message: '',
})

// Mock conversations
const conversations = ref([
  {
    id: 1,
    name: 'Maria Rossi',
    lastMessage: 'The property is available for viewing next week.',
    time: '10:30 AM',
    unread: 2,
    online: true,
    messages: [
      {
        id: 1,
        sender: 'them',
        text: 'Hello, I saw your viewing request for Via Roma 123.',
        time: '10:15 AM',
      },
      {
        id: 2,
        sender: 'me',
        text: 'Yes, I\'m very interested! When can I schedule a viewing?',
        time: '10:20 AM',
      },
      {
        id: 3,
        sender: 'them',
        text: 'The property is available for viewing next week.',
        time: '10:30 AM',
      },
    ],
  },
  {
    id: 2,
    name: 'Housing Office',
    lastMessage: 'Your lease renewal documents are ready.',
    time: 'Yesterday',
    unread: 0,
    online: false,
    messages: [
      {
        id: 1,
        sender: 'them',
        text: 'Your lease renewal documents are ready.',
        time: 'Yesterday 3:00 PM',
      },
      {
        id: 2,
        sender: 'me',
        text: 'Thank you! I\'ll review them today.',
        time: 'Yesterday 3:15 PM',
      },
    ],
  },
  {
    id: 3,
    name: 'John Smith',
    lastMessage: 'Thanks for fixing the heating issue!',
    time: '2 days ago',
    unread: 0,
    online: false,
    messages: [
      {
        id: 1,
        sender: 'them',
        text: 'The heating in my apartment is not working.',
        time: '3 days ago',
      },
      {
        id: 2,
        sender: 'me',
        text: 'I\'ll send a technician tomorrow morning.',
        time: '3 days ago',
      },
      {
        id: 3,
        sender: 'them',
        text: 'Thanks for fixing the heating issue!',
        time: '2 days ago',
      },
    ],
  },
])

// Computed
const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value

  const query = searchQuery.value.toLowerCase()
  return conversations.value.filter(c =>
    c.name.toLowerCase().includes(query) ||
    c.lastMessage.toLowerCase().includes(query)
  )
})

// Methods
const handleSendMessage = async () => {
  if (!messageText.value.trim() || !selectedConversation.value) return

  sending.value = true
  try {
    await new Promise((resolve) => setTimeout(resolve, 500))

    // Add message to conversation
    selectedConversation.value.messages.push({
      id: selectedConversation.value.messages.length + 1,
      sender: 'me',
      text: messageText.value,
      time: 'Just now',
    })

    // Update last message in conversation list
    const conversation = conversations.value.find(c => c.id === selectedConversation.value.id)
    if (conversation) {
      conversation.lastMessage = messageText.value
      conversation.time = 'Just now'
    }

    messageText.value = ''
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    sending.value = false
  }
}

const handleNewMessage = async () => {
  sending.value = true
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Create new conversation
    const newConversation = {
      id: conversations.value.length + 1,
      name: newMessageForm.value.recipient === 'landlord' ? 'Maria Rossi'
        : newMessageForm.value.recipient === 'tenant' ? 'John Doe'
        : 'Housing Office',
      lastMessage: newMessageForm.value.message,
      time: 'Just now',
      unread: 0,
      online: false,
      messages: [
        {
          id: 1,
          sender: 'me',
          text: newMessageForm.value.message,
          time: 'Just now',
        },
      ],
    }

    conversations.value.unshift(newConversation)
    selectedConversation.value = newConversation

    // Reset form
    newMessageForm.value = {
      recipient: '',
      message: '',
    }

    showNewMessageModal.value = false
    alert('Message sent successfully')
  } catch (error) {
    console.error('Error sending new message:', error)
  } finally {
    sending.value = false
  }
}
</script>
