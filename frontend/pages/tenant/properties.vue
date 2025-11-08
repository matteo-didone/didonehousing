<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ t('property.title') }}</h1>
      <p class="mt-2 text-muted-foreground">
        {{ t('property.subtitle.tenant') }}
      </p>
    </div>

    <!-- Stats Card -->
    <Card v-if="!loading && !error">
      <CardContent class="p-6">
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <p class="text-sm text-muted-foreground">{{ t('property.tenant.availableProperties') }}</p>
            <p class="text-3xl font-bold text-primary">{{ pagination?.total || 0 }}</p>
          </div>
          <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
              <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-4">
      <Card v-for="i in 3" :key="i" class="p-6">
        <div class="animate-pulse space-y-3">
          <div class="h-4 bg-muted rounded w-3/4"></div>
          <div class="h-3 bg-muted rounded w-1/2"></div>
          <div class="h-3 bg-muted rounded w-1/4"></div>
        </div>
      </Card>
    </div>

    <!-- Error State -->
    <Card v-else-if="error" class="p-6">
      <div class="text-center text-destructive">
        <p class="font-medium">{{ t('common.error') }}</p>
        <p class="text-sm mt-1">{{ error }}</p>
        <Button @click="loadProperties" class="mt-4" variant="outline">
          {{ t('common.retry') }}
        </Button>
      </div>
    </Card>

    <!-- Empty State -->
    <Card v-else-if="!properties.length" class="p-12">
      <div class="text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
        </div>
        <h3 class="text-lg font-medium">{{ t('property.noResults') }}</h3>
        <p class="text-sm text-muted-foreground mt-1">
          {{ t('property.noResultsDescription') }}
        </p>
      </div>
    </Card>

    <!-- Properties Grid -->
    <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <Card
        v-for="property in properties"
        :key="property.id"
        class="cursor-pointer hover:border-primary transition-colors overflow-hidden"
        @click="navigateTo(`/properties/${property.id}`)"
      >
        <CardContent class="p-0">
          <!-- Image Placeholder (future) -->
          <div class="h-48 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary/40">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
              <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
          </div>

          <!-- Content -->
          <div class="p-6 space-y-4">
            <!-- Header with Price -->
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <h3 class="font-semibold text-lg line-clamp-1">
                  {{ property.street_name }} {{ property.house_number }}{{ property.apt_number ? `, ${property.apt_number}` : '' }}
                </h3>
                <p class="text-sm text-muted-foreground">
                  {{ property.city }}, {{ property.province }}
                </p>
              </div>
              <Badge variant="success" class="shrink-0">
                {{ t('property.status.approved') }}
              </Badge>
            </div>

            <!-- Price -->
            <div v-if="property.listing" class="p-3 bg-primary/5 rounded-lg border border-primary/20">
              <div class="flex items-baseline justify-between">
                <div>
                  <p class="text-2xl font-bold text-primary">
                    {{ formatCurrency(property.listing.monthly_rent, property.listing.currency) }}
                  </p>
                  <p class="text-xs text-muted-foreground mt-1">{{ t('property.perMonth') }}</p>
                </div>
                <div v-if="property.listing.available_from" class="text-right">
                  <p class="text-xs text-muted-foreground">{{ t('property.availableFrom') }}</p>
                  <p class="text-sm font-medium">{{ formatDate(property.listing.available_from) }}</p>
                </div>
              </div>
            </div>

            <!-- Property Details -->
            <div class="flex items-center gap-4 text-sm text-muted-foreground">
              <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                  <path d="M9 3v18" />
                </svg>
                <span>{{ property.bedrooms }}</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1 0l-1 1a1.5 1.5 0 0 0 0 1L7 9" />
                  <path d="m15 6 2.5-2.5a1.5 1.5 0 0 1 1 0l1 1a1.5 1.5 0 0 1 0 1L17 9" />
                  <path d="M9 18h6" />
                  <path d="M10 5v8h4V5" />
                </svg>
                <span>{{ property.bathrooms }}</span>
              </div>
              <div v-if="property.furnished" class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
                  <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9" />
                  <path d="M12 3v6" />
                </svg>
                <span>{{ t('property.furnished') }}</span>
              </div>
              <div v-if="property.pets_allowed" class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="4" r="2" />
                  <circle cx="18" cy="8" r="2" />
                  <circle cx="20" cy="16" r="2" />
                  <path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z" />
                </svg>
                <span>{{ t('property.tenant.petsOk') }}</span>
              </div>
            </div>

            <!-- View Details Button -->
            <div class="pt-2 border-t border-border">
              <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">{{ t('property.tenant.clickToViewDetails') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <path d="m9 18 6-6-6-6" />
                </svg>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
      <Button
        variant="outline"
        size="sm"
        :disabled="pagination.current_page === 1"
        @click="changePage(pagination.current_page - 1)"
      >
        {{ t('common.previous') }}
      </Button>
      <span class="text-sm text-muted-foreground">
        {{ t('common.page') }} {{ pagination.current_page }} {{ t('common.of') }} {{ pagination.last_page }}
      </span>
      <Button
        variant="outline"
        size="sm"
        :disabled="pagination.current_page === pagination.last_page"
        @click="changePage(pagination.current_page + 1)"
      >
        {{ t('common.next') }}
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'
import type { Property, PaginatedResponse } from '@/composables/useProperty'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'tenant'],
})

const { t } = useI18n()
const { fetchProperties } = useProperty()

const properties = ref<Property[]>([])
const pagination = ref<Omit<PaginatedResponse<Property>, 'data'> | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const loadProperties = async (page = 1) => {
  loading.value = true
  error.value = null

  try {
    const response = await fetchProperties({ 
      approved: true,  // Only show approved properties
      page, 
      per_page: 12,
      sort: '-created_at'
    })
    properties.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      per_page: response.per_page,
      total: response.total,
      from: response.from,
      to: response.to,
    }
  } catch (err: any) {
    console.error('Failed to load properties:', err)
    error.value = err.data?.message || t('common.errorLoadingData')
  } finally {
    loading.value = false
  }
}

const changePage = (page: number) => {
  loadProperties(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const formatCurrency = (amount: number, currency: string) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: currency,
  }).format(amount)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

onMounted(() => {
  loadProperties()
})
</script>
