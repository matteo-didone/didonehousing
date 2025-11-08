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

      <!-- Active Listings -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.activeListings }}
              </p>
              <p class="mt-2 text-3xl font-bold text-success">{{ stats.activeListings }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Pending Review -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.pendingReview }}
              </p>
              <p class="mt-2 text-3xl font-bold text-warning">{{ stats.pendingReview }}</p>
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

      <!-- Monthly Revenue -->
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ translations.stats.monthlyRevenue }}
              </p>
              <p class="mt-2 text-3xl font-bold">€{{ stats.monthlyRevenue.toLocaleString() }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <line x1="12" x2="12" y1="2" y2="22" />
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Quick Actions -->
      <div class="lg:col-span-2 space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.quickActions }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <!-- Add Property -->
              <NuxtLink to="/properties/create">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M5 12h14" />
                      <path d="M12 5v14" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.addProperty }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.addPropertyDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- My Properties -->
              <NuxtLink to="/properties">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                      <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.viewProperties }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.viewPropertiesDesc }}
                  </p>
                </div>
              </NuxtLink>

              <!-- View Inquiries -->
              <NuxtLink to="/messages">
                <div class="flex flex-col items-center rounded-lg border border-border p-6 text-center transition-all hover:border-primary hover:shadow-md cursor-pointer">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                  </div>
                  <h3 class="font-semibold">{{ translations.viewInquiries }}</h3>
                  <p class="mt-1 text-xs text-muted-foreground">
                    {{ translations.viewInquiriesDesc }}
                  </p>
                </div>
              </NuxtLink>
            </div>
          </CardContent>
        </Card>

        <!-- Recent Activity -->
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>{{ translations.recentActivity }}</CardTitle>
            <NuxtLink to="/properties" class="text-sm text-primary hover:underline">
              {{ translations.viewAll }}
            </NuxtLink>
          </CardHeader>
          <CardContent>
            <div v-if="recentActivity.length > 0" class="space-y-4">
              <div
                v-for="activity in recentActivity"
                :key="activity.id"
                class="flex items-start gap-4 rounded-lg border border-border p-4"
              >
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="font-medium">{{ activity.title }}</p>
                  <p class="text-sm text-muted-foreground">{{ activity.description }}</p>
                  <p class="mt-1 text-xs text-muted-foreground">{{ activity.time }}</p>
                </div>
                <Badge :variant="activity.status === 'success' ? 'success' : 'warning'">
                  {{ activity.statusText }}
                </Badge>
              </div>
            </div>
            <div v-else class="py-8 text-center text-muted-foreground">
              {{ translations.noActivity }}
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Sidebar Stats -->
      <div class="space-y-6">
        <!-- Property Status Distribution -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.propertyStatus }}</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="space-y-3">
              <div>
                <div class="mb-1 flex items-center justify-between text-sm">
                  <span>{{ t('property.statuses.available') }}</span>
                  <span class="font-medium">2</span>
                </div>
                <div class="h-2 w-full rounded-full bg-muted">
                  <div class="h-2 rounded-full bg-success" style="width: 50%"></div>
                </div>
              </div>

              <div>
                <div class="mb-1 flex items-center justify-between text-sm">
                  <span>{{ t('property.statuses.pending') }}</span>
                  <span class="font-medium">1</span>
                </div>
                <div class="h-2 w-full rounded-full bg-muted">
                  <div class="h-2 rounded-full bg-warning" style="width: 25%"></div>
                </div>
              </div>

              <div>
                <div class="mb-1 flex items-center justify-between text-sm">
                  <span>{{ t('property.statuses.rented') }}</span>
                  <span class="font-medium">1</span>
                </div>
                <div class="h-2 w-full rounded-full bg-muted">
                  <div class="h-2 rounded-full bg-primary" style="width: 25%"></div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Revenue Overview -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.revenueOverview }}</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">{{ translations.thisMonth }}</span>
                <span class="text-sm font-medium text-success">+12%</span>
              </div>
              <p class="mt-1 text-2xl font-bold">€{{ stats.monthlyRevenue.toLocaleString() }}</p>
            </div>
            <div class="border-t border-border pt-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">{{ translations.lastMonth }}</span>
              </div>
              <p class="mt-1 text-xl font-semibold text-muted-foreground">€3,200</p>
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
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'landlord'],
})

const { user } = useAuth()
const { t, locale } = useI18n()

// Welcome message with user name (reactive to user changes)
const welcomeMessage = computed(() => {
  return t('dashboard.landlord.welcome', { name: user.value?.first_name || '' })
})

// Translations
const translations = ref({
  title: '',
  welcome: '',
  subtitle: '',
  stats: {
    totalProperties: '',
    activeListings: '',
    pendingReview: '',
    monthlyRevenue: '',
  },
  quickActions: '',
  addProperty: '',
  addPropertyDesc: '',
  viewProperties: '',
  viewPropertiesDesc: '',
  viewInquiries: '',
  viewInquiriesDesc: '',
  recentActivity: '',
  noActivity: '',
  propertyStatus: '',
  revenueOverview: '',
  thisMonth: '',
  lastMonth: '',
  viewAll: '',
})

// Load translations function
const loadTranslations = () => {
  translations.value = {
    title: t('dashboard.landlord.title'),
    welcome: t('dashboard.landlord.welcome'),
    subtitle: t('dashboard.landlord.subtitle'),
    stats: {
      totalProperties: t('dashboard.landlord.stats.totalProperties'),
      activeListings: t('dashboard.landlord.stats.activeListings'),
      pendingReview: t('dashboard.landlord.stats.pendingReview'),
      monthlyRevenue: t('dashboard.landlord.stats.monthlyRevenue'),
    },
    quickActions: t('dashboard.landlord.quickActions'),
    addProperty: t('dashboard.landlord.addProperty'),
    addPropertyDesc: t('dashboard.landlord.addPropertyDesc'),
    viewProperties: t('dashboard.landlord.viewProperties'),
    viewPropertiesDesc: t('dashboard.landlord.viewPropertiesDesc'),
    viewInquiries: t('dashboard.landlord.viewInquiries'),
    viewInquiriesDesc: t('dashboard.landlord.viewInquiriesDesc'),
    recentActivity: t('dashboard.landlord.recentActivity'),
    noActivity: t('dashboard.landlord.noActivity'),
    propertyStatus: t('dashboard.landlord.propertyStatus'),
    revenueOverview: t('dashboard.landlord.revenueOverview'),
    thisMonth: t('dashboard.landlord.thisMonth'),
    lastMonth: t('dashboard.landlord.lastMonth'),
    viewAll: t('dashboard.landlord.viewAll'),
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
  totalProperties: 4,
  activeListings: 2,
  pendingReview: 1,
  monthlyRevenue: 3600,
})

const recentActivity = ref([
  {
    id: 1,
    title: 'New property approved',
    description: 'Via Roma, 123 - Aviano',
    time: '2 hours ago',
    status: 'success',
    statusText: 'Approved',
  },
  {
    id: 2,
    title: 'Property inquiry',
    description: 'Tenant requested viewing for Via Giuseppe Verdi, 45',
    time: '5 hours ago',
    status: 'warning',
    statusText: 'Pending',
  },
  {
    id: 3,
    title: 'Rent payment received',
    description: 'Corso Italia, 67 - €900',
    time: '1 day ago',
    status: 'success',
    statusText: 'Completed',
  },
])
</script>
