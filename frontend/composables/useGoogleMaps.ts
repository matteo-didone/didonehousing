import { ref } from 'vue'

// Aviano Air Base coordinates
export const AVIANO_BASE_COORDS = {
  lat: 46.031389,
  lng: 12.596667,
  address: 'Aviano Air Base, 33081 Aviano PN, Italy',
}

export interface PlaceResult {
  place_id: string
  formatted_address: string
  street_name: string
  house_number: string
  city: string
  province: string
  postal_code: string
  country: string
  latitude: number
  longitude: number
  distance_from_base_km?: number
}

export const useGoogleMaps = () => {
  const config = useRuntimeConfig()
  const isLoaded = ref(false)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  /**
   * Load Google Maps JavaScript API
   */
  const loadGoogleMaps = async (): Promise<void> => {
    if (isLoaded.value) return
    if (isLoading.value) return

    isLoading.value = true
    error.value = null

    try {
      const apiKey = config.public.googleMapsApiKey

      if (!apiKey) {
        throw new Error('Google Maps API key not configured')
      }

      // Check if already loaded
      if (window.google && window.google.maps) {
        isLoaded.value = true
        isLoading.value = false
        return
      }

      // Load the script
      await new Promise<void>((resolve, reject) => {
        const script = document.createElement('script')
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&language=it`
        script.async = true
        script.defer = true

        script.onload = () => {
          isLoaded.value = true
          resolve()
        }

        script.onerror = () => {
          reject(new Error('Failed to load Google Maps'))
        }

        document.head.appendChild(script)
      })
    } catch (err: any) {
      error.value = err.message
      console.error('Error loading Google Maps:', err)
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Calculate distance between two coordinates using Haversine formula
   */
  const calculateDistance = (
    lat1: number,
    lng1: number,
    lat2: number,
    lng2: number
  ): number => {
    const earthRadius = 6371 // km

    const latDiff = toRadians(lat2 - lat1)
    const lngDiff = toRadians(lng2 - lng1)

    const a =
      Math.sin(latDiff / 2) * Math.sin(latDiff / 2) +
      Math.cos(toRadians(lat1)) *
        Math.cos(toRadians(lat2)) *
        Math.sin(lngDiff / 2) *
        Math.sin(lngDiff / 2)

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

    return Math.round(earthRadius * c * 100) / 100 // round to 2 decimals
  }

  const toRadians = (degrees: number): number => {
    return degrees * (Math.PI / 180)
  }

  /**
   * Calculate distance from Aviano Air Base
   */
  const calculateDistanceFromBase = (lat: number, lng: number): number => {
    return calculateDistance(
      AVIANO_BASE_COORDS.lat,
      AVIANO_BASE_COORDS.lng,
      lat,
      lng
    )
  }

  /**
   * Parse Google Place result to our format
   */
  const parsePlaceResult = (
    place: google.maps.places.PlaceResult
  ): PlaceResult | null => {
    if (!place.geometry?.location || !place.address_components) {
      return null
    }

    const lat = place.geometry.location.lat()
    const lng = place.geometry.location.lng()

    // Extract address components
    let street_name = ''
    let house_number = ''
    let city = ''
    let province = ''
    let postal_code = ''
    let country = 'IT'

    for (const component of place.address_components) {
      const types = component.types

      if (types.includes('route')) {
        street_name = component.long_name
      } else if (types.includes('street_number')) {
        house_number = component.long_name
      } else if (types.includes('locality')) {
        city = component.long_name
      } else if (types.includes('administrative_area_level_2')) {
        province = component.short_name
      } else if (types.includes('postal_code')) {
        postal_code = component.long_name
      } else if (types.includes('country')) {
        country = component.short_name
      }
    }

    return {
      place_id: place.place_id || '',
      formatted_address: place.formatted_address || '',
      street_name,
      house_number,
      city,
      province,
      postal_code,
      country,
      latitude: lat,
      longitude: lng,
      distance_from_base_km: calculateDistanceFromBase(lat, lng),
    }
  }

  /**
   * Initialize autocomplete on an input element
   */
  const initAutocomplete = (
    input: HTMLInputElement,
    onPlaceSelected: (place: PlaceResult) => void
  ): google.maps.places.Autocomplete | null => {
    if (!window.google || !window.google.maps) {
      console.error('Google Maps not loaded')
      return null
    }

    const autocomplete = new google.maps.places.Autocomplete(input, {
      componentRestrictions: { country: 'it' },
      fields: [
        'place_id',
        'formatted_address',
        'address_components',
        'geometry',
      ],
      types: ['address'],
    })

    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace()
      const parsed = parsePlaceResult(place)

      if (parsed) {
        onPlaceSelected(parsed)
      }
    })

    return autocomplete
  }

  return {
    isLoaded,
    isLoading,
    error,
    loadGoogleMaps,
    calculateDistance,
    calculateDistanceFromBase,
    parsePlaceResult,
    initAutocomplete,
    AVIANO_BASE_COORDS,
  }
}
