<template>
  <div v-if="property" class="space-y-6">
    <!-- Back Navigation -->
    <NuxtLink to="/properties" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
        <path d="m12 19-7-7 7-7" />
        <path d="M19 12H5" />
      </svg>
      {{ translations.backToList }}
    </NuxtLink>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Property Image -->
        <Card class="overflow-hidden">
          <div class="relative h-96 bg-muted">
            <img
              v-if="property.image"
              :src="property.image"
              :alt="property.address"
              class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
              </svg>
            </div>
            <!-- Status Badge -->
            <div class="absolute top-4 right-4">
              <Badge :variant="getStatusVariant(property.status)">
                {{ translations.statuses[property.status] }}
              </Badge>
            </div>
          </div>
        </Card>

        <!-- Description -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.description }}</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-muted-foreground leading-relaxed">
              {{ property.description || 'No description available.' }}
            </p>
          </CardContent>
        </Card>

        <!-- Amenities -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.amenities }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
              <!-- Parking -->
              <div v-if="property.amenities?.parking" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                  <path d="M7 7h4a4 4 0 0 1 0 8H7" />
                  <path d="M7 15V7" />
                </svg>
                <span>{{ translations.create.parking }}</span>
              </div>

              <!-- Balcony -->
              <div v-if="property.amenities?.balcony" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                </svg>
                <span>{{ translations.create.balcony }}</span>
              </div>

              <!-- Garden -->
              <div v-if="property.amenities?.garden" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <path d="M12 22v-8" />
                  <path d="M8 14c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                  <circle cx="8" cy="10" r="4" />
                  <circle cx="16" cy="10" r="4" />
                </svg>
                <span>{{ translations.create.garden }}</span>
              </div>

              <!-- Air Conditioning -->
              <div v-if="property.amenities?.airConditioning" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
                <span>{{ translations.create.airConditioning }}</span>
              </div>

              <!-- Heating -->
              <div v-if="property.amenities?.heating" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <path d="M12 2v20M2 12h20" />
                </svg>
                <span>{{ translations.create.heating }}</span>
              </div>

              <!-- Elevator -->
              <div v-if="property.amenities?.elevator" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <path d="m18 6-6-6-6 6" />
                  <path d="m18 18 6 6-6 6" />
                  <path d="M12 3v18" />
                </svg>
                <span>{{ translations.create.elevator }}</span>
              </div>

              <!-- Pets -->
              <div v-if="property.amenities?.pets" class="flex items-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <circle cx="11" cy="4" r="2" />
                  <circle cx="18" cy="8" r="2" />
                  <circle cx="20" cy="16" r="2" />
                  <path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q7.16 17.5 7 17.5a3 3 0 0 1 0-6" />
                </svg>
                <span>{{ translations.create.pets }}</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Location -->
        <Card>
          <CardHeader>
            <CardTitle>{{ translations.location }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary mt-0.5">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <div>
                  <p class="font-medium">{{ property.address }}</p>
                  <p class="text-sm text-muted-foreground">{{ property.city }}, {{ property.zipCode }}</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <p class="text-sm">
                  <span class="font-medium">{{ property.distanceToBase }} km</span>
                  <span class="text-muted-foreground"> {{ translations.distanceToBase }}</span>
                </p>
              </div>
              <!-- Map Placeholder -->
              <div class="h-64 bg-muted rounded-lg flex items-center justify-center">
                <p class="text-sm text-muted-foreground">Map placeholder</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Property Info Card -->
        <Card>
          <CardHeader>
            <CardTitle class="text-2xl">
              €{{ property.rent.toLocaleString() }}
              <span class="text-base font-normal text-muted-foreground">/month</span>
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- Key Details -->
            <div class="grid grid-cols-3 gap-4 text-center">
              <div>
                <div class="text-2xl font-bold text-primary">{{ property.bedrooms }}</div>
                <div class="text-xs text-muted-foreground">{{ translations.bedrooms }}</div>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ property.bathrooms }}</div>
                <div class="text-xs text-muted-foreground">{{ translations.bathrooms }}</div>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ property.squareMeters }}</div>
                <div class="text-xs text-muted-foreground">m²</div>
              </div>
            </div>

            <div class="border-t border-border pt-4 space-y-3">
              <!-- Property Type -->
              <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.propertyType }}:</span>
                <span class="font-medium">{{ translations.types[property.type] }}</span>
              </div>

              <!-- Security Deposit -->
              <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.securityDeposit }}:</span>
                <span class="font-medium">€{{ property.deposit?.toLocaleString() || 'N/A' }}</span>
              </div>

              <!-- Furnished -->
              <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.furnished }}:</span>
                <span class="font-medium">{{ property.furnished === 'yes' ? translations.create.yes : translations.create.no }}</span>
              </div>

              <!-- Year Built -->
              <div v-if="property.yearBuilt" class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.yearBuilt }}:</span>
                <span class="font-medium">{{ property.yearBuilt }}</span>
              </div>

              <!-- Floor -->
              <div v-if="property.floor" class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.floor }}:</span>
                <span class="font-medium">{{ property.floor }}{{ property.totalFloors ? ` / ${property.totalFloors}` : '' }}</span>
              </div>

              <!-- Available From -->
              <div v-if="property.availableFrom" class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{ translations.availableFrom }}:</span>
                <span class="font-medium">{{ property.availableFrom }}</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-border pt-4 space-y-2">
              <!-- Tenant Actions -->
              <template v-if="isTenant">
                <Button class="w-full" size="lg">
                  {{ translations.requestViewing }}
                </Button>
                <Button variant="outline" class="w-full">
                  {{ translations.contactLandlord }}
                </Button>
              </template>

              <!-- Landlord Actions -->
              <template v-if="isLandlord">
                <Button class="w-full" size="lg">
                  {{ translations.edit }}
                </Button>
                <Button variant="destructive" class="w-full">
                  {{ translations.delete }}
                </Button>
              </template>

              <!-- HO Actions -->
              <template v-if="isHousingOffice && property.status === 'pending'">
                <Button class="w-full" size="lg" variant="success">
                  {{ translations.approve }}
                </Button>
                <Button variant="destructive" class="w-full">
                  {{ translations.reject }}
                </Button>
              </template>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div v-else class="flex items-center justify-center py-12">
    <p class="text-muted-foreground">Loading property...</p>
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
  middleware: 'auth',
})

