export interface Property {
  id: number
  title: string
  description: string
  address: string
  city: string
  zip_code: string
  property_type: 'apartment' | 'house' | 'room'
  bedrooms: number
  bathrooms: number
  square_meters: number
  monthly_rent: number
  deposit: number
  available_from: string
  furnished: boolean
  pets_allowed: boolean
  smoking_allowed: boolean
  utilities_included: boolean
  status: 'available' | 'rented' | 'pending'
  latitude?: number
  longitude?: number
  photos?: PropertyPhoto[]
  owner?: {
    id: number
    name: string
    email: string
  }
  created_at: string
  updated_at: string
}

export interface PropertyPhoto {
  id: number
  property_id: number
  file_path: string
  is_primary: boolean
  order: number
}

export interface PropertySearchFilters {
  city?: string
  property_type?: string
  min_rent?: number
  max_rent?: number
  bedrooms?: number
  bathrooms?: number
  furnished?: boolean
  pets_allowed?: boolean
}
