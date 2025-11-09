<template>
  <div class="mx-auto max-w-4xl space-y-6">
    <!-- Header -->
    <div>
      <NuxtLink to="/properties" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
        {{ translations.cancel }}
      </NuxtLink>
      <h1 class="text-3xl font-bold tracking-tight">{{ translations.title }}</h1>
      <p class="mt-2 text-muted-foreground">{{ translations.subtitle }}</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Basic Information -->
      <Card>
        <CardHeader>
          <CardTitle>{{ translations.basicInfo }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <!-- Property Type -->
          <div class="space-y-2">
            <Label for="type">{{ translations.propertyType }} *</Label>
            <Select id="type" v-model="form.type" required>
              <option value="">{{ translations.selectType }}</option>
              <option value="apartment">{{ propertyTypes.apartment }}</option>
              <option value="house">{{ propertyTypes.house }}</option>
              <option value="villa">{{ propertyTypes.villa }}</option>
              <option value="studio">{{ propertyTypes.studio }}</option>
            </Select>
          </div>

          <!-- Address -->
          <div class="space-y-2">
            <Label for="address">{{ translations.address }} *</Label>
            <Input
              id="address"
              v-model="form.address"
              :placeholder="translations.addressPlaceholder"
              required
            />
          </div>

          <!-- City & ZIP -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="city">{{ translations.city }} *</Label>
              <Input
                id="city"
                v-model="form.city"
                :placeholder="translations.cityPlaceholder"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="zipCode">{{ translations.zipCode }}</Label>
              <Input
                id="zipCode"
                v-model="form.zipCode"
                :placeholder="translations.zipCodePlaceholder"
              />
            </div>
          </div>

          <!-- Distance to Base -->
          <div class="space-y-2">
            <Label for="distanceToBase">{{ translations.distanceToBase }} (km) *</Label>
            <Input
              id="distanceToBase"
              v-model.number="form.distanceToBase"
              type="number"
              step="0.1"
              min="0"
              required
            />
          </div>
        </CardContent>
      </Card>

      <!-- Property Details -->
      <Card>
        <CardHeader>
          <CardTitle>{{ translations.propertyDetails }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <!-- Rent & Deposit -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="rent">{{ translations.rentPerMonth }} *</Label>
              <Input
                id="rent"
                v-model.number="form.rent"
                type="number"
                min="0"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="deposit">{{ translations.securityDeposit }} *</Label>
              <Input
                id="deposit"
                v-model.number="form.deposit"
                type="number"
                min="0"
                :placeholder="translations.depositPlaceholder"
                required
              />
            </div>
          </div>

          <!-- Bedrooms, Bathrooms, Square Meters -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="bedrooms">{{ translations.bedrooms }} *</Label>
              <Input
                id="bedrooms"
                v-model.number="form.bedrooms"
                type="number"
                min="0"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="bathrooms">{{ translations.bathrooms }} *</Label>
              <Input
                id="bathrooms"
                v-model.number="form.bathrooms"
                type="number"
                min="0"
                required
              />
            </div>
            <div class="space-y-2">
              <Label for="squareMeters">{{ translations.squareMeters }} *</Label>
              <Input
                id="squareMeters"
                v-model.number="form.squareMeters"
                type="number"
                min="0"
                required
              />
            </div>
          </div>

          <!-- Floor, Total Floors, Year Built -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="space-y-2">
              <Label for="floor">{{ translations.floor }}</Label>
              <Input
                id="floor"
                v-model.number="form.floor"
                type="number"
                min="0"
              />
            </div>
            <div class="space-y-2">
              <Label for="totalFloors">{{ translations.totalFloors }}</Label>
              <Input
                id="totalFloors"
                v-model.number="form.totalFloors"
                type="number"
                min="0"
              />
            </div>
            <div class="space-y-2">
              <Label for="yearBuilt">{{ translations.yearBuilt }}</Label>
              <Input
                id="yearBuilt"
                v-model.number="form.yearBuilt"
                type="number"
                min="1800"
                :max="new Date().getFullYear()"
              />
            </div>
          </div>

          <!-- Furnished & Available From -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <Label for="furnished">{{ translations.furnished }}</Label>
              <Select id="furnished" v-model="form.furnished">
                <option value="no">{{ translations.no }}</option>
                <option value="yes">{{ translations.yes }}</option>
              </Select>
            </div>
            <div class="space-y-2">
              <Label for="availableFrom">{{ translations.availableDate }}</Label>
              <Input
                id="availableFrom"
                v-model="form.availableFrom"
                type="date"
              />
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="description">{{ translations.description }} *</Label>
            <Textarea
              id="description"
              v-model="form.description"
              :placeholder="translations.descriptionPlaceholder"
              rows="6"
              required
            />
          </div>
        </CardContent>
      </Card>

      <!-- Amenities & Features -->
      <Card>
        <CardHeader>
          <CardTitle>{{ translations.amenities }}</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <!-- Parking -->
            <div class="flex items-center space-x-2">
              <input
                id="parking"
                v-model="form.amenities.parking"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="parking" class="font-normal cursor-pointer">{{ translations.parking }}</Label>
            </div>

            <!-- Balcony -->
            <div class="flex items-center space-x-2">
              <input
                id="balcony"
                v-model="form.amenities.balcony"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="balcony" class="font-normal cursor-pointer">{{ translations.balcony }}</Label>
            </div>

            <!-- Garden -->
            <div class="flex items-center space-x-2">
              <input
                id="garden"
                v-model="form.amenities.garden"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="garden" class="font-normal cursor-pointer">{{ translations.garden }}</Label>
            </div>

            <!-- Air Conditioning -->
            <div class="flex items-center space-x-2">
              <input
                id="airConditioning"
                v-model="form.amenities.airConditioning"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="airConditioning" class="font-normal cursor-pointer">{{ translations.airConditioning }}</Label>
            </div>

            <!-- Heating -->
            <div class="flex items-center space-x-2">
              <input
                id="heating"
                v-model="form.amenities.heating"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="heating" class="font-normal cursor-pointer">{{ translations.heating }}</Label>
            </div>

            <!-- Elevator -->
            <div class="flex items-center space-x-2">
              <input
                id="elevator"
                v-model="form.amenities.elevator"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="elevator" class="font-normal cursor-pointer">{{ translations.elevator }}</Label>
            </div>

            <!-- Pets -->
            <div class="flex items-center space-x-2">
              <input
                id="pets"
                v-model="form.amenities.pets"
                type="checkbox"
                class="h-4 w-4 rounded border-input"
              />
              <Label for="pets" class="font-normal cursor-pointer">{{ translations.pets }}</Label>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Photos -->
      <Card>
        <CardHeader>
          <CardTitle>{{ translations.uploadPhotos }}</CardTitle>
          <CardDescription>{{ translations.uploadHint }}</CardDescription>
        </CardHeader>
        <CardContent>
          <PhotoUpload v-model="photos" :max-photos="20" />
        </CardContent>
      </Card>

      <!-- Documents -->
      <Card>
        <CardHeader>
          <CardTitle>{{ translations.uploadDocuments }}</CardTitle>
          <CardDescription>{{ translations.uploadDocumentsHint }}</CardDescription>
        </CardHeader>
        <CardContent>
          <DocumentUpload v-model="documents" :max-documents="10" />
        </CardContent>
      </Card>

      <!-- Submit Buttons -->
      <div class="flex gap-4">
        <NuxtLink to="/properties" class="flex-1">
          <Button type="button" variant="outline" class="w-full" size="lg">
            {{ translations.cancel }}
          </Button>
        </NuxtLink>
        <Button type="submit" :disabled="submitting" class="flex-1" size="lg">
          <span v-if="submitting">{{ translations.submitting }}</span>
          <span v-else>{{ translations.submit }}</span>
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Select from '@/components/ui/Select.vue'
import Textarea from '@/components/ui/Textarea.vue'
import PhotoUpload from '@/components/PhotoUpload.vue'
import DocumentUpload from '@/components/DocumentUpload.vue'

definePageMeta({
  layout: 'default',
  middleware: ['auth', 'landlord'],
})

const { t, locale } = useI18n()

// Translations
const translations = ref({
  title: '',
  subtitle: '',
  basicInfo: '',
  propertyDetails: '',
  amenities: '',
  uploadPhotos: '',
  uploadHint: '',
  uploadDocuments: '',
  uploadDocumentsHint: '',
  submit: '',
  cancel: '',
  submitting: '',
  address: '',
  addressPlaceholder: '',
  city: '',
  cityPlaceholder: '',
  zipCode: '',
  zipCodePlaceholder: '',
  propertyType: '',
  selectType: '',
  distanceToBase: '',
  rentPerMonth: '',
  securityDeposit: '',
  depositPlaceholder: '',
  bedrooms: '',
  bathrooms: '',
  squareMeters: '',
  floor: '',
  totalFloors: '',
  yearBuilt: '',
  furnished: '',
  yes: '',
  no: '',
  availableDate: '',
  description: '',
  descriptionPlaceholder: '',
  parking: '',
  balcony: '',
  garden: '',
  airConditioning: '',
  heating: '',
  elevator: '',
  pets: '',
  dragDrop: '',
})

const propertyTypes = ref({
  apartment: '',
  house: '',
  villa: '',
  studio: '',
})

const loadTranslations = () => {
  translations.value = {
    title: t('property.create.title'),
    subtitle: t('property.create.subtitle'),
    basicInfo: t('property.create.basicInfo'),
    propertyDetails: t('property.create.propertyDetails'),
    amenities: t('property.create.amenities'),
    uploadPhotos: t('property.create.uploadPhotos'),
    uploadHint: t('property.create.uploadHint'),
    uploadDocuments: t('property.documents.title'),
    uploadDocumentsHint: t('property.documents.subtitle'),
    submit: t('property.create.submit'),
    cancel: t('property.create.cancel'),
    submitting: t('property.create.submitting'),
    address: t('property.address'),
    addressPlaceholder: t('property.create.addressPlaceholder'),
    city: t('property.create.city'),
    cityPlaceholder: t('property.create.cityPlaceholder'),
    zipCode: t('property.create.zipCode'),
    zipCodePlaceholder: t('property.create.zipCodePlaceholder'),
    propertyType: t('property.create.propertyType'),
    selectType: t('property.create.selectType'),
    distanceToBase: t('property.distanceToBase'),
    rentPerMonth: t('property.create.rentPerMonth'),
    securityDeposit: t('property.create.securityDeposit'),
    depositPlaceholder: t('property.create.depositPlaceholder'),
    bedrooms: t('property.bedrooms'),
    bathrooms: t('property.bathrooms'),
    squareMeters: t('property.squareMeters'),
    floor: t('property.create.floor'),
    totalFloors: t('property.create.totalFloors'),
    yearBuilt: t('property.create.yearBuilt'),
    furnished: t('property.create.furnished'),
    yes: t('property.create.yes'),
    no: t('property.create.no'),
    availableDate: t('property.create.availableDate'),
    description: t('property.create.description'),
    descriptionPlaceholder: t('property.create.descriptionPlaceholder'),
    parking: t('property.create.parking'),
    balcony: t('property.create.balcony'),
    garden: t('property.create.garden'),
    airConditioning: t('property.create.airConditioning'),
    heating: t('property.create.heating'),
    elevator: t('property.create.elevator'),
    pets: t('property.create.pets'),
    dragDrop: t('property.create.dragDrop'),
  }

  propertyTypes.value = {
    apartment: t('property.types.apartment'),
    house: t('property.types.house'),
    villa: t('property.types.villa'),
    studio: t('property.types.studio'),
  }
}

onMounted(() => {
  loadTranslations()
})

watch(locale, () => {
  loadTranslations()
})

// Form state
const submitting = ref(false)
const photos = ref([])
const documents = ref([])
const form = ref({
  type: '',
  address: '',
  city: '',
  zipCode: '',
  distanceToBase: null as number | null,
  rent: null as number | null,
  deposit: null as number | null,
  bedrooms: null as number | null,
  bathrooms: null as number | null,
  squareMeters: null as number | null,
  floor: null as number | null,
  totalFloors: null as number | null,
  yearBuilt: null as number | null,
  furnished: 'no',
  availableFrom: '',
  description: '',
  amenities: {
    parking: false,
    balcony: false,
    garden: false,
    airConditioning: false,
    heating: false,
    elevator: false,
    pets: false,
  },
})

// Composables
const config = useRuntimeConfig()
const { token } = useAuth()
const { uploadDocument } = useDocuments()

// Methods
const handleSubmit = async () => {
  submitting.value = true
  try {
    // 1. Create the property first
    const response = await fetch(`${config.public.apiBaseUrl}/api/properties`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form.value),
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Failed to create property')
    }

    const { data: property } = await response.json()
    console.log('Property created:', property)

    // 2. Upload photos
    if (photos.value.length > 0) {
      console.log('Uploading photos...')
      for (let i = 0; i < photos.value.length; i++) {
        const photo = photos.value[i]
        photo.uploading = true

        try {
          const formData = new FormData()
          formData.append('file', photo.file)
          formData.append('documentable_type', 'Property')
          formData.append('documentable_id', property.id.toString())
          formData.append('type', 'photo')
          if (photo.description) {
            formData.append('description', photo.description)
          }

          await uploadDocument(formData)

          photo.uploaded = true
          photo.uploading = false
          photo.progress = 100
        } catch (err) {
          console.error('Error uploading photo:', err)
          photo.error = err.message || 'Upload failed'
          photo.uploading = false
        }
      }
    }

    // 3. Upload documents
    if (documents.value.length > 0) {
      console.log('Uploading documents...')
      for (let i = 0; i < documents.value.length; i++) {
        const doc = documents.value[i]
        doc.uploading = true

        try {
          const formData = new FormData()
          formData.append('file', doc.file)
          formData.append('documentable_type', 'Property')
          formData.append('documentable_id', property.id.toString())
          formData.append('type', doc.type)
          if (doc.description) {
            formData.append('description', doc.description)
          }

          await uploadDocument(formData)

          doc.uploaded = true
          doc.uploading = false
          doc.progress = 100
        } catch (err) {
          console.error('Error uploading document:', err)
          doc.error = err.message || 'Upload failed'
          doc.uploading = false
        }
      }
    }

    // Show success message and redirect
    alert(t('property.create.success') + '\n' + t('property.create.successDescription'))
    navigateTo('/properties')
  } catch (error) {
    console.error('Error submitting property:', error)
    alert('Error creating property: ' + (error.message || 'Unknown error'))
  } finally {
    submitting.value = false
  }
}
</script>
