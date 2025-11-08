<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
        <p class="mt-2 text-muted-foreground">
          {{ isLandlord ? translations.subtitle.landlord : translations.subtitle.tenant }}
        </p>
      </div>
      <NuxtLink v-if="isLandlord" to="/properties/create">
        <Button size="lg">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
            <path d="M5 12h14" />
            <path d="M12 5v14" />
          </svg>
          {{ translations.addNew }}
        </Button>
      </NuxtLink>
    </div>

    <!-- Search & Sort Bar -->
    <div class="flex flex-col gap-3 sm:flex-row">
      <div class="relative flex-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>
        <Input
          v-model="searchQuery"
          :placeholder="translations.searchPlaceholder"
          class="pl-10"
        />
      </div>
      <Select v-model="sortBy" class="w-full sm:w-[200px]">
        <option value="">{{ translations.sortBy }}</option>
        <option value="priceAsc">{{ translations.sortOptions.priceAsc }}</option>
        <option value="priceDesc">{{ translations.sortOptions.priceDesc }}</option>
        <option value="newest">{{ translations.sortOptions.newest }}</option>
        <option value="distanceAsc">{{ translations.sortOptions.distanceAsc }}</option>
      </Select>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
      <!-- Filters Sidebar -->
      <aside class="lg:col-span-1">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center justify-between">
              <span>{{ translations.filters.title }}</span>
              <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="text-sm font-normal text-primary hover:underline"
              >
                {{ translations.filters.clearAll }}
              </button>
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- Property Type -->
            <div class="space-y-2">
              <Label>{{ translations.filters.propertyType }}</Label>
              <Select v-model="filters.type">
                <option value="">{{ t('common.allTypes') }}</option>
                <option value="apartment">{{ translations.types.apartment }}</option>
                <option value="house">{{ translations.types.house }}</option>
                <option value="villa">{{ translations.types.villa }}</option>
                <option value="studio">{{ translations.types.studio }}</option>
              </Select>
            </div>

            <!-- Price Range -->
            <div class="space-y-2">
              <Label>{{ translations.filters.priceRange }}</Label>
              <div class="grid grid-cols-2 gap-2">
                <Input
                  v-model.number="filters.priceMin"
                  type="number"
                  :placeholder="translations.filters.priceMin"
                  min="0"
                />
                <Input
                  v-model.number="filters.priceMax"
                  type="number"
                  :placeholder="translations.filters.priceMax"
                  min="0"
                />
              </div>
            </div>

            <!-- Bedrooms -->
            <div class="space-y-2">
              <Label>{{ translations.filters.bedroomsMin }}</Label>
              <Select v-model="filters.bedrooms">
                <option value="">{{ t('common.any') }}</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="4">4+</option>
              </Select>
            </div>

            <!-- Bathrooms -->
            <div class="space-y-2">
              <Label>{{ translations.filters.bathroomsMin }}</Label>
              <Select v-model="filters.bathrooms">
                <option value="">{{ t('common.any') }}</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
              </Select>
            </div>

            <!-- Max Distance -->
            <div class="space-y-2">
              <Label>{{ translations.filters.maxDistance }} (km)</Label>
              <Input
                v-model.number="filters.maxDistance"
                type="number"
                placeholder="Max km"
                min="0"
              />
            </div>
          </CardContent>
        </Card>
      </aside>

      <!-- Properties Grid -->
      <div class="lg:col-span-3">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <p class="text-muted-foreground">{{ translations.loading }}</p>
        </div>

        <!-- No Results -->
        <div v-else-if="filteredProperties.length === 0" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-muted mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
              <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold">{{ translations.noResults }}</h3>
          <p class="mt-2 text-sm text-muted-foreground">{{ translations.noResultsDescription }}</p>
        </div>

        <!-- Properties Grid -->
        <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <Card
            v-for="property in filteredProperties"
            :key="property.id"
            class="overflow-hidden transition-all hover:shadow-lg cursor-pointer"
            @click="navigateTo(`/properties/${property.id}`)"
          >
            <!-- Property Image -->
            <div class="relative h-48 bg-muted">
              <img
                v-if="property.image"
                :src="property.image"
                :alt="property.address"
                class="h-full w-full object-cover"
              />
              <div v-else class="flex h-full items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                  <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
              </div>
              <!-- Status Badge -->
              <div class="absolute top-3 right-3">
                <Badge :variant="getStatusVariant(property.status)">
                  {{ translations.statuses[property.status] }}
                </Badge>
              </div>
            </div>

            <CardHeader>
              <CardTitle class="line-clamp-1">{{ property.address }}</CardTitle>
              <CardDescription class="line-clamp-1">{{ property.city }}</CardDescription>
            </CardHeader>

            <CardContent class="space-y-3">
              <!-- Price -->
              <div class="text-2xl font-bold text-primary">
                €{{ property.rent.toLocaleString() }}<span class="text-sm font-normal text-muted-foreground">{{ translations.perMonth }}</span>
              </div>

              <!-- Property Details -->
              <div class="flex items-center gap-4 text-sm text-muted-foreground">
                <div class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  </svg>
                  <span>{{ property.bedrooms }} {{ translations.bedrooms }}</span>
                </div>
                <div class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1 0l-1 1a1.5 1.5 0 0 0 0 1L7 9" />
                    <path d="m15 6 2.5-2.5a1.5 1.5 0 0 1 1 0l1 1a1.5 1.5 0 0 1 0 1L17 9" />
                    <path d="M9 18 6.5 20.5a1.5 1.5 0 0 1-1 0l-1-1a1.5 1.5 0 0 1 0-1L7 15" />
                    <path d="m15 18 2.5 2.5a1.5 1.5 0 0 0 1 0l1-1a1.5 1.5 0 0 0 0-1L17 15" />
                  </svg>
                  <span>{{ property.bathrooms }} {{ translations.bathrooms }}</span>
                </div>
                <div class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                  </svg>
                  <span>{{ property.squareMeters }}{{ t('units.squareMeters') }}</span>
                </div>
              </div>

              <!-- Distance to Base -->
              <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <span>{{ property.distanceToBase }} {{ translations.kmFromBase }}</span>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'
