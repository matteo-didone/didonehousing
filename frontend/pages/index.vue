<template>
  <div>
    <!-- Hero Section with Search -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Trova la tua casa ideale ad Aviano
          </h1>
          <p class="text-xl mb-8">
            La piattaforma di riferimento per affitti residenziali ad Aviano e dintorni
          </p>

          <!-- Search Box -->
          <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-6">
              <form @submit.prevent="handleSearch" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- City -->
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                    Città
                  </label>
                  <input
                    id="city"
                    v-model="searchFilters.city"
                    type="text"
                    placeholder="Aviano, Pordenone..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                  />
                </div>

                <!-- Property Type -->
                <div>
                  <label for="property_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo
                  </label>
                  <select
                    id="property_type"
                    v-model="searchFilters.property_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
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
                    Camere
                  </label>
                  <select
                    id="bedrooms"
                    v-model="searchFilters.bedrooms"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                  >
                    <option value="">Qualsiasi</option>
                    <option value="1">1+</option>
                    <option value="2">2+</option>
                    <option value="3">3+</option>
                    <option value="4">4+</option>
                  </select>
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                  <button
                    type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium"
                  >
                    Cerca
                  </button>
                </div>
              </form>

              <!-- Advanced Filters Toggle -->
              <div class="mt-4">
                <button
                  @click="showAdvancedFilters = !showAdvancedFilters"
                  class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                >
                  {{ showAdvancedFilters ? 'Nascondi' : 'Mostra' }} filtri avanzati
                </button>
              </div>

              <!-- Advanced Filters -->
              <div v-if="showAdvancedFilters" class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Min Rent -->
                  <div>
                    <label for="min_rent" class="block text-sm font-medium text-gray-700 mb-1">
                      Affitto min (€)
                    </label>
                    <input
                      id="min_rent"
                      v-model.number="searchFilters.min_rent"
                      type="number"
                      placeholder="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                    />
                  </div>

                  <!-- Max Rent -->
                  <div>
                    <label for="max_rent" class="block text-sm font-medium text-gray-700 mb-1">
                      Affitto max (€)
                    </label>
                    <input
                      id="max_rent"
                      v-model.number="searchFilters.max_rent"
                      type="number"
                      placeholder="2000"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                    />
                  </div>

                  <!-- Checkboxes -->
                  <div class="flex flex-col justify-center space-y-2">
                    <label class="flex items-center text-gray-700">
                      <input
                        v-model="searchFilters.furnished"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                      />
                      <span class="text-sm">Arredato</span>
                    </label>
                    <label class="flex items-center text-gray-700">
                      <input
                        v-model="searchFilters.pets_allowed"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                      />
                      <span class="text-sm">Animali ammessi</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Properties Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Proprietà in Evidenza
          </h2>
          <p class="text-gray-600">
            Scopri le migliori opportunità disponibili ora
          </p>
        </div>

        <!-- Loading State -->
        <div v-if="pending" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          <p class="mt-4 text-gray-600">Caricamento proprietà...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">Errore nel caricamento delle proprietà. Riprova più tardi.</p>
        </div>

        <!-- Properties Grid -->
        <div v-else-if="properties && properties.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <PropertyCard
            v-for="property in properties"
            :key="property.id"
            :property="property"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <Icon name="heroicons:home" class="mx-auto h-16 w-16 text-gray-400 mb-4" />
          <p class="text-gray-600">Nessuna proprietà disponibile al momento.</p>
        </div>

        <!-- View All Button -->
        <div v-if="properties && properties.length > 0" class="text-center mt-12">
          <NuxtLink
            to="/properties"
            class="inline-block bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium"
          >
            Vedi tutte le proprietà
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Perché scegliere Aviano Housing?
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
              <Icon name="heroicons:magnifying-glass" class="w-8 h-8 text-blue-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Ricerca Facile</h3>
            <p class="text-gray-600">
              Trova la casa perfetta con i nostri filtri di ricerca avanzati
            </p>
          </div>

          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
              <Icon name="heroicons:shield-check" class="w-8 h-8 text-blue-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Sicuro e Affidabile</h3>
            <p class="text-gray-600">
              Tutti i proprietari e le proprietà sono verificati
            </p>
          </div>

          <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
              <Icon name="heroicons:chat-bubble-left-right" class="w-8 h-8 text-blue-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Supporto Dedicato</h3>
            <p class="text-gray-600">
              Il nostro team è sempre pronto ad aiutarti
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import type { PropertySearchFilters } from '~/types/property'
import PropertyCard from '~/components/property/PropertyCard.vue'

// SEO
useHead({
  title: 'Home',
  meta: [
    {
      name: 'description',
      content: 'Trova la tua casa ideale ad Aviano. Piattaforma di riferimento per affitti residenziali.',
    },
  ],
})

// Search filters
const searchFilters = ref<PropertySearchFilters>({
  city: '',
  property_type: '',
  bedrooms: undefined,
  min_rent: undefined,
  max_rent: undefined,
  furnished: undefined,
  pets_allowed: undefined,
})

const showAdvancedFilters = ref(false)

// Fetch featured properties (first 6 available properties)
const { getProperties } = useProperties()
const { data, pending, error } = await getProperties({ status: 'available' })

const properties = computed(() => {
  return data.value?.data?.slice(0, 6) || []
})

// Handle search
const handleSearch = () => {
  // Redirect to properties page with filters
  const query = Object.fromEntries(
    Object.entries(searchFilters.value).filter(([_, v]) => v !== undefined && v !== null && v !== '')
  )
  navigateTo({
    path: '/properties',
    query,
  })
}
</script>
