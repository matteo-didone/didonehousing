<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
      <p class="mt-2 text-lg text-muted-foreground">{{ translations.welcome }}</p>
      <p class="text-muted-foreground">{{ translations.subtitle }}</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Pending Approvals -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.pendingApprovals }}
              </p>
              <p class="mt-2 text-3xl font-bold text-warning">{{ stats.pendingApprovals }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-warning">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Total Properties -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.totalProperties }}
              </p>
              <p class="mt-2 text-3xl font-bold">{{ stats.totalProperties }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Active Tenants -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.activeTenants }}
              </p>
              <p class="mt-2 text-3xl font-bold text-success">{{ stats.activeTenants }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Open Tickets -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.openTickets }}
              </p>
              <p class="mt-2 text-3xl font-bold">{{ stats.openTickets }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Quick Actions -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.quickActions }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <!-- Review Properties -->
              <NuxtLink to="/ho/pending-review">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M9 11l3 3L22 4" />
                      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.reviewProperties }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.reviewPropertiesDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- Manage Tenants -->
              <NuxtLink to="/ho/tenants">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                      <circle cx="9" cy="7" r="4" />
                      <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                      <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.manageTenants }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.manageTenantsDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- View Reports -->
              <NuxtLink to="/ho/reports">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M3 3v18h18" />
                      <path d="m19 9-5 5-4-4-3 3" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.viewReports }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.viewReportsDesc }}
                  </p>
                </div>
              </NuxtLink>
            </div>
          </CardContent>
        </Card>

        <!-- Pending Approvals -->
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>{{ translations.pendingApprovalsList }}</CardTitle>
            <NuxtLink to="/ho/pending-review" class="text-sm text-primary hover:underline">
              {{ translations.viewAll }}
            </NuxtLink>
          </CardHeader>
          <CardContent>
            <div v-if="pendingApprovals.length > 0" class="space-y-4">
              <div
                v-for="property in pendingApprovals"
                :key="property.id"
                class="flex items-start gap-4 rounded-lg border border-border p-4"
              >
                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-muted">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="font-semibold">{{ property.address }}</h3>
                  <p class="text-sm text-muted-foreground">{{ property.city }} • {{ property.landlord }}</p>
                  <p class="mt-1 text-xs text-muted-foreground">Submitted {{ property.submittedDate }}</p>
                  <div class="mt-3 flex gap-2">
                    <Button size="sm" variant="success">
                      {{ translations.approve }}
                    </Button>
                    <Button size="sm" variant="destructive">
                      {{ translations.reject }}
                    </Button>
                    <Button size="sm" variant="outline" @click="navigateTo(`/properties/${property.id}`)">
                      {{ translations.viewDetails }}
                    </Button>
                  </div>
                </div>
                <Badge variant="warning">{{ t('property.statuses.pending') }}</Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Recent Approvals -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.recentApprovals }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="recentApprovals.length > 0" class="space-y-3">
              <div
                v-for="approval in recentApprovals"
                :key="approval.id"
                class="text-sm"
              >
                <div class="flex items-start justify-between">
                  <p class="font-medium line-clamp-1">{{ approval.property }}</p>
                  <Badge :variant="approval.status === 'approved' ? 'success' : 'destructive'" class="ml-2">
                    {{ approval.status }}
                  </Badge>
                </div>
                <p class="text-xs text-muted-foreground">{{ approval.statusText }} {{ approval.by }}</p>
                <p class="text-xs text-muted-foreground">{{ approval.date }}</p>
              </div>
            </div>
            <div v-else class="py-8 text-center text-muted-foreground text-sm">
              {{ translations.noRecentApprovals }}
            </div>
          </CardContent>
        </Card>

        <!-- Property Statistics -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.propertyStats }}</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <p class="text-sm font-medium mb-3">{{ translations.byStatus }}</p>
              <div class="space-y-2">
                <div>
                  <div class="mb-1 flex items-center justify-between text-sm">
                    <span>{{ t('property.statuses.available') }}</span>
                    <span class="font-medium">45</span>
                  </div>
                  <div class="h-2 w-full rounded-full bg-muted">
                    <div class="h-2 rounded-full bg-success" style="width: 60%"></div>
                  </div>
                </div>
                <div>
                  <div class="mb-1 flex items-center justify-between text-sm">
                    <span>{{ t('property.statuses.rented') }}</span>
                    <span class="font-medium">30</span>
                  </div>
                  <div class="h-2 w-full rounded-full bg-muted">
                    <div class="h-2 rounded-full bg-primary" style="width: 40%"></div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'housing-office'],
})