import Badge from '@/components/ui/Badge.vue'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
})

const { isLandlord } = useAuth()
const { t } = useI18n()

// Translations
const translations = ref({
  title: '',
  subtitle: {
    landlord: '',
    tenant: '',
  },
  searchPlaceholder: '',
  addNew: '',
  sortBy: '',
  loading: '',
  noResults: '',
  noResultsDescription: '',
  perMonth: '',
  kmFromBase: '',
  bedrooms: '',
  bathrooms: '',
  filters: {
    title: '',
    propertyType: '',
    priceRange: '',
    priceMin: '',
    priceMax: '',
    bedroomsMin: '',
    bathroomsMin: '',
    maxDistance: '',
    clearAll: '',
  },
  sortOptions: {
    priceAsc: '',
    priceDesc: '',
    newest: '',
    distanceAsc: '',
  },
  statuses: {
    available: '',
    pending: '',
    rented: '',
    unavailable: '',
    underReview: '',
  },
  types: {
    apartment: '',
    house: '',
    villa: '',
    studio: '',
  },
})

onMounted(() => {
  translations.value = {
    title: t('property.title'),
    subtitle: {
      landlord: t('property.subtitle.landlord'),
      tenant: t('property.subtitle.tenant'),
    },
    searchPlaceholder: t('property.searchPlaceholder'),
    addNew: t('property.addNew'),
    sortBy: t('property.sortBy'),
    loading: t('property.loading'),
    noResults: t('property.noResults'),
    noResultsDescription: t('property.noResultsDescription'),
    perMonth: t('property.perMonth'),
    kmFromBase: t('property.kmFromBase'),
    bedrooms: t('property.bedrooms'),
    bathrooms: t('property.bathrooms'),
    filters: {
      title: t('property.filters.title'),
      propertyType: t('property.filters.propertyType'),
      priceRange: t('property.filters.priceRange'),
      priceMin: t('property.filters.priceMin'),
      priceMax: t('property.filters.priceMax'),
      bedroomsMin: t('property.filters.bedroomsMin'),
      bathroomsMin: t('property.filters.bathroomsMin'),
      maxDistance: t('property.filters.maxDistance'),
      clearAll: t('property.filters.clearAll'),
    },
    sortOptions: {
      priceAsc: t('property.sortOptions.priceAsc'),
      priceDesc: t('property.sortOptions.priceDesc'),
      newest: t('property.sortOptions.newest'),
      distanceAsc: t('property.sortOptions.distanceAsc'),
    },
    statuses: {
      available: t('property.statuses.available'),
      pending: t('property.statuses.pending'),
      rented: t('property.statuses.rented'),
      unavailable: t('property.statuses.unavailable'),
      underReview: t('property.statuses.underReview'),
    },
    types: {
      apartment: t('property.types.apartment'),
      house: t('property.types.house'),
      villa: t('property.types.villa'),
      studio: t('property.types.studio'),
    },
  }
})