const route = useRoute()
const { isLandlord, isTenant, isHousingOffice } = useAuth()
const { t, locale } = useI18n()

// Translations
const translations = ref({
  backToList: '',
  edit: '',
  delete: '',
  contactLandlord: '',
  requestViewing: '',
  approve: '',
  reject: '',
  description: '',
  amenities: '',
  location: '',
  bedrooms: '',
  bathrooms: '',
  propertyType: '',
  securityDeposit: '',
  furnished: '',
  yearBuilt: '',
  floor: '',
  availableFrom: '',
  distanceToBase: '',
  types: {
    apartment: '',
    house: '',
    villa: '',
    studio: '',
  },
  statuses: {
    available: '',
    pending: '',
    rented: '',
    unavailable: '',
    underReview: '',
  },
  create: {
    yes: '',
    no: '',
    parking: '',
    balcony: '',
    garden: '',
    airConditioning: '',
    heating: '',
    elevator: '',
    pets: '',
  },
})

const loadTranslations = () => {
  translations.value = {
    backToList: t('property.detail.backToList'),
    edit: t('property.detail.edit'),
    delete: t('property.detail.delete'),
    contactLandlord: t('property.detail.contactLandlord'),
    requestViewing: t('property.detail.requestViewing'),
    approve: t('property.detail.approve'),
    reject: t('property.detail.reject'),
    description: t('property.detail.description'),
    amenities: t('property.detail.amenities'),
    location: t('property.detail.location'),
    bedrooms: t('property.bedrooms'),
    bathrooms: t('property.bathrooms'),
    propertyType: t('property.detail.propertyType'),
    securityDeposit: t('property.detail.securityDeposit'),
    furnished: t('property.detail.furnished'),
    yearBuilt: t('property.detail.yearBuilt'),
    floor: t('property.detail.floor'),
    availableFrom: t('property.detail.availableFrom'),
    distanceToBase: t('property.detail.distanceToBase'),
    types: {
      apartment: t('property.types.apartment'),
      house: t('property.types.house'),
      villa: t('property.types.villa'),
      studio: t('property.types.studio'),
    },
    statuses: {
      available: t('property.statuses.available'),
      pending: t('property.statuses.pending'),
      rented: t('property.statuses.rented'),
      unavailable: t('property.statuses.unavailable'),
      underReview: t('property.statuses.underReview'),
    },
    create: {
      yes: t('property.create.yes'),
      no: t('property.create.no'),
      parking: t('property.create.parking'),
      balcony: t('property.create.balcony'),
      garden: t('property.create.garden'),
      airConditioning: t('property.create.airConditioning'),
      heating: t('property.create.heating'),
      elevator: t('property.create.elevator'),
      pets: t('property.create.pets'),
    },
  }
}

onMounted(() => {
  loadTranslations()
})

watch(locale, () => {
  loadTranslations()
})

// Mock property data (replace with API call later)
const property = ref({
  id: route.params.id,
  address: 'Via Roma, 123',
  city: 'Aviano',
  zipCode: '33081',
  rent: 1200,
  deposit: 2400,
  bedrooms: 3,
  bathrooms: 2,
  squareMeters: 120,
  distanceToBase: 2.5,
  status: 'available',
  type: 'apartment',
  image: null,
  floor: 2,
  totalFloors: 4,
  yearBuilt: 2015,
  furnished: 'no',
  availableFrom: '2025-01-01',
  description: 'Beautiful 3-bedroom apartment in the heart of Aviano, just 2.5km from Aviano Air Base. The property features modern amenities, spacious rooms, and excellent natural lighting. Located in a quiet residential area with easy access to schools, shops, and public transportation. Perfect for military families looking for comfortable off-base housing.',
  amenities: {
    parking: true,
    balcony: true,
    garden: false,
    airConditioning: true,
    heating: true,
    elevator: true,
    pets: false,
  },
})

// Methods
const getStatusVariant = (status: string) => {
  const variants: Record<string, any> = {
    available: 'success',
    pending: 'warning',
    rented: 'secondary',
    unavailable: 'destructive',
    underReview: 'default',
  }
  return variants[status] || 'default'
}
</script>
