<template>
  <div class="max-w-5xl mx-auto space-y-6">
    <!-- Loading State -->
    <div v-if="loading" class="space-y-4">
      <div class="animate-pulse space-y-4">
        <div class="h-8 bg-muted rounded w-1/3"></div>
        <div class="h-64 bg-muted rounded"></div>
        <div class="h-48 bg-muted rounded"></div>
      </div>
    </div>

    <!-- Error State -->
    <Card v-else-if="error" class="p-6">
      <div class="text-center text-destructive">
        <p class="font-medium">{{ t('common.error') }}</p>
        <p class="text-sm mt-1">{{ error }}</p>
        <div class="mt-4 space-x-2">
          <Button @click="loadProperty" variant="outline">
            {{ t('common.retry') }}
          </Button>
          <NuxtLink to="/properties">
            <Button variant="outline">
              {{ t('property.detail.backToList') }}
            </Button>
          </NuxtLink>
        </div>
      </div>
    </Card>

    <!-- Property Detail -->
    <template v-else-if="property">
      <!-- Header -->
      <div>
        <NuxtLink to="/properties" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7" />
            <path d="M19 12H5" />
          </svg>
          {{ t('property.detail.backToList') }}
        </NuxtLink>

        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h1 class="text-3xl font-bold tracking-tight">
                {{ property.street_name }} {{ property.house_number }}{{ property.apt_number ? `, ${property.apt_number}` : '' }}
              </h1>
              <Badge :variant="getStatusVariant(property.status)">
                {{ t(`property.status.${property.status}`) }}
              </Badge>
            </div>
            <p class="text-muted-foreground">
              {{ property.postal_code }} {{ property.city }}, {{ property.province }} - {{ property.country }}
            </p>
          </div>
        </div>
      </div>

      <!-- Status Info (if pending/approved/rejected) -->
      <Card v-if="property.status !== 'draft'" class="border-l-4" :class="getStatusBorderClass(property.status)">
        <CardContent class="p-4">
          <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 16v-4" />
              <path d="M12 8h.01" />
            </svg>
            <div class="flex-1">
              <p class="font-medium">
                <template v-if="property.status === 'pending_review'">
                  {{ t('property.detail.statusPendingReview') }}
                </template>
                <template v-else-if="property.status === 'approved'">
                  {{ t('property.detail.statusApproved') }}
                </template>
                <template v-else-if="property.status === 'rejected'">
                  {{ t('property.detail.statusRejected') }}
                </template>
              </p>
              <p v-if="property.ho_reviewer" class="text-sm text-muted-foreground mt-1">
                {{ t('property.detail.reviewer') }}: {{ property.ho_reviewer.first_name }} {{ property.ho_reviewer.last_name }}
              </p>
              <p v-if="property.rejection_reason" class="text-sm mt-2 p-3 bg-destructive/10 rounded border border-destructive/20">
                <span class="font-medium">{{ t('property.detail.rejectionReason') }}:</span> {{ property.rejection_reason }}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Property Info -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.detail.propertyInfo') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
          <!-- Rooms Summary -->
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="text-center p-4 border border-border rounded-lg">
              <div class="text-3xl font-bold text-primary">{{ property.bedrooms }}</div>
              <div class="text-sm text-muted-foreground mt-1">{{ t('property.bedrooms') }}</div>
            </div>
            <div class="text-center p-4 border border-border rounded-lg">
              <div class="text-3xl font-bold text-primary">{{ property.bathrooms }}</div>
              <div class="text-sm text-muted-foreground mt-1">{{ t('property.bathrooms') }}</div>
            </div>
            <div v-if="property.living_rooms" class="text-center p-4 border border-border rounded-lg">
              <div class="text-3xl font-bold text-primary">{{ property.living_rooms }}</div>
              <div class="text-sm text-muted-foreground mt-1">{{ t('property.create.livingRooms') }}</div>
            </div>
            <div v-if="property.dining_rooms" class="text-center p-4 border border-border rounded-lg">
              <div class="text-3xl font-bold text-primary">{{ property.dining_rooms }}</div>
              <div class="text-sm text-muted-foreground mt-1">{{ t('property.create.diningRooms') }}</div>
            </div>
          </div>

          <!-- Details Grid -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.furnishingStatus') }}</span>
              <span class="font-medium">{{ t(`property.create.${property.furnishing_status}`) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.pets') }}</span>
              <span class="font-medium">{{ property.pets_allowed ? t('common.yes') : t('common.no') }}</span>
            </div>
            <div v-if="property.heating_type" class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.heatingType') }}</span>
              <span class="font-medium">{{ t(`property.heating.${property.heating_type}`) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.basement') }}</span>
              <span class="font-medium">{{ property.basement ? t('common.yes') : t('common.no') }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.attic') }}</span>
              <span class="font-medium">{{ property.attic ? t('common.yes') : t('common.no') }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.garage') }}</span>
              <span class="font-medium">{{ property.garage ? t('common.yes') : t('common.no') }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-muted/50 rounded">
              <span class="text-sm text-muted-foreground">{{ t('property.create.yard') }}</span>
              <span class="font-medium">{{ property.yard ? t('common.yes') : t('common.no') }}</span>
            </div>
          </div>

          <!-- Listing Info (if exists) -->
          <div v-if="property.listing" class="p-4 border-2 border-primary/20 bg-primary/5 rounded-lg">
            <h3 class="font-semibold text-lg mb-3">{{ t('property.detail.listingInfo') }}</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">{{ t('property.monthlyRent') }}</span>
                <span class="text-xl font-bold text-primary">
                  {{ formatCurrency(property.listing.monthly_rent, property.listing.currency) }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">{{ t('property.availableFrom') }}</span>
                <span class="font-medium">{{ formatDate(property.listing.available_from) }}</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Actions -->
      <Card>
        <CardContent class="p-6">
          <div class="flex flex-wrap items-center gap-3">
            <!-- Landlord Actions -->
            <template v-if="isLandlord">
              <!-- Draft status (or corrupted/null status): can edit, delete, submit -->
              <template v-if="property.status === 'draft' || !property.status">
                <NuxtLink :to="`/properties/${property.id}/edit`">
                  <Button variant="outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                    </svg>
                    {{ t('property.detail.edit') }}
                  </Button>
                </NuxtLink>
                <Button @click="handleSubmitForReview" :disabled="submitting" variant="default">
                  <svg v-if="submitting" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 animate-spin">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="m5 12 7 7L22 9" />
                  </svg>
                  {{ submitting ? t('property.detail.submitting') : t('property.detail.submitForReview') }}
                </Button>
                <Button @click="confirmDelete" variant="destructive">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                  </svg>
                  {{ t('property.detail.delete') }}
                </Button>
              </template>

              <!-- Rejected status: can edit and resubmit -->
              <template v-else-if="property.status === 'rejected'">
                <NuxtLink :to="`/properties/${property.id}/edit`">
                  <Button variant="outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                    </svg>
                    {{ t('property.detail.editAndResubmit') }}
                  </Button>
                </NuxtLink>
              </template>
            </template>

            <!-- HO Actions -->
            <template v-if="isHousingOffice && property.status === 'pending_review'">
              <Button @click="handleApprove" :disabled="actioning" variant="default">
                <svg v-if="actioning" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 animate-spin">
                  <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                  <path d="m5 12 7 7L22 9" />
                </svg>
                {{ t('property.detail.approve') }}
              </Button>
              <Button @click="showRejectDialog = true" :disabled="actioning" variant="destructive">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                  <path d="M18 6 6 18" />
                  <path d="m6 6 12 12" />
                </svg>
                {{ t('property.detail.reject') }}
              </Button>
            </template>
          </div>
        </CardContent>
      </Card>

      <!-- Reject Dialog -->
      <div v-if="showRejectDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showRejectDialog = false">
        <Card class="w-full max-w-md m-4">
          <CardHeader>
            <CardTitle>{{ t('property.detail.rejectProperty') }}</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="space-y-2">
              <Label for="rejection_reason">{{ t('property.detail.rejectionReason') }} *</Label>
              <textarea
                id="rejection_reason"
                v-model="rejectionReason"
                rows="4"
                class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                :placeholder="t('property.detail.rejectionReasonPlaceholder')"
                required
              ></textarea>
            </div>
            <div class="flex justify-end gap-2">
              <Button @click="showRejectDialog = false" variant="outline">
                {{ t('common.cancel') }}
              </Button>
              <Button @click="handleReject" :disabled="!rejectionReason.trim() || actioning" variant="destructive">
                <svg v-if="actioning" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 animate-spin">
                  <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
                {{ actioning ? t('property.detail.rejecting') : t('property.detail.confirmReject') }}
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'
import Label from '@/components/ui/Label.vue'
import type { Property } from '@/composables/useProperty'

definePageMeta({
  layout: 'default',
  middleware: ['auth'],
})

const route = useRoute()
const { t } = useI18n()
const { user, isLandlord, isHousingOffice } = useAuth()
const { fetchProperty, submitForReview, approveProperty, rejectProperty, deleteProperty } = useProperty()

const property = ref<Property | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const submitting = ref(false)
const actioning = ref(false)
const showRejectDialog = ref(false)
const rejectionReason = ref('')

const loadProperty = async () => {
  loading.value = true
  error.value = null

  try {
    const id = parseInt(route.params.id as string)
    property.value = await fetchProperty(id)
  } catch (err: any) {
    console.error('Failed to load property:', err)
    error.value = err.data?.message || t('common.errorLoadingData')
  } finally {
    loading.value = false
  }
}

const handleSubmitForReview = async () => {
  if (!property.value) return
  
  submitting.value = true
  try {
    const response = await submitForReview(property.value.id)
    property.value = response.property
    console.log('Property submitted for review')
  } catch (err: any) {
    console.error('Failed to submit property:', err)
    alert(err.data?.message || t('common.error'))
  } finally {
    submitting.value = false
  }
}

const handleApprove = async () => {
  if (!property.value) return
  
  actioning.value = true
  try {
    const response = await approveProperty(property.value.id)
    property.value = response.property
    console.log('Property approved')
  } catch (err: any) {
    console.error('Failed to approve property:', err)
    alert(err.data?.message || t('common.error'))
  } finally {
    actioning.value = false
  }
}

const handleReject = async () => {
  if (!property.value || !rejectionReason.value.trim()) return
  
  actioning.value = true
  try {
    const response = await rejectProperty(property.value.id, rejectionReason.value)
    property.value = response.property
    showRejectDialog.value = false
    rejectionReason.value = ''
    console.log('Property rejected')
  } catch (err: any) {
    console.error('Failed to reject property:', err)
    alert(err.data?.message || t('common.error'))
  } finally {
    actioning.value = false
  }
}

const confirmDelete = () => {
  if (!property.value) return
  
  if (confirm(t('property.detail.confirmDelete'))) {
    handleDelete()
  }
}

const handleDelete = async () => {
  if (!property.value) return
  
  try {
    await deleteProperty(property.value.id)
    await navigateTo('/properties')
  } catch (err: any) {
    console.error('Failed to delete property:', err)
    alert(err.data?.message || t('common.error'))
  }
}

const getStatusVariant = (status: Property['status']) => {
  switch (status) {
    case 'draft':
      return 'secondary'
    case 'pending_review':
      return 'warning'
    case 'approved':
      return 'success'
    case 'rejected':
      return 'destructive'
    default:
      return 'default'
  }
}

const getStatusBorderClass = (status: Property['status']) => {
  switch (status) {
    case 'pending_review':
      return 'border-l-warning'
    case 'approved':
      return 'border-l-success'
    case 'rejected':
      return 'border-l-destructive'
    default:
      return 'border-l-muted'
  }
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
    month: 'long',
    day: 'numeric',
  })
}

onMounted(() => {
  loadProperty()
})
</script>