// State
const loading = ref(false)
const searchQuery = ref('')
const sortBy = ref('')
const filters = ref({
  type: '',
  priceMin: null as number | null,
  priceMax: null as number | null,
  bedrooms: '',
  bathrooms: '',
  maxDistance: null as number | null,
})

// Mock data (replace with API call later)
const properties = ref([
  {
    id: 1,
    address: 'Via Roma, 123',
    city: 'Aviano',
    rent: 1200,
    bedrooms: 3,
    bathrooms: 2,
    squareMeters: 120,
    distanceToBase: 2.5,
    status: 'available',
    type: 'apartment',
    image: null,
  },
  {
    id: 2,
    address: 'Via Giuseppe Verdi, 45',
    city: 'Pordenone',
    rent: 1500,
    bedrooms: 4,
    bathrooms: 2,
    squareMeters: 150,
    distanceToBase: 8.3,
    status: 'available',
    type: 'house',
    image: null,
  },
  {
    id: 3,
    address: 'Corso Italia, 67',
    city: 'Roveredo in Piano',
    rent: 900,
    bedrooms: 2,
    bathrooms: 1,
    squareMeters: 80,
    distanceToBase: 5.1,
    status: 'pending',
    type: 'apartment',
    image: null,
  },
  {
    id: 4,
    address: 'Via Dante Alighieri, 89',
    city: 'Aviano',
    rent: 1800,
    bedrooms: 5,
    bathrooms: 3,
    squareMeters: 200,
    distanceToBase: 3.2,
    status: 'available',
    type: 'villa',
    image: null,
  },
  {
    id: 5,
    address: 'Via Mazzini, 12',
    city: 'Pordenone',
    rent: 650,
    bedrooms: 1,
    bathrooms: 1,
    squareMeters: 45,
    distanceToBase: 9.5,
    status: 'rented',
    type: 'studio',
    image: null,
  },
])

// Computed
const hasActiveFilters = computed(() => {
  return !!(
    filters.value.type ||
    filters.value.priceMin ||
    filters.value.priceMax ||
    filters.value.bedrooms ||
    filters.value.bathrooms ||
    filters.value.maxDistance
  )
})

const filteredProperties = computed(() => {
  let result = properties.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(
      (p) =>
        p.address.toLowerCase().includes(query) ||
        p.city.toLowerCase().includes(query)
    )
  }

  // Type filter
  if (filters.value.type) {
    result = result.filter((p) => p.type === filters.value.type)
  }

  // Price range filter
  if (filters.value.priceMin) {
    result = result.filter((p) => p.rent >= (filters.value.priceMin || 0))
  }
  if (filters.value.priceMax) {
    result = result.filter((p) => p.rent <= (filters.value.priceMax || Infinity))
  }

  // Bedrooms filter
  if (filters.value.bedrooms) {
    result = result.filter((p) => p.bedrooms >= parseInt(filters.value.bedrooms))
  }

  // Bathrooms filter
  if (filters.value.bathrooms) {
    result = result.filter((p) => p.bathrooms >= parseInt(filters.value.bathrooms))
  }

  // Max distance filter
  if (filters.value.maxDistance) {
    result = result.filter((p) => p.distanceToBase <= (filters.value.maxDistance || Infinity))
  }

  // Sorting
  if (sortBy.value === 'priceAsc') {
    result = [...result].sort((a, b) => a.rent - b.rent)
  } else if (sortBy.value === 'priceDesc') {
    result = [...result].sort((a, b) => b.rent - a.rent)
  } else if (sortBy.value === 'distanceAsc') {
    result = [...result].sort((a, b) => a.distanceToBase - b.distanceToBase)
  }

  return result
})

// Methods
const clearFilters = () => {
  filters.value = {
    type: '',
    priceMin: null,
    priceMax: null,
    bedrooms: '',
    bathrooms: '',
    maxDistance: null,
  }
}

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
