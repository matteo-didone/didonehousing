import type { User } from './auth'

export interface Property {
  id: number
  landlord_id: number
  // Address
  street_name: string
  house_number: string
  apt_number?: string
  city: string
  province: string
  postal_code: string
  country: string
  // Cadastral Data
  cadastral_sheet_number?: string
  cadastral_plot_number?: string
  cadastral_unit_number?: string
  cadastral_tax_evaluation?: number
  cadastral_category?: string
  // Rooms
  living_rooms?: number
  dining_rooms?: number
  bedrooms: number
  bathrooms: number
  kitchen?: number
  basement: boolean
  attic: boolean
  garage: boolean
  yard: boolean
  // Property Details
  furnished: boolean
  pets_allowed: boolean
  heating_type?: HeatingType
  cooling_type?: string
  // Status
  status: PropertyStatus
  ho_reviewed_at?: string
  ho_reviewer_id?: number
  ho_comments?: string
  // Timestamps
  created_at: string
  updated_at: string
  deleted_at?: string
  // Relations
  landlord?: User
  listing?: Listing
  ho_reviewer?: User
}

export type PropertyStatus = 'draft' | 'pending_review' | 'approved' | 'rejected'

export type HeatingType =
  | 'city_gas'
  | 'lpg_coupons'
  | 'lpg_no_coupons'
  | 'fuel'
  | 'electric'
  | 'separate_system'
  | 'separate_meter'
  | 'shared_us'
  | 'shared_italians'

export interface PropertyFormData {
  street_name: string
  house_number: string
  apt_number?: string
  city: string
  province: string
  postal_code: string
  country: string
  bedrooms: number
  bathrooms: number
  living_rooms?: number
  dining_rooms?: number
  kitchen?: number
  furnished: boolean
  pets_allowed: boolean
  heating_type?: HeatingType
  cooling_type?: string
  basement: boolean
  attic: boolean
  garage: boolean
  yard: boolean
  cadastral_sheet_number?: string
  cadastral_plot_number?: string
  cadastral_unit_number?: string
  cadastral_tax_evaluation?: number
  cadastral_category?: string
}

export interface Listing {
  id: number
  property_id: number
  monthly_rent: string | number
  security_deposit: string | number
  condo_fees?: string | number
  duration_years?: number
  status: ListingStatus
  submitted_at?: string
  reviewed_at?: string
  approved_at?: string
  published_at?: string
  checklist_data?: Record<string, any>
  ho_comments?: string
  ho_reviewer_id?: number
  created_at: string
  updated_at: string
  deleted_at?: string
  // Relations
  property?: Property
  ho_reviewer?: User
}

export type ListingStatus =
  | 'draft'
  | 'submitted'
  | 'in_review'
  | 'approved'
  | 'rejected'
  | 'published'
  | 'unpublished'

export interface ListingFormData {
  property_id: number
  monthly_rent: number
  security_deposit: number
  condo_fees?: number
  duration_years?: number
  checklist_data?: Record<string, any>
}
