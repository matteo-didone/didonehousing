<template>
  <NuxtLink
    :to="`/properties/${property.id}`"
    class="block bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300"
  >
    <!-- Property Image -->
    <div class="relative h-48 bg-gray-200">
      <img
        v-if="primaryPhoto"
        :src="primaryPhoto"
        :alt="property.title"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
        <Icon name="heroicons:home" class="w-16 h-16" />
      </div>

      <!-- Status Badge -->
      <div class="absolute top-2 right-2">
        <span
          :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            property.status === 'available' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white'
          ]"
        >
          {{ property.status === 'available' ? 'Disponibile' : 'Non disponibile' }}
        </span>
      </div>

      <!-- Property Type Badge -->
      <div class="absolute top-2 left-2">
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500 text-white">
          {{ propertyTypeLabel }}
        </span>
      </div>
    </div>

    <!-- Property Details -->
    <div class="p-4">
      <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-1">
        {{ property.title }}
      </h3>

      <div class="flex items-center text-gray-600 mb-3">
        <Icon name="heroicons:map-pin" class="w-4 h-4 mr-1" />
        <span class="text-sm">{{ property.city }}</span>
      </div>

      <p class="text-gray-600 text-sm mb-4 line-clamp-2">
        {{ property.description }}
      </p>

      <!-- Property Features -->
      <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
        <div class="flex items-center">
          <Icon name="heroicons:home" class="w-4 h-4 mr-1" />
          <span>{{ property.bedrooms }} camere</span>
        </div>
        <div class="flex items-center">
          <Icon name="heroicons:squares-2x2" class="w-4 h-4 mr-1" />
          <span>{{ property.square_meters }} m²</span>
        </div>
      </div>

      <!-- Additional Features Icons -->
      <div class="flex items-center gap-3 mb-4">
        <span
          v-if="property.furnished"
          class="flex items-center text-xs text-gray-600"
          title="Arredato"
        >
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-500 mr-1" />
          Arredato
        </span>
        <span
          v-if="property.pets_allowed"
          class="flex items-center text-xs text-gray-600"
          title="Animali ammessi"
        >
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-500 mr-1" />
          Animali OK
        </span>
        <span
          v-if="property.utilities_included"
          class="flex items-center text-xs text-gray-600"
          title="Utenze incluse"
        >
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-500 mr-1" />
          Utenze incluse
        </span>
      </div>

      <!-- Price -->
      <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <div>
          <span class="text-2xl font-bold text-blue-600">
            €{{ formatPrice(property.monthly_rent) }}
          </span>
          <span class="text-gray-600 text-sm">/mese</span>
        </div>
        <span class="text-sm text-gray-500">
          Disponibile dal {{ formatDate(property.available_from) }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Property } from '~/types/property'

interface Props {
  property: Property
}

const props = defineProps<Props>()

const primaryPhoto = computed(() => {
  if (!props.property.photos || props.property.photos.length === 0) {
    return null
  }
  const primary = props.property.photos.find(photo => photo.is_primary)
  return primary ? `/storage/${primary.file_path}` : `/storage/${props.property.photos[0].file_path}`
})

const propertyTypeLabel = computed(() => {
  const types: Record<string, string> = {
    apartment: 'Appartamento',
    house: 'Casa',
    room: 'Stanza'
  }
  return types[props.property.property_type] || props.property.property_type
})

const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('it-IT').format(price)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>