const { t, locale } = useI18n()

// Translations
const translations = ref({
  title: '',
  welcome: '',
  subtitle: '',
  stats: {
    pendingApprovals: '',
    totalProperties: '',
    activeTenants: '',
    openTickets: '',
  },
  quickActions: '',
  reviewProperties: '',
  reviewPropertiesDesc: '',
  manageTenants: '',
  manageTenantsDesc: '',
  viewReports: '',
  viewReportsDesc: '',
  pendingApprovalsList: '',
  viewAll: '',
  recentApprovals: '',
  noRecentApprovals: '',
  propertyStats: '',
  byStatus: '',
  approve: '',
  reject: '',
  viewDetails: '',
})

// Load translations function
const loadTranslations = () => {
  translations.value = {
    title: t('dashboard.ho.title'),
    welcome: t('dashboard.ho.welcome'),
    subtitle: t('dashboard.ho.subtitle'),
    stats: {
      pendingApprovals: t('dashboard.ho.stats.pendingApprovals'),
      totalProperties: t('dashboard.ho.stats.totalProperties'),
      activeTenants: t('dashboard.ho.stats.activeTenants'),
      openTickets: t('dashboard.ho.stats.openTickets'),
    },
    quickActions: t('dashboard.ho.quickActions'),
    reviewProperties: t('dashboard.ho.reviewProperties'),
    reviewPropertiesDesc: t('dashboard.ho.reviewPropertiesDesc'),
    manageTenants: t('dashboard.ho.manageTenants'),
    manageTenantsDesc: t('dashboard.ho.manageTenantsDesc'),
    viewReports: t('dashboard.ho.viewReports'),
    viewReportsDesc: t('dashboard.ho.viewReportsDesc'),
    pendingApprovalsList: t('dashboard.ho.pendingApprovalsList'),
    viewAll: t('dashboard.ho.viewAll'),
    recentApprovals: t('dashboard.ho.recentApprovals'),
    noRecentApprovals: t('dashboard.ho.noRecentApprovals'),
    propertyStats: t('dashboard.ho.propertyStats'),
    byStatus: t('dashboard.ho.byStatus'),
    approve: t('dashboard.ho.approve'),
    reject: t('dashboard.ho.reject'),
    viewDetails: t('dashboard.ho.viewDetails'),
  }
}

// Load translations on mount
onMounted(() => {
  loadTranslations()
})

// Watch for locale changes and reload translations
watch(locale, () => {
  loadTranslations()
})

// Mock data
const stats = ref({
  pendingApprovals: 5,
  totalProperties: 75,
  activeTenants: 58,
  openTickets: 12,
})

const pendingApprovals = ref([
  {
    id: 1,
    address: 'Via Roma, 123',
    city: 'Aviano',
    landlord: 'Mario Rossi',
    submittedDate: '2 days ago',
  },
  {
    id: 2,
    address: 'Via Giuseppe Verdi, 45',
    city: 'Pordenone',
    landlord: 'Laura Bianchi',
    submittedDate: '3 days ago',
  },
  {
    id: 3,
    address: 'Corso Italia, 67',
    city: 'Roveredo in Piano',
    landlord: 'Giovanni Neri',
    submittedDate: '5 days ago',
  },
])

const recentApprovals = ref([
  {
    id: 1,
    property: 'Via Dante, 89',
    status: 'approved',
    statusText: t('dashboard.ho.approvedBy'),
    by: 'John Smith',
    date: '1 hour ago',
  },
  {
    id: 2,
    property: 'Via Mazzini, 12',
    status: 'rejected',
    statusText: t('dashboard.ho.rejectedBy'),
    by: 'Sarah Johnson',
    date: '3 hours ago',
  },
  {
    id: 3,
    property: 'Piazza Libertà, 34',
    status: 'approved',
    statusText: t('dashboard.ho.approvedBy'),
    by: 'Michael Brown',
    date: '5 hours ago',
  },
])
</script>
