<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <NuxtLink to="/properties" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
        {{ t('property.detail.backToList') }}
      </NuxtLink>
      <h1 class="text-3xl font-bold tracking-tight">{{ t('property.create.title') }}</h1>
      <p class="mt-2 text-muted-foreground">{{ t('property.create.subtitle') }}</p>
    </div>

    <!-- Error Alert -->
    <Card v-if="error" class="border-destructive bg-destructive/10">
      <CardContent class="p-4">
        <div class="flex items-start gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-destructive mt-0.5">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" x2="12" y1="8" y2="12" />
            <line x1="12" x2="12.01" y1="16" y2="16" />
          </svg>
          <div class="flex-1">
            <p class="font-medium text-destructive">{{ t('common.error') }}</p>
            <p class="text-sm text-destructive mt-1">{{ error }}</p>
          </div>
        </div>
      </CardContent>
    </Card>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Address Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.create.location') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <!-- Google Maps Autocomplete -->
          <div class="space-y-2">
            <Label for="address_search">
              {{ t('property.create.searchAddress') || 'Search Address' }}
              <span v-if="isLoading" class="ml-2 text-xs text-muted-foreground">(Loading...)</span>
            </Label>
            <input
              id="address_search"
              ref="addressInput"
              v-model="autocompleteSearch"
              :placeholder="t('property.create.searchAddressPlaceholder') || 'Start typing an address in Italy...'"
              :disabled="!isLoaded"
              type="text"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            />
            <p class="text-xs text-muted-foreground">
              {{ t('property.create.searchAddressHint') || 'Use autocomplete to fill address fields automatically' }}
            </p>
            <!-- Distance Badge -->
            <div v-if="form.distance_from_base_km" class="flex items-center gap-2 mt-2">
              <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                {{ form.distance_from_base_km }} km from Aviano AB
              </span>
            </div>
          </div>

          <div class="border-t border-border pt-4">
            <p class="text-sm font-medium text-muted-foreground mb-3">{{ t('property.create.manualEntry') || 'Or enter manually:' }}</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div class="sm:col-span-2 space-y-2">
                <Label for="street_name">{{ t('property.address') }} *</Label>
                <Input
                  id="street_name"
                  v-model="form.street_name"
                  required
                  :placeholder="t('property.create.addressPlaceholder')"
                />
              </div>
              <div class="space-y-2">
                <Label for="house_number">{{ t('property.create.houseNumber') }} *</Label>
                <Input
                  id="house_number"
                  v-model="form.house_number"
                  required
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="apt_number">{{ t('property.create.aptNumber') }}</Label>
              <Input
                id="apt_number"
                v-model="form.apt_number"
                placeholder="A, 3, etc."
              />
            </div>
            <div class="space-y-2">
              <Label for="postal_code">{{ t('property.create.zipCode') }} *</Label>
              <Input
                id="postal_code"
                v-model="form.postal_code"
                required
                placeholder="33081"
              />
            </div>
            <div class="space-y-2">
              <Label for="city">{{ t('property.create.city') }} *</Label>
              <Input
                id="city"
                v-model="form.city"
                required
                :placeholder="t('property.create.cityPlaceholder')"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="province">{{ t('property.create.province') }} *</Label>
              <Input
                id="province"
                v-model="form.province"
                required
                maxlength="2"
                placeholder="PN"
              />
              <p class="text-xs text-muted-foreground">{{ t('property.create.provinceHint') }}</p>
            </div>
            <div class="space-y-2">
              <Label for="country">{{ t('property.create.country') }} *</Label>
              <Input
                id="country"
                v-model="form.country"
                required
                maxlength="2"
                placeholder="IT"
                value="IT"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Property Details Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.create.propertyDetails') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="space-y-2">
              <Label for="bedrooms">{{ t('property.bedrooms') }} *</Label>
              <Input
                id="bedrooms"
                v-model.number="form.bedrooms"
                type="number"
                min="1"
                max="10"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="bathrooms">{{ t('property.bathrooms') }} *</Label>
              <Input
                id="bathrooms"
                v-model.number="form.bathrooms"
                type="number"
                min="1"
                max="10"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="living_rooms">{{ t('property.create.livingRooms') }}</Label>
              <Input
                id="living_rooms"
                v-model.number="form.living_rooms"
                type="number"
                min="0"
                max="10"
              />
            </div>
            <div class="space-y-2">
              <Label for="dining_rooms">{{ t('property.create.diningRooms') }}</Label>
              <Input
                id="dining_rooms"
                v-model.number="form.dining_rooms"
                type="number"
                min="0"
                max="10"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="kitchen">{{ t('property.create.kitchen') }}</Label>
              <Input
                id="kitchen"
                v-model.number="form.kitchen"
                type="number"
                min="0"
                max="10"
              />
            </div>
            <div class="space-y-2">
              <Label for="heating_type">{{ t('property.create.heatingType') }}</Label>
              <Select v-model="form.heating_type">
                <option value="">{{ t('common.select') }}...</option>
                <option value="city_gas">{{ t('property.heating.cityGas') }}</option>
                <option value="lpg_coupons">{{ t('property.heating.lpgCoupons') }}</option>
                <option value="lpg_no_coupons">{{ t('property.heating.lpgNoCoupons') }}</option>
                <option value="fuel">{{ t('property.heating.fuel') }}</option>
                <option value="electric">{{ t('property.heating.electric') }}</option>
                <option value="separate_system">{{ t('property.heating.separateSystem') }}</option>
              </Select>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Amenities Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.create.amenities') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Furnished -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="furnished" class="font-medium">{{ t('property.create.furnished') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.furnishedDesc') }}</p>
              </div>
              <input
                id="furnished"
                type="checkbox"
                v-model="form.furnished"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Pets Allowed -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="pets_allowed" class="font-medium">{{ t('property.create.pets') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.petsDesc') }}</p>
              </div>
              <input
                id="pets_allowed"
                type="checkbox"
                v-model="form.pets_allowed"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Basement -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="basement" class="font-medium">{{ t('property.create.basement') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.basementDesc') }}</p>
              </div>
              <input
                id="basement"
                type="checkbox"
                v-model="form.basement"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Attic -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="attic" class="font-medium">{{ t('property.create.attic') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.atticDesc') }}</p>
              </div>
              <input
                id="attic"
                type="checkbox"
                v-model="form.attic"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Garage -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="garage" class="font-medium">{{ t('property.create.garage') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.garageDesc') }}</p>
              </div>
              <input
                id="garage"
                type="checkbox"
                v-model="form.garage"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Yard -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="yard" class="font-medium">{{ t('property.create.yard') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.yardDesc') }}</p>
              </div>
              <input
                id="yard"
                type="checkbox"
                v-model="form.yard"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Actions -->
      <div class="flex items-center justify-between gap-4">
        <NuxtLink to="/properties">
          <Button type="button" variant="outline">
            {{ t('property.create.cancel') }}
          </Button>
        </NuxtLink>
        <Button type="submit" :disabled="submitting">
          <svg v-if="submitting" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 animate-spin">
            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
          </svg>
          {{ submitting ? t('property.create.submitting') : t('property.create.submit') }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'landlord'],
})

const { t } = useI18n()
const { createProperty } = useProperty()
const { loadGoogleMaps, initAutocomplete, isLoaded, isLoading } = useGoogleMaps()

const form = reactive({
  // Address
  street_name: '',
  house_number: '',
  apt_number: '',
  city: '',
  province: '',
  postal_code: '',
  country: 'IT',
  // Google Maps data
  google_place_id: '',
  latitude: null as number | null,
  longitude: null as number | null,
  distance_from_base_km: null as number | null,
  formatted_address: '',
  // Rooms
  bedrooms: 1,
  bathrooms: 1,
  living_rooms: null,
  dining_rooms: null,
  kitchen: null,
  // Amenities
  basement: false,
  attic: false,
  garage: false,
  yard: false,
  furnished: false,
  pets_allowed: false,
  heating_type: '',
})

const submitting = ref(false)
const error = ref<string | null>(null)
const addressInput = ref<HTMLInputElement | null>(null)
const autocompleteSearch = ref('')

// Load Google Maps on mount
onMounted(async () => {
  try {
    await loadGoogleMaps()

    // Initialize autocomplete after Google Maps is loaded
    if (addressInput.value && isLoaded.value) {
      initAutocomplete(addressInput.value, (place) => {
        // Populate form with selected place
        form.google_place_id = place.place_id
        form.formatted_address = place.formatted_address
        form.street_name = place.street_name
        form.house_number = place.house_number
        form.city = place.city
        form.province = place.province
        form.postal_code = place.postal_code
        form.country = place.country
        form.latitude = place.latitude
        form.longitude = place.longitude
        form.distance_from_base_km = place.distance_from_base_km || null

        // Update the search field to show the selected address
        autocompleteSearch.value = place.formatted_address
      })
    }
  } catch (err) {
    console.error('Failed to load Google Maps:', err)
    // Not critical - user can still enter address manually
  }
})

const handleSubmit = async () => {
  submitting.value = true
  error.value = null

  try {
    // Clean up null values
    const data: any = { ...form }
    Object.keys(data).forEach(key => {
      if (data[key] === null || data[key] === '') {
        delete data[key]
      }
    })

    const response = await createProperty(data)
    console.log('Property created:', response)

    // Redirect to property detail page
    await navigateTo(`/properties/${response.property.id}`)
  } catch (err: any) {
    console.error('Failed to create property:', err)
    error.value = err.data?.message || t('common.errorLoadingData')
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    submitting.value = false
  }
}
</script>
