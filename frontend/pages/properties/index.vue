<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Proprietà Disponibili
        </h1>
        <p class="text-gray-600">
          {{ totalProperties }} proprietà trovate
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h2 class="text-xl font-semibold mb-4">Filtri</h2>

            <form @submit.prevent="applyFilters" class="space-y-4">
              <!-- City -->
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                  Città
                </label>
                <input
                  id="city"
                  v-model="filters.city"
                  type="text"
                  placeholder="Aviano, Pordenone..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>

              <!-- Property Type -->
              <div>
                <label for="property_type" class="block text-sm font-medium text-gray-700 mb-1">
                  Tipo di proprietà
                </label>
                <select
                  id="property_type"
                  v-model="filters.property_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Tutti</option>
                  <option value="apartment">Appartamento</option>
                  <option value="house">Casa</option>
                  <option value="room">Stanza</option>
                </select>
              </div>

              <!-- Bedrooms -->
              <div>
                <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-1">
                  Numero di camere
                </label>
                <select
                  id="bedrooms"
                  v-model="filters.bedrooms"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Qualsiasi</option>
                  <option value="1">1+</option>
                  <option value="2">2+</option>
                  <option value="3">3+</option>
                  <option value="4">4+</option>
                </select>
              </div>

              <!-- Price Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Fascia di prezzo (€/mese)
                </label>
                <div class="grid grid-cols-2 gap-2">
                  <input
                    v-model.number="filters.min_rent"
                    type="number"
                    placeholder="Min"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <input
                    v-model.number="filters.max_rent"
                    type="number"
                    placeholder="Max"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>

              <!-- Checkboxes -->
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    v-model="filters.furnished"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                  />
                  <span class="text-sm text-gray-700">Arredato</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.pets_allowed"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                  />
                  <span class="text-sm text-gray-700">Animali ammessi</span>
                </label>
              </div>

              <!-- Buttons -->
              <div class="space-y-2 pt-4">
                <button
                  type="submit"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium"
                >
                  Applica Filtri
                </button>
                <button
                  type="button"
                  @click="resetFilters"
                  class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors font-medium"
                >
                  Reset
                </button>
              </div>
            </form>
          </div>
        </aside>

        <!-- Properties Grid -->
        <main class="lg:col-span-3">
          <!-- Loading State -->
          <div v-if="pending" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Caricamento proprietà...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <Icon name="heroicons:exclamation-triangle" class="mx-auto h-12 w-12 text-red-600 mb-2" />
            <p class="text-red-600">Errore nel caricamento delle proprietà.</p>
            <button
              @click="refresh"
              class="mt-4 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors"
            >
              Riprova
            </button>
          </div>

          <!-- Properties List -->
          <div v-else-if="properties && properties.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <PropertyCard
                v-for="property in properties"
                :key="property.id"
                :property="property"
              />
            </div>

            <!-- Pagination would go here -->
            <div v-if="totalProperties > properties.length" class="mt-8 text-center">
              <p class="text-gray-600">
                Showing {{ properties.length }} of {{ totalProperties }} properties
              </p>
              <!-- Add pagination component here in future -->
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
            <Icon name="heroicons:magnifying-glass" class="mx-auto h-16 w-16 text-gray-400 mb-4" />
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
              Nessuna proprietà trovata
            </h3>
            <p class="text-gray-600 mb-6">
              Prova a modificare i filtri di ricerca per trovare più risultati.
            </p>
            <button
              @click="resetFilters"
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium"
            >
              Reset Filtri
            </button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PropertySearchFilters } from '~/types/property'
import PropertyCard from '~/components/property/PropertyCard.vue'

// SEO
useHead({
  title: 'Proprietà Disponibili',
  meta: [
    {
      name: 'description',
      content: 'Esplora tutte le proprietà disponibili per l\'affitto ad Aviano e dintorni.',
    },
  ],
})

const route = useRoute()
const router = useRouter()

// Initialize filters from query params
const filters = ref<PropertySearchFilters>({
  city: (route.query.city as string) || '',
  property_type: (route.query.property_type as string) || '',
  bedrooms: route.query.bedrooms ? Number(route.query.bedrooms) : undefined,
  min_rent: route.query.min_rent ? Number(route.query.min_rent) : undefined,
  max_rent: route.query.max_rent ? Number(route.query.max_rent) : undefined,
  furnished: route.query.furnished === 'true' ? true : undefined,
  pets_allowed: route.query.pets_allowed === 'true' ? true : undefined,
})

// Fetch properties with current filters
const { getProperties } = useProperties()
const { data, pending, error, refresh } = await getProperties(filters.value)

const properties = computed(() => data.value?.data || [])
const totalProperties = computed(() => data.value?.meta?.total || properties.value.length || 0)

// Apply filters
const applyFilters = () => {
  const query = Object.fromEntries(
    Object.entries(filters.value).filter(([_, v]) => v !== undefined && v !== null && v !== '')
  )
  router.push({ query })
  refresh()
}

// Reset filters
const resetFilters = () => {
  filters.value = {
    city: '',
    property_type: '',
    bedrooms: undefined,
    min_rent: undefined,
    max_rent: undefined,
    furnished: undefined,
    pets_allowed: undefined,
  }
  router.push({ query: {} })
  refresh()
}

// Watch for route changes (e.g., from homepage search)
watch(() => route.query, (newQuery) => {
  filters.value = {
    city: (newQuery.city as string) || '',
    property_type: (newQuery.property_type as string) || '',
    bedrooms: newQuery.bedrooms ? Number(newQuery.bedrooms) : undefined,
    min_rent: newQuery.min_rent ? Number(newQuery.min_rent) : undefined,
    max_rent: newQuery.max_rent ? Number(newQuery.max_rent) : undefined,
    furnished: newQuery.furnished === 'true' ? true : undefined,
    pets_allowed: newQuery.pets_allowed === 'true' ? true : undefined,
  }
  refresh()
}, { deep: true })
</script>
