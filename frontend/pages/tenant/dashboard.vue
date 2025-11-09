<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
      <p class="mt-2 text-lg text-muted-foreground">
        {{ welcomeMessage }}
      </p>
      <p class="text-muted-foreground">{{ translations.subtitle }}</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Saved Properties -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.savedProperties }}
              </p>
              <p class="mt-2 text-3xl font-bold">{{ stats.savedProperties }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Viewing Requests -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.viewingRequests }}
              </p>
              <p class="mt-2 text-3xl font-bold text-warning">{{ stats.viewingRequests }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-warning">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Active Lease -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.activeLease }}
              </p>
              <p class="mt-2 text-3xl font-bold text-success">{{ stats.activeLease ? '1' : '0' }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                <polyline points="14 2 14 8 20 8" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Maintenance Tickets -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.maintenanceTickets }}
              </p>
              <p class="mt-2 text-3xl font-bold">{{ stats.maintenanceTickets }}</p>
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
              <!-- Search Properties -->
              <NuxtLink to="/properties">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.3-4.3" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.searchProperties }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.searchPropertiesDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- My Lease -->
              <NuxtLink to="/lease">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                      <polyline points="14 2 14 8 20 8" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.myLease }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.myLeaseDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- Submit Maintenance -->
              <NuxtLink to="/maintenance">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.submitMaintenance }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.submitMaintenanceDesc }}
                  </p>
                </div>
              </NuxtLink>
            </div>
          </CardContent>
        </Card>

        <!-- Featured Properties -->
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>{{ translations.featuredProperties }}</CardTitle>
            <NuxtLink to="/properties" class="text-sm text-primary hover:underline">
              {{ translations.viewAllProperties }}
            </NuxtLink>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div
                v-for="property in featuredProperties"
                :key="property.id"
                class="group cursor-pointer rounded-lg border border-border p-4 transition-all hover:border-primary hover:shadow-md"
                @click="navigateTo(`/properties/${property.id}`)"
              >
                <div class="flex items-start gap-4">
                  <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                      <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-semibold line-clamp-1">{{ property.address }}</h3>
                    <p class="text-sm text-muted-foreground">{{ property.city }}</p>
                    <div class="mt-2 flex items-center justify-between">
                      <p class="text-lg font-bold text-primary">€{{ property.rent.toLocaleString() }}{{ t('units.perMonthShort') }}</p>
                      <Badge variant="success">{{ property.bedrooms }} {{ t('units.bedroomsShort') }}</Badge>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Upcoming Payments -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.upcomingPayments }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="upcomingPayments.length > 0" class="space-y-4">
              <div
                v-for="payment in upcomingPayments"
                :key="payment.id"
                class="rounded-lg border border-border p-4"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium">{{ payment.description }}</span>
                  <Badge :variant="payment.urgent ? 'destructive' : 'default'">
                    {{ payment.urgent ? 'Urgent' : 'Due' }}
                  </Badge>
                </div>
                <p class="text-xs text-muted-foreground">{{ translations.dueDate }}: {{ payment.dueDate }}</p>
                <p class="mt-2 text-xl font-bold text-primary">€{{ payment.amount.toLocaleString() }}</p>
                <Button size="sm" class="mt-3 w-full">{{ translations.payNow }}</Button>
              </div>
            </div>
            <div v-else class="py-8 text-center text-muted-foreground text-sm">
              {{ translations.noPayments }}
            </div>
          </CardContent>
        </Card>

        <!-- Recent Activity -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.recentActivity }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="recentActivity.length > 0" class="space-y-3">
              <div
                v-for="activity in recentActivity"
                :key="activity.id"
                class="text-sm"
              >
                <p class="font-medium">{{ activity.title }}</p>
                <p class="text-xs text-muted-foreground">{{ activity.time }}</p>
              </div>
            </div>
            <div v-else class="py-8 text-center text-muted-foreground text-sm">
              {{ translations.noActivity }}
            </div>
          </CardContent>
        </Card>
      </div>
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
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'tenant'],
})

const { user } = useAuth()
const { t, locale } = useI18n()
const { fetchStats, loading: dashboardLoading } = useTenantDashboard()

// Welcome message with user name (reactive to user changes)
const welcomeMessage = computed(() => {
  return t('dashboard.tenant.welcome', { name: user.value?.first_name || '' })
})

// Translations
const translations = ref({
  title: '',
  welcome: '',
  subtitle: '',
  stats: {
    savedProperties: '',
    viewingRequests: '',
    activeLease: '',
    maintenanceTickets: '',
  },
  quickActions: '',
  searchProperties: '',
  searchPropertiesDesc: '',
  myLease: '',
  myLeaseDesc: '',
  submitMaintenance: '',
  submitMaintenanceDesc: '',
  featuredProperties: '',
  viewAllProperties: '',
  recentActivity: '',
  noActivity: '',
  upcomingPayments: '',
  dueDate: '',
  amount: '',
  payNow: '',
  noPayments: '',
})

// Load translations function
const loadTranslations = () => {
  translations.value = {
    title: t('dashboard.tenant.title'),
    welcome: t('dashboard.tenant.welcome'),
    subtitle: t('dashboard.tenant.subtitle'),
    stats: {
      savedProperties: t('dashboard.tenant.stats.savedProperties'),
      viewingRequests: t('dashboard.tenant.stats.viewingRequests'),
      activeLease: t('dashboard.tenant.stats.activeLease'),
      maintenanceTickets: t('dashboard.tenant.stats.maintenanceTickets'),
    },
    quickActions: t('dashboard.tenant.quickActions'),
    searchProperties: t('dashboard.tenant.searchProperties'),
    searchPropertiesDesc: t('dashboard.tenant.searchPropertiesDesc'),
    myLease: t('dashboard.tenant.myLease'),
    myLeaseDesc: t('dashboard.tenant.myLeaseDesc'),
    submitMaintenance: t('dashboard.tenant.submitMaintenance'),
    submitMaintenanceDesc: t('dashboard.tenant.submitMaintenanceDesc'),
    featuredProperties: t('dashboard.tenant.featuredProperties'),
    viewAllProperties: t('dashboard.tenant.viewAllProperties'),
    recentActivity: t('dashboard.tenant.recentActivity'),
    noActivity: t('dashboard.tenant.noActivity'),
    upcomingPayments: t('dashboard.tenant.upcomingPayments'),
    dueDate: t('dashboard.tenant.dueDate'),
    amount: t('dashboard.tenant.amount'),
    payNow: t('dashboard.tenant.payNow'),
    noPayments: t('dashboard.tenant.noPayments'),
  }
}

// Watch for locale changes and reload translations
watch(locale, () => {
  loadTranslations()
})

// Data from API
const stats = ref({
  savedProperties: 0,
  viewingRequests: 0,
  activeLease: false,
  maintenanceTickets: 0,
})

const featuredProperties = ref<any[]>([])
const upcomingPayments = ref<any[]>([])
const recentActivity = ref<any[]>([])

// Load dashboard data
const loadDashboardData = async () => {
  try {
    const statsData = await fetchStats()
    stats.value = statsData
    // TODO: Add endpoints for featured properties, payments, and activity when backend is ready
  } catch (err: any) {
    console.error('Error loading tenant dashboard data:', err)
  }
}

// Load data on mount
onMounted(() => {
  loadTranslations()
  loadDashboardData()
})
</script>
