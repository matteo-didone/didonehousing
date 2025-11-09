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
              <span v-else-if="isLoaded" class="ml-2 text-xs text-success">✓ Ready</span>
            </Label>
            <input
              id="address_search"
              ref="addressInput"
              v-model="autocompleteSearch"
              :placeholder="t('property.create.searchAddressPlaceholder') || 'Start typing an address in Italy...'"
              type="text"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            />
            <p v-if="isLoaded" class="text-xs text-success">
              {{ t('property.create.searchAddressHint') || 'Use autocomplete to fill address fields automatically' }}
            </p>
            <p v-else class="text-xs text-muted-foreground">
              Type manually or wait for autocomplete to load...
            </p>
            <!-- Distance Badge -->
            <div v-if="form.distance_from_base_km" class="flex items-center gap-2 mt-2">
              <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                {{ t('property.create.distanceFromBase', { distance: form.distance_from_base_km }) }}
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
          <!-- Rooms -->
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
              <Label for="full_bathrooms">{{ t('property.create.fullBathrooms') }} *</Label>
              <Input
                id="full_bathrooms"
                v-model.number="form.full_bathrooms"
                type="number"
                min="0"
                max="10"
                required
              />
              <p class="text-xs text-muted-foreground">{{ t('property.create.fullBathroomsDesc') }}</p>
            </div>
            <div class="space-y-2">
              <Label for="half_bathrooms">{{ t('property.create.halfBathrooms') }}</Label>
              <Input
                id="half_bathrooms"
                v-model.number="form.half_bathrooms"
                type="number"
                min="0"
                max="10"
              />
              <p class="text-xs text-muted-foreground">{{ t('property.create.halfBathroomsDesc') }}</p>
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
          </div>

          <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
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
              <Label for="floor_number">{{ t('property.create.floor') }}</Label>
              <Input
                id="floor_number"
                v-model.number="form.floor_number"
                type="number"
                min="0"
                max="50"
              />
            </div>
            <div class="space-y-2">
              <Label for="total_floors">{{ t('property.create.totalFloors') }}</Label>
              <Input
                id="total_floors"
                v-model.number="form.total_floors"
                type="number"
                min="1"
                max="50"
              />
            </div>
          </div>

          <!-- Property Size & Age -->
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="total_sqm">{{ t('property.create.totalSqm') }}</Label>
              <Input
                id="total_sqm"
                v-model.number="form.total_sqm"
                type="number"
                min="10"
                step="0.01"
                placeholder="85.50"
              />
            </div>
            <div class="space-y-2">
              <Label for="year_built">{{ t('property.create.yearBuilt') }}</Label>
              <Input
                id="year_built"
                v-model.number="form.year_built"
                type="number"
                min="1800"
                :max="new Date().getFullYear()"
                placeholder="2000"
              />
            </div>
            <div class="space-y-2">
              <Label for="energy_class">{{ t('property.create.energyClass') }}</Label>
              <Input
                id="energy_class"
                v-model="form.energy_class"
                maxlength="5"
                :placeholder="t('property.create.energyClassPlaceholder')"
              />
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
          <!-- Furnishing Status -->
          <div class="space-y-2">
            <Label for="furnishing_status">{{ t('property.create.furnishingStatus') }} *</Label>
            <Select v-model="form.furnishing_status" required>
              <option value="unfurnished">{{ t('property.create.unfurnished') }}</option>
              <option value="partially_furnished">{{ t('property.create.partiallyFurnished') }}</option>
              <option value="fully_furnished">{{ t('property.create.fullyFurnished') }}</option>
            </Select>
          </div>

          <!-- Pets -->
          <div class="space-y-3 p-4 border border-border rounded-lg">
            <div class="flex items-center justify-between">
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
            <div v-if="form.pets_allowed" class="pt-2">
              <Label for="pets_notes">{{ t('property.create.petsNotes') }}</Label>
              <Input
                id="pets_notes"
                v-model="form.pets_notes"
                :placeholder="t('property.create.petsNotesPlaceholder')"
                class="mt-1"
              />
            </div>
          </div>

          <!-- Heating -->
          <div class="space-y-3 p-4 border border-border rounded-lg">
            <h3 class="font-medium">{{ t('property.create.heating') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="space-y-2">
                <Label for="heating_type">{{ t('property.create.heatingType') }}</Label>
                <Select v-model="form.heating_type">
                  <option value="">{{ t('common.select') }}...</option>
                  <option value="city_gas">{{ t('property.heating.cityGas') }}</option>
                  <option value="lpg_with_coupons">{{ t('property.heating.lpgCoupons') }}</option>
                  <option value="lpg_without_coupons">{{ t('property.heating.lpgNoCoupons') }}</option>
                  <option value="fuel_oil">{{ t('property.heating.fuel') }}</option>
                  <option value="electric">{{ t('property.heating.electric') }}</option>
                  <option value="heat_pump">Heat Pump</option>
                  <option value="other">Other</option>
                </Select>
              </div>
              <div class="space-y-2">
                <Label for="heating_system">{{ t('property.create.heatingSystem') }}</Label>
                <Select v-model="form.heating_system">
                  <option value="">{{ t('common.select') }}...</option>
                  <option value="centralized">{{ t('property.create.centralizedHeating') }}</option>
                  <option value="autonomous">{{ t('property.create.autonomousHeating') }}</option>
                  <option value="shared_with_us">{{ t('property.create.sharedWithUs') }}</option>
                  <option value="shared_with_italians">{{ t('property.create.sharedWithItalians') }}</option>
                </Select>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <input
                id="has_heat_meter"
                type="checkbox"
                v-model="form.has_heat_meter"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
              <div>
                <label for="has_heat_meter" class="font-medium text-sm">{{ t('property.create.hasHeatMeter') }}</label>
                <p class="text-xs text-muted-foreground">{{ t('property.create.hasHeatMeterDesc') }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <Label for="heating_notes">{{ t('property.create.heatingNotes') }}</Label>
              <Input
                id="heating_notes"
                v-model="form.heating_notes"
                :placeholder="t('property.create.heatingNotesPlaceholder')"
              />
            </div>
          </div>

          <!-- Garage -->
          <div class="space-y-3 p-4 border border-border rounded-lg">
            <div class="flex items-center justify-between">
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
            <div v-if="form.garage" class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2">
              <div class="space-y-2">
                <Label for="garage_type">{{ t('property.create.garageType') }}</Label>
                <Select v-model="form.garage_type">
                  <option value="">{{ t('common.select') }}...</option>
                  <option value="indoor">{{ t('property.create.garageIndoor') }}</option>
                  <option value="outdoor">{{ t('property.create.garageOutdoor') }}</option>
                  <option value="both">{{ t('property.create.garageBoth') }}</option>
                </Select>
              </div>
              <div class="space-y-2">
                <Label for="garage_spaces">{{ t('property.create.garageSpaces') }}</Label>
                <Input
                  id="garage_spaces"
                  v-model.number="form.garage_spaces"
                  type="number"
                  min="1"
                  max="10"
                />
              </div>
            </div>
          </div>

          <!-- Yard/Garden -->
          <div class="space-y-3 p-4 border border-border rounded-lg">
            <div class="flex items-center justify-between">
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
            <div v-if="form.yard" class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2">
              <div class="space-y-2">
                <Label for="yard_type">{{ t('property.create.yardType') }}</Label>
                <Select v-model="form.yard_type">
                  <option value="">{{ t('common.select') }}...</option>
                  <option value="front">{{ t('property.create.yardFront') }}</option>
                  <option value="back">{{ t('property.create.yardBack') }}</option>
                  <option value="both">{{ t('property.create.yardBoth') }}</option>
                </Select>
              </div>
              <div class="space-y-2">
                <Label for="yard_sqm">{{ t('property.create.yardSqm') }}</Label>
                <Input
                  id="yard_sqm"
                  v-model.number="form.yard_sqm"
                  type="number"
                  min="1"
                  step="0.01"
                  placeholder="50.00"
                />
              </div>
            </div>
          </div>

          <!-- Other Amenities -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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

            <!-- Elevator -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="elevator" class="font-medium">{{ t('property.create.elevator') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.elevatorDesc') }}</p>
              </div>
              <input
                id="elevator"
                type="checkbox"
                v-model="form.elevator"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Balcony -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="balcony" class="font-medium">{{ t('property.create.balcony') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.balconyDesc') }}</p>
              </div>
              <input
                id="balcony"
                type="checkbox"
                v-model="form.balcony"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>

            <!-- Terrace -->
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
              <div>
                <label for="terrace" class="font-medium">{{ t('property.create.terrace') }}</label>
                <p class="text-sm text-muted-foreground">{{ t('property.create.terraceDesc') }}</p>
              </div>
              <input
                id="terrace"
                type="checkbox"
                v-model="form.terrace"
                class="h-5 w-5 rounded border-border text-primary focus:ring-primary"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Cadastral Information Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.create.cadastralInfo') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="cadastral_sheet_number">{{ t('property.create.cadastralSheetNumber') }}</Label>
              <Input
                id="cadastral_sheet_number"
                v-model="form.cadastral_sheet_number"
                placeholder="123"
              />
            </div>
            <div class="space-y-2">
              <Label for="cadastral_plot_number">{{ t('property.create.cadastralPlotNumber') }}</Label>
              <Input
                id="cadastral_plot_number"
                v-model="form.cadastral_plot_number"
                placeholder="456"
              />
            </div>
            <div class="space-y-2">
              <Label for="cadastral_unit_number">{{ t('property.create.cadastralUnitNumber') }}</Label>
              <Input
                id="cadastral_unit_number"
                v-model="form.cadastral_unit_number"
                placeholder="7"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="cadastral_category">{{ t('property.create.cadastralCategory') }}</Label>
              <Input
                id="cadastral_category"
                v-model="form.cadastral_category"
                :placeholder="t('property.create.cadastralCategoryPlaceholder')"
              />
            </div>
            <div class="space-y-2">
              <Label for="cadastral_tax_evaluation">{{ t('property.create.cadastralTaxEvaluation') }}</Label>
              <Input
                id="cadastral_tax_evaluation"
                v-model="form.cadastral_tax_evaluation"
                placeholder="€ 850.00"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Financial Information Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ t('property.create.financialInfo') }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="monthly_rent">{{ t('property.create.monthlyRent') }} *</Label>
              <Input
                id="monthly_rent"
                v-model.number="form.monthly_rent"
                type="number"
                min="0"
                step="0.01"
                required
                :placeholder="t('property.create.monthlyRentPlaceholder')"
              />
            </div>
            <div class="space-y-2">
              <Label for="security_deposit">{{ t('property.create.securityDepositAmount') }}</Label>
              <Input
                id="security_deposit"
                v-model.number="form.security_deposit"
                type="number"
                min="0"
                step="0.01"
                :placeholder="t('property.create.securityDepositPlaceholder')"
              />
            </div>
            <div class="space-y-2">
              <Label for="condo_fees">{{ t('property.create.condoFees') }}</Label>
              <Input
                id="condo_fees"
                v-model.number="form.condo_fees"
                type="number"
                min="0"
                step="0.01"
                :placeholder="t('property.create.condoFeesPlaceholder')"
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
import { ref, reactive, onMounted, nextTick } from 'vue'
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
  full_bathrooms: 1,
  half_bathrooms: 0,
  living_rooms: null,
  dining_rooms: null,
  kitchen: null,
  // Amenities
  basement: false,
  attic: false,
  garage: false,
  garage_type: '',
  garage_spaces: null,
  yard: false,
  yard_type: '',
  yard_sqm: null,
  balcony: false,
  terrace: false,
  elevator: false,
  // Furnishing & Pets
  furnishing_status: 'unfurnished',
  pets_allowed: false,
  pets_notes: '',
  // Heating
  heating_type: '',
  heating_system: '',
  has_heat_meter: false,
  heating_notes: '',
  // Property Details
  floor_number: null,
  total_floors: null,
  total_sqm: null,
  year_built: null,
  // Energy
  energy_class: '',
  // Cadastral
  cadastral_sheet_number: '',
  cadastral_plot_number: '',
  cadastral_unit_number: '',
  cadastral_tax_evaluation: '',
  cadastral_category: '',
  // Financial (for Listing)
  monthly_rent: null,
  security_deposit: null,
  condo_fees: null,
})

const submitting = ref(false)
const error = ref<string | null>(null)
const addressInput = ref<HTMLInputElement | null>(null)
const autocompleteSearch = ref('')

// Load Google Maps on mount
onMounted(async () => {
  console.log('[Google Maps] Starting initialization...')

  try {
    await loadGoogleMaps()
    console.log('[Google Maps] Loaded successfully, isLoaded:', isLoaded.value)

    // Wait for next tick to ensure DOM is ready
    await nextTick()

    // Initialize autocomplete after Google Maps is loaded
    if (addressInput.value && isLoaded.value) {
      console.log('[Google Maps] Initializing autocomplete on input element')
      const autocomplete = initAutocomplete(addressInput.value, (place) => {
        console.log('[Google Maps] Place selected:', place)

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

      if (autocomplete) {
        console.log('[Google Maps] Autocomplete initialized successfully')
      } else {
        console.warn('[Google Maps] Autocomplete initialization returned null')
      }
    } else {
      console.warn('[Google Maps] Cannot initialize autocomplete - input:', addressInput.value, 'isLoaded:', isLoaded.value)
    }
  } catch (err) {
    console.error('[Google Maps] Failed to load:', err)
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
