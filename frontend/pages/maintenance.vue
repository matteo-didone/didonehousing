<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
      </div>
      <Button @click="showCreateModal = true" size="lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
        {{ translations.createTicket }}
      </Button>
    </div>

    <!-- Filter -->
    <div class="flex gap-3">
      <Select v-model="filterStatus" class="w-[200px]">
        <option value="">{{ translations.allStatuses }}</option>
        <option value="open">{{ translations.statuses.open }}</option>
        <option value="inProgress">{{ translations.statuses.inProgress }}</option>
        <option value="resolved">{{ translations.statuses.resolved }}</option>
        <option value="closed">{{ translations.statuses.closed }}</option>
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
                  {{ translations.statuses[ticket.status] }}
                </Badge>
                <Badge :variant="getPriorityVariant(ticket.priority)">
                  {{ translations.priorities[ticket.priority] }}
                </Badge>
              </div>
              <p class="text-sm text-muted-foreground line-clamp-2">{{ ticket.description }}</p>
              <div class="mt-3 flex items-center gap-4 text-xs text-muted-foreground">
                <span>{{ translations.category }}: {{ translations.categories[ticket.category] }}</span>
                <span>{{ translations.createdAt }}: {{ ticket.createdAt }}</span>
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
        <h3 class="text-lg font-semibold">{{ translations.noTickets }}</h3>
        <p class="mt-2 text-sm text-muted-foreground">{{ translations.noTicketsDesc }}</p>
        <Button @click="showCreateModal = true" class="mt-4">
          {{ translations.createTicket }}
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
          <CardTitle>{{ translations.createTicket }}</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleCreateTicket" class="space-y-4">
            <div class="space-y-2">
              <Label for="subject">{{ translations.subject }}</Label>
              <Input
                id="subject"
                v-model="ticketForm.subject"
                :placeholder="translations.subjectPlaceholder"
                required
              />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div class="space-y-2">
                <Label for="priority">{{ translations.priority }}</Label>
                <Select id="priority" v-model="ticketForm.priority" required>
                  <option value="low">{{ translations.priorities.low }}</option>
                  <option value="medium">{{ translations.priorities.medium }}</option>
                  <option value="high">{{ translations.priorities.high }}</option>
                  <option value="urgent">{{ translations.priorities.urgent }}</option>
                </Select>
              </div>

              <div class="space-y-2">
                <Label for="category">{{ translations.category }}</Label>
                <Select id="category" v-model="ticketForm.category" required>
                  <option value="plumbing">{{ translations.categories.plumbing }}</option>
                  <option value="electrical">{{ translations.categories.electrical }}</option>
                  <option value="hvac">{{ translations.categories.hvac }}</option>
                  <option value="appliances">{{ translations.categories.appliances }}</option>
                  <option value="structural">{{ translations.categories.structural }}</option>
                  <option value="other">{{ translations.categories.other }}</option>
                </Select>
              </div>

              <div class="space-y-2">
                <Label for="status">{{ translations.status }}</Label>
                <Select id="status" v-model="ticketForm.status" required>
                  <option value="open">{{ translations.statuses.open }}</option>
                </Select>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="description">{{ translations.description }}</Label>
              <Textarea
                id="description"
                v-model="ticketForm.description"
                :placeholder="translations.descriptionPlaceholder"
                rows="6"
                required
              />
            </div>

            <div class="flex gap-3 justify-end">
              <Button type="button" variant="outline" @click="showCreateModal = false">
                Cancel
              </Button>
              <Button type="submit" :disabled="submitting">
                <span v-if="submitting">{{ translations.submitting }}</span>
                <span v-else>{{ translations.submit }}</span>
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
                  {{ translations.statuses[selectedTicket.status] }}
                </Badge>
                <Badge :variant="getPriorityVariant(selectedTicket.priority)">
                  {{ translations.priorities[selectedTicket.priority] }}
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
            <p class="text-sm font-medium text-muted-foreground">{{ translations.category }}</p>
            <p>{{ translations.categories[selectedTicket.category] }}</p>
          </div>

          <div>
            <p class="text-sm font-medium text-muted-foreground">{{ translations.description }}</p>
            <p>{{ selectedTicket.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="font-medium text-muted-foreground">{{ translations.createdBy }}</p>
              <p>{{ selectedTicket.createdBy }}</p>
            </div>
            <div>
              <p class="font-medium text-muted-foreground">{{ translations.createdAt }}</p>
              <p>{{ selectedTicket.createdAt }}</p>
            </div>
          </div>

          <div class="flex gap-2 pt-4">
            <Button variant="outline">{{ translations.update }}</Button>
            <Button v-if="selectedTicket.status !== 'closed'" variant="destructive">
              {{ translations.close }}
            </Button>
            <Button v-else variant="outline">{{ translations.reopen }}</Button>
          </div>
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
  createTicket: '',
  subject: '',
  subjectPlaceholder: '',
  description: '',
  descriptionPlaceholder: '',
  priority: '',
  category: '',
  status: '',
  submit: '',
  submitting: '',
  update: '',
  close: '',
  reopen: '',
  priorities: { low: '', medium: '', high: '', urgent: '' },
  categories: { plumbing: '', electrical: '', hvac: '', appliances: '', structural: '', other: '' },
  statuses: { open: '', inProgress: '', resolved: '', closed: '' },
  createdBy: '',
  createdAt: '',
  noTickets: '',
  noTicketsDesc: '',
  allStatuses: '',
})

