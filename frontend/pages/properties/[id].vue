<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="pending" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      <p class="mt-4 text-gray-600">Caricamento proprietà...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <Icon name="heroicons:exclamation-triangle" class="mx-auto h-12 w-12 text-red-600 mb-2" />
        <p class="text-red-600 mb-4">Errore nel caricamento della proprietà.</p>
        <NuxtLink
          to="/properties"
          class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
        >
          Torna alle proprietà
        </NuxtLink>
      </div>
    </div>

    <!-- Property Detail -->
    <div v-else-if="property" class="pb-12">
      <!-- Image Gallery -->
      <div class="bg-gray-900 mb-8">
        <div class="max-w-7xl mx-auto">
          <div class="relative h-96 md:h-[500px]">
            <img
              v-if="currentPhoto"
              :src="currentPhoto"
              :alt="property.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <Icon name="heroicons:home" class="w-32 h-32" />
            </div>

            <!-- Photo Navigation -->
            <div v-if="property.photos && property.photos.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
              <div class="flex gap-2">
                <button
                  v-for="(photo, index) in property.photos"
                  :key="photo.id"
                  @click="currentPhotoIndex = index"
                  :class="[
                    'w-3 h-3 rounded-full transition-colors',
                    index === currentPhotoIndex ? 'bg-white' : 'bg-white/50 hover:bg-white/75'
                  ]"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ property.title }}
                  </h1>
                  <div class="flex items-center text-gray-600">
                    <Icon name="heroicons:map-pin" class="w-5 h-5 mr-1" />
                    <span>{{ property.address }}, {{ property.city }} {{ property.zip_code }}</span>
                  </div>
                </div>
                <span
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-semibold',
                    property.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ property.status === 'available' ? 'Disponibile' : 'Non disponibile' }}
                </span>
              </div>

              <div class="flex items-center gap-6 text-gray-700 border-t pt-4">
                <div class="flex items-center">
                  <Icon name="heroicons:home" class="w-5 h-5 mr-2 text-blue-600" />
                  <span class="font-medium">{{ property.bedrooms }} Camere</span>
                </div>
                <div class="flex items-center">
                  <Icon name="heroicons:squares-2x2" class="w-5 h-5 mr-2 text-blue-600" />
                  <span class="font-medium">{{ property.square_meters }} m²</span>
                </div>
                <div class="flex items-center">
                  <Icon name="heroicons:home-modern" class="w-5 h-5 mr-2 text-blue-600" />
                  <span class="font-medium">{{ propertyTypeLabel }}</span>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <h2 class="text-xl font-semibold mb-4">Descrizione</h2>
              <p class="text-gray-700 whitespace-pre-line">{{ property.description }}</p>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <h2 class="text-xl font-semibold mb-4">Caratteristiche</h2>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="flex items-center">
                  <Icon
                    :name="property.furnished ? 'heroicons:check-circle' : 'heroicons:x-circle'"
                    :class="['w-5 h-5 mr-2', property.furnished ? 'text-green-500' : 'text-red-500']"
                  />
                  <span class="text-gray-700">{{ property.furnished ? 'Arredato' : 'Non arredato' }}</span>
                </div>
                <div class="flex items-center">
                  <Icon
                    :name="property.pets_allowed ? 'heroicons:check-circle' : 'heroicons:x-circle'"
                    :class="['w-5 h-5 mr-2', property.pets_allowed ? 'text-green-500' : 'text-red-500']"
                  />
                  <span class="text-gray-700">{{ property.pets_allowed ? 'Animali ammessi' : 'Animali non ammessi' }}</span>
                </div>
                <div class="flex items-center">
                  <Icon
                    :name="property.smoking_allowed ? 'heroicons:check-circle' : 'heroicons:x-circle'"
                    :class="['w-5 h-5 mr-2', property.smoking_allowed ? 'text-green-500' : 'text-red-500']"
                  />
                  <span class="text-gray-700">{{ property.smoking_allowed ? 'Fumatori ammessi' : 'Non fumatori' }}</span>
                </div>
                <div class="flex items-center">
                  <Icon
                    :name="property.utilities_included ? 'heroicons:check-circle' : 'heroicons:x-circle'"
                    :class="['w-5 h-5 mr-2', property.utilities_included ? 'text-green-500' : 'text-red-500']"
                  />
                  <span class="text-gray-700">{{ property.utilities_included ? 'Utenze incluse' : 'Utenze escluse' }}</span>
                </div>
                <div class="flex items-center">
                  <Icon name="heroicons:calendar" class="w-5 h-5 mr-2 text-blue-600" />
                  <span class="text-gray-700">{{ property.bathrooms }} Bagni</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Price Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
              <div class="mb-6">
                <div class="text-4xl font-bold text-blue-600 mb-2">
                  €{{ formatPrice(property.monthly_rent) }}
                  <span class="text-lg text-gray-600 font-normal">/mese</span>
                </div>
                <div class="text-gray-600">
                  Deposito: €{{ formatPrice(property.deposit) }}
                </div>
                <div class="text-sm text-gray-600 mt-2">
                  Disponibile dal: {{ formatDate(property.available_from) }}
                </div>
              </div>

              <!-- Contact Form -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold">Contatta il proprietario</h3>
                <form @submit.prevent="handleContactSubmit" class="space-y-4">
                  <div>
                    <input
                      v-model="contactForm.name"
                      type="text"
                      placeholder="Nome"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  <div>
                    <input
                      v-model="contactForm.email"
                      type="email"
                      placeholder="Email"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  <div>
                    <textarea
                      v-model="contactForm.message"
                      placeholder="Messaggio"
                      rows="4"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                  </div>
                  <button
                    type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium"
                  >
                    Invia Messaggio
                  </button>
                </form>
              </div>
            </div>

            <!-- Owner Info (if available) -->
            <div v-if="property.owner" class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-lg font-semibold mb-4">Proprietario</h3>
              <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg mr-3">
                  {{ property.owner.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ property.owner.name }}</p>
                  <p class="text-sm text-gray-600">Membro dal {{ new Date(property.created_at).getFullYear() }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Property } from '~/types/property'

const route = useRoute()
const propertyId = route.params.id as string

// Fetch property
const { getProperty } = useProperties()
const { data, pending, error } = await getProperty(propertyId)

const property = computed(() => data.value?.data)

// SEO
useHead({
  title: property.value?.title || 'Proprietà',
  meta: [
    {
      name: 'description',
      content: property.value?.description || 'Dettagli proprietà',
    },
  ],
})

// Photo gallery
const currentPhotoIndex = ref(0)
const currentPhoto = computed(() => {
  if (!property.value?.photos || property.value.photos.length === 0) {
    return null
  }
  return `/storage/${property.value.photos[currentPhotoIndex.value].file_path}`
})

// Property type label
const propertyTypeLabel = computed(() => {
  const types: Record<string, string> = {
    apartment: 'Appartamento',
    house: 'Casa',
    room: 'Stanza'
  }
  return property.value ? types[property.value.property_type] : ''
})

// Contact form
const contactForm = ref({
  name: '',
  email: '',
  message: `Ciao, sono interessato alla proprietà "${property.value?.title}". Potresti fornirmi maggiori informazioni?`
})

const handleContactSubmit = () => {
  // TODO: Implement contact submission
  alert('Funzionalità di contatto in arrivo! Per ora questa è solo una demo.')
  console.log('Contact form submitted:', contactForm.value)
}

// Utility functions
const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('it-IT').format(price)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>
