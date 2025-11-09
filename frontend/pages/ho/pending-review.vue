<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">{{ t('dashboard.ho.pendingReview') }}</h1>
      <p class="mt-2 text-muted-foreground">
        {{ t('dashboard.ho.reviewPropertiesDesc') }}
      </p>
    </div>

    <!-- Stats Card -->
    <Card v-if="!loading && !error">
      <CardContent class="p-6">
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <p class="text-sm text-muted-foreground">{{ t('dashboard.ho.totalPendingReview') }}</p>
            <p class="text-3xl font-bold text-warning">{{ pagination?.total || 0 }}</p>
          </div>
          <div class="h-12 w-12 rounded-full bg-warning/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-warning">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
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
        <Button @click="loadPendingProperties" class="mt-4" variant="outline">
          {{ t('common.retry') }}
        </Button>
      </div>
    </Card>

    <!-- Empty State -->
    <Card v-else-if="!properties.length" class="p-12">
      <div class="text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
            <path d="m5 12 7 7L22 9" />
          </svg>
        </div>
        <h3 class="text-lg font-medium">{{ t('property.ho.noPendingReview') }}</h3>
        <p class="text-sm text-muted-foreground mt-1">
          {{ t('property.ho.noPendingReviewDesc') }}
        </p>
      </div>
    </Card>

    <!-- Pending Properties List -->
    <div v-else class="space-y-4">
      <Card
        v-for="property in properties"
        :key="property.id"
        class="cursor-pointer hover:border-primary transition-colors"
        @click="navigateTo(`/properties/${property.id}`)"
      >
        <CardContent class="p-6">
          <div class="flex items-start justify-between gap-4">
            <!-- Property Info -->
            <div class="flex-1 space-y-3">
              <!-- Header -->
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="font-semibold text-lg">
                    {{ property.street_name }} {{ property.house_number }}{{ property.apt_number ? `, ${property.apt_number}` : '' }}
                  </h3>
                  <p class="text-sm text-muted-foreground">
                    {{ property.postal_code }} {{ property.city }}, {{ property.province }}
                  </p>
                </div>
                <Badge variant="warning">
                  {{ t('property.status.pending_review') }}
                </Badge>
              </div>

              <!-- Property Details -->
              <div class="flex items-center gap-6 text-sm text-muted-foreground">
                <div class="flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M9 3v18" />
                  </svg>
                  <span>{{ property.bedrooms }} {{ t('property.bedrooms') }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1 0l-1 1a1.5 1.5 0 0 0 0 1L7 9" />
                    <path d="m15 6 2.5-2.5a1.5 1.5 0 0 1 1 0l1 1a1.5 1.5 0 0 1 0 1L17 9" />
                    <path d="M9 18h6" />
                    <path d="M10 5v8h4V5" />
                  </svg>
                  <span>{{ property.bathrooms }} {{ t('property.bathrooms') }}</span>
                </div>
                <div v-if="property.furnishing_status === 'partially_furnished' || property.furnishing_status === 'fully_furnished'" class="flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
                    <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9" />
                    <path d="M12 3v6" />
                  </svg>
                  <span>{{ t('property.furnished') }}</span>
                </div>
              </div>

              <!-- Submission Info -->
              <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>{{ t('property.ho.submittedOn') }}: {{ formatDate(property.submitted_at) }}</span>
              </div>

              <!-- Listing Info (if exists) -->
              <div v-if="property.listing" class="pt-3 border-t border-border">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">{{ t('property.monthlyRent') }}</span>
                  <span class="font-semibold text-lg">
                    {{ formatCurrency(property.listing.monthly_rent, property.listing.currency) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-col gap-2 ml-4">
              <Button 
                @click.stop="quickApprove(property.id)" 
                size="sm"
                :disabled="actioning[property.id]"
              >
                <svg v-if="actioning[property.id]" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 animate-spin">
                  <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                  <path d="m5 12 7 7L22 9" />
                </svg>
                {{ t('property.detail.approve') }}
              </Button>
              <Button 
                @click.stop="navigateTo(`/properties/${property.id}`)" 
                size="sm"
                variant="outline"
              >
                {{ t('property.viewDetails') }}
              </Button>
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
import { ref, reactive, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'
import type { Property, PaginatedResponse } from '@/composables/useProperty'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'housing-office'],
})

const { t } = useI18n()
const { fetchProperties, approveProperty } = useProperty()

const properties = ref<Property[]>([])
const pagination = ref<Omit<PaginatedResponse<Property>, 'data'> | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const actioning = reactive<Record<number, boolean>>({})

const loadPendingProperties = async (page = 1) => {
  loading.value = true
  error.value = null

  try {
    const response = await fetchProperties({ 
      pending_review: true,
      page, 
      per_page: 20,
      sort: 'submitted_at'
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
    console.error('Failed to load pending properties:', err)
    error.value = err.data?.message || t('common.errorLoadingData')
  } finally {
    loading.value = false
  }
}

const changePage = (page: number) => {
  loadPendingProperties(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const quickApprove = async (propertyId: number) => {
  actioning[propertyId] = true
  
  try {
    await approveProperty(propertyId)
    // Remove from list after approval
    properties.value = properties.value.filter(p => p.id !== propertyId)
    if (pagination.value) {
      pagination.value.total--
    }
    console.log('Property approved')
  } catch (err: any) {
    console.error('Failed to approve property:', err)
    alert(err.data?.message || t('common.error'))
  } finally {
    actioning[propertyId] = false
  }
}

const formatCurrency = (amount: number, currency: string) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: currency,
  }).format(amount)
}

const formatDate = (date: string | undefined) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

onMounted(() => {
  loadPendingProperties()
})
</script>