onMounted(() => {
  translations.value = {
    title: t('maintenance.title'),
    createTicket: t('maintenance.createTicket'),
    subject: t('maintenance.subject'),
    subjectPlaceholder: t('maintenance.subjectPlaceholder'),
    description: t('maintenance.description'),
    descriptionPlaceholder: t('maintenance.descriptionPlaceholder'),
    priority: t('maintenance.priority'),
    category: t('maintenance.category'),
    status: t('maintenance.status'),
    submit: t('maintenance.submit'),
    submitting: t('maintenance.submitting'),
    update: t('maintenance.update'),
    close: t('maintenance.close'),
    reopen: t('maintenance.reopen'),
    priorities: {
      low: t('maintenance.priorities.low'),
      medium: t('maintenance.priorities.medium'),
      high: t('maintenance.priorities.high'),
      urgent: t('maintenance.priorities.urgent'),
    },
    categories: {
      plumbing: t('maintenance.categories.plumbing'),
      electrical: t('maintenance.categories.electrical'),
      hvac: t('maintenance.categories.hvac'),
      appliances: t('maintenance.categories.appliances'),
      structural: t('maintenance.categories.structural'),
      other: t('maintenance.categories.other'),
    },
    statuses: {
      open: t('maintenance.statuses.open'),
      inProgress: t('maintenance.statuses.inProgress'),
      resolved: t('maintenance.statuses.resolved'),
      closed: t('maintenance.statuses.closed'),
    },
    createdBy: t('maintenance.createdBy'),
    createdAt: t('maintenance.createdAt'),
    noTickets: t('maintenance.noTickets'),
    noTicketsDesc: t('maintenance.noTicketsDesc'),
    allStatuses: t('maintenance.allStatuses'),
  }
})

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

// Mock data
const tickets = ref([
  {
    id: 1,
    subject: 'Leaking faucet in kitchen',
    description: 'The kitchen faucet has been leaking for 2 days. Water drips constantly.',
    priority: 'high',
    category: 'plumbing',
    status: 'open',
    createdBy: 'John Doe',
    createdAt: '2 days ago',
  },
  {
    id: 2,
    subject: 'Heating not working',
    description: 'The heating system is not turning on. Temperature is very low.',
    priority: 'urgent',
    category: 'hvac',
    status: 'inProgress',
    createdBy: 'Jane Smith',
    createdAt: '1 day ago',
  },
  {
    id: 3,
    subject: 'Light fixture broken',
    description: 'Living room light fixture fell from ceiling. Needs replacement.',
    priority: 'medium',
    category: 'electrical',
    status: 'resolved',
    createdBy: 'John Doe',
    createdAt: '1 week ago',
  },
])

// Computed
const filteredTickets = computed(() => {
  if (!filterStatus.value) return tickets.value
  return tickets.value.filter(t => t.status === filterStatus.value)
})

// Methods
const handleCreateTicket = async () => {
  submitting.value = true
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Add new ticket
    tickets.value.unshift({
      id: tickets.value.length + 1,
      ...ticketForm.value,
      createdBy: 'Current User',
      createdAt: 'Just now',
    })

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
  } catch (error) {
    console.error('Error creating ticket:', error)
  } finally {
    submitting.value = false
  }
}

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
</script>
