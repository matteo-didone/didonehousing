<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">{{ t('maintenance.title') }}</h1>
      </div>
      <Button @click="showCreateModal = true" size="lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
        {{ t('maintenance.createTicket') }}
      </Button>
    </div>

    <!-- Filter -->
    <div class="flex gap-3">
      <Select v-model="filterStatus" class="w-[200px]">
        <option value="">{{ t('maintenance.allStatuses') }}</option>
        <option value="open">{{ t('maintenance.statuses.open') }}</option>
        <option value="inProgress">{{ t('maintenance.statuses.inProgress') }}</option>
        <option value="resolved">{{ t('maintenance.statuses.resolved') }}</option>
        <option value="closed">{{ t('maintenance.statuses.closed') }}</option>
      </Select>
    </div>

    <!-- Tickets List -->
    <div v-if="filteredTickets.length > 0" class="grid grid-cols-1 gap-4">
      <Card
        v-for="ticket in filteredTickets"
        :key="ticket.id"
        class="cursor-pointer transition-all hover:shadow-md"
        @click="selectedTicket = ticket"
      >
        <CardContent class="p-6">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="text-lg font-semibold">{{ ticket.subject }}</h3>
                <Badge :variant="getStatusVariant(ticket.status)">
                  {{ t(`maintenance.statuses.${ticket.status}`) }}
                </Badge>
                <Badge :variant="getPriorityVariant(ticket.priority)">
                  {{ t(`maintenance.priorities.${ticket.priority}`) }}
                </Badge>
              </div>
              <p class="text-sm text-muted-foreground line-clamp-2">{{ ticket.description }}</p>
              <div class="mt-3 flex items-center gap-4 text-xs text-muted-foreground">
                <span>{{ t('maintenance.category') }}: {{ t(`maintenance.categories.${ticket.category}`) }}</span>
                <span>{{ t('maintenance.createdAt') }}: {{ formatDate(ticket.created_at) }}</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State -->
    <Card v-else>
      <CardContent class="py-12 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-muted mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold">{{ t('maintenance.noTickets') }}</h3>
        <p class="mt-2 text-sm text-muted-foreground">{{ t('maintenance.noTicketsDesc') }}</p>
        <Button @click="showCreateModal = true" class="mt-4">
          {{ t('maintenance.createTicket') }}
        </Button>
      </CardContent>
    </Card>

    <!-- Create Ticket Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showCreateModal = false"
    >
      <Card class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <CardHeader>
          <CardTitle>{{ t('maintenance.createTicket') }}</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleCreateTicket" class="space-y-4">
            <div class="space-y-2">
              <Label for="subject">{{ t('maintenance.subject') }}</Label>
              <Input
                id="subject"
                v-model="ticketForm.subject"
                :placeholder="t('maintenance.subjectPlaceholder')"
                required
              />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div class="space-y-2">
                <Label for="priority">{{ t('maintenance.priority') }}</Label>
                <Select id="priority" v-model="ticketForm.priority" required>
                  <option value="low">{{ t('maintenance.priorities.low') }}</option>
                  <option value="medium">{{ t('maintenance.priorities.medium') }}</option>
                  <option value="high">{{ t('maintenance.priorities.high') }}</option>
                  <option value="urgent">{{ t('maintenance.priorities.urgent') }}</option>
                </Select>
              </div>

              <div class="space-y-2">
                <Label for="category">{{ t('maintenance.category') }}</Label>
                <Select id="category" v-model="ticketForm.category" required>
                  <option value="plumbing">{{ t('maintenance.categories.plumbing') }}</option>
                  <option value="electrical">{{ t('maintenance.categories.electrical') }}</option>
                  <option value="hvac">{{ t('maintenance.categories.hvac') }}</option>
                  <option value="appliances">{{ t('maintenance.categories.appliances') }}</option>
                  <option value="structural">{{ t('maintenance.categories.structural') }}</option>
                  <option value="other">{{ t('maintenance.categories.other') }}</option>
                </Select>
              </div>

              <div class="space-y-2">
                <Label for="status">{{ t('maintenance.status') }}</Label>
                <Select id="status" v-model="ticketForm.status" required>
                  <option value="open">{{ t('maintenance.statuses.open') }}</option>
                </Select>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="description">{{ t('maintenance.description') }}</Label>
              <Textarea
                id="description"
                v-model="ticketForm.description"
                :placeholder="t('maintenance.descriptionPlaceholder')"
                rows="6"
                required
              />
            </div>

            <div class="flex gap-3 justify-end">
              <Button type="button" variant="outline" @click="showCreateModal = false">
                {{ t('common.cancel') }}
              </Button>
              <Button type="submit" :disabled="submitting">
                <span v-if="submitting">{{ t('maintenance.submitting') }}</span>
                <span v-else>{{ t('maintenance.submit') }}</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>

    <!-- Ticket Details Modal -->
    <div
      v-if="selectedTicket"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="selectedTicket = null"
    >
      <Card class="w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <CardHeader>
          <div class="flex items-start justify-between">
            <div>
              <CardTitle>{{ selectedTicket.subject }}</CardTitle>
              <div class="mt-2 flex items-center gap-2">
                <Badge :variant="getStatusVariant(selectedTicket.status)">
                  {{ t(`maintenance.statuses.${selectedTicket.status}`) }}
                </Badge>
                <Badge :variant="getPriorityVariant(selectedTicket.priority)">
                  {{ t(`maintenance.priorities.${selectedTicket.priority}`) }}
                </Badge>
              </div>
            </div>
            <button @click="selectedTicket = null" class="text-muted-foreground hover:text-foreground">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
            </button>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <div>
            <p class="text-sm font-medium text-muted-foreground">{{ t('maintenance.category') }}</p>
            <p>{{ t(`maintenance.categories.${selectedTicket.category}`) }}</p>
          </div>

          <div>
            <p class="text-sm font-medium text-muted-foreground">{{ t('maintenance.description') }}</p>
            <p>{{ selectedTicket.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="font-medium text-muted-foreground">{{ t('maintenance.createdBy') }}</p>
              <p>{{ selectedTicket.created_by }}</p>
            </div>
            <div>
              <p class="font-medium text-muted-foreground">{{ t('maintenance.createdAt') }}</p>
              <p>{{ formatDate(selectedTicket.created_at) }}</p>
            </div>
          </div>

          <div class="flex gap-2 pt-4">
            <Button variant="outline">{{ t('maintenance.update') }}</Button>
            <Button v-if="selectedTicket.status !== 'closed'" variant="destructive" @click="handleCloseTicket(selectedTicket.id)">
              {{ t('maintenance.close') }}
            </Button>
            <Button v-else variant="outline" @click="handleReopenTicket(selectedTicket.id)">{{ t('maintenance.reopen') }}</Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
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
const { fetchTickets, createTicket, updateTicket, closeTicket, reopenTicket, loading: apiLoading } = useMaintenance()

// State
const showCreateModal = ref(false)
const selectedTicket = ref<any>(null)
const submitting = ref(false)
const filterStatus = ref('')

const ticketForm = ref({
  subject: '',
  description: '',
  priority: 'medium',
  category: 'other',
  status: 'open',
})

const tickets = ref<any[]>([])
const loadingTickets = ref(false)

// Load tickets from API
const loadTickets = async () => {
  loadingTickets.value = true
  try {
    const response = await fetchTickets({
      status: filterStatus.value || undefined,
      per_page: 50,
    })
    tickets.value = response.data || response.tickets || []
  } catch (err: any) {
    console.error('Error loading tickets:', err)
  } finally {
    loadingTickets.value = false
  }
}

// Watch filter changes
watch(filterStatus, () => {
  loadTickets()
})

// Computed
const filteredTickets = computed(() => {
  return tickets.value
})

// Methods
const handleCreateTicket = async () => {
  submitting.value = true
  try {
    await createTicket(ticketForm.value)

    // Reload tickets
    await loadTickets()

    // Reset form
    ticketForm.value = {
      subject: '',
      description: '',
      priority: 'medium',
      category: 'other',
      status: 'open',
    }

    showCreateModal.value = false
    alert('Ticket created successfully')
  } catch (error: any) {
    console.error('Error creating ticket:', error)
    alert(error.message || 'Error creating ticket')
  } finally {
    submitting.value = false
  }
}

const handleCloseTicket = async (id: number) => {
  if (!confirm('Are you sure you want to close this ticket?')) return

  try {
    await closeTicket(id)
    selectedTicket.value = null
    await loadTickets()
    alert('Ticket closed successfully')
  } catch (error: any) {
    console.error('Error closing ticket:', error)
    alert(error.message || 'Error closing ticket')
  }
}

const handleReopenTicket = async (id: number) => {
  try {
    await reopenTicket(id)
    selectedTicket.value = null
    await loadTickets()
    alert('Ticket reopened successfully')
  } catch (error: any) {
    console.error('Error reopening ticket:', error)
    alert(error.message || 'Error reopening ticket')
  }
}

// Load tickets on mount
onMounted(() => {
  loadTickets()
})

const getStatusVariant = (status: string) => {
  const variants: Record<string, any> = {
    open: 'default',
    inProgress: 'warning',
    resolved: 'success',
    closed: 'secondary',
  }
  return variants[status] || 'default'
}

const getPriorityVariant = (priority: string) => {
  const variants: Record<string, any> = {
    low: 'secondary',
    medium: 'default',
    high: 'warning',
    urgent: 'destructive',
  }
  return variants[priority] || 'default'
}

const formatDate = (dateString: string): string => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffInDays = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24))

  if (diffInDays === 0) {
    return 'Today'
  } else if (diffInDays === 1) {
    return 'Yesterday'
  } else if (diffInDays < 7) {
    return `${diffInDays} days ago`
  } else if (diffInDays < 30) {
    return `${Math.floor(diffInDays / 7)} weeks ago`
  } else {
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  }
}
</script>
