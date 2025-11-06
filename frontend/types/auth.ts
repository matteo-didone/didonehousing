export interface User {
  id: number
  first_name: string
  last_name: string
  full_name: string
  email: string
  phone?: string
  locale: 'en' | 'it'
  two_factor_enabled: boolean
  last_login_at?: string
  email_verified_at?: string
  roles: string[]
  permissions: string[]
  profile?: TenantProfile | LandlordProfile | HoProfile | VendorProfile
}

export interface TenantProfile {
  id: number
  user_id: number
  rank?: string
  branch?: string
  unit?: string
  family_size?: number
  has_pets: boolean
  pet_details?: string
  oha_amount?: number
  oha_currency?: 'USD' | 'EUR'
  special_requirements?: string
}

export interface LandlordProfile {
  id: number
  user_id: number
  company_name?: string
  tax_id?: string
  business_type?: 'individual' | 'company'
  city?: string
  province?: string
  cedolare_secca?: boolean
  bank_name?: string
  iban?: string
}

export interface HoProfile {
  id: number
  user_id: number
  office_code?: string
  position?: string
}

export interface VendorProfile {
  id: number
  user_id: number
  company_name?: string
  vat_number?: string
  services?: string[]
  service_area?: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  first_name: string
  last_name: string
  email: string
  phone?: string
  password: string
  password_confirmation: string
  locale: 'en' | 'it'
  role: 'tenant' | 'landlord' | 'vendor'
  profile?: Record<string, any>
}

export interface AuthResponse {
  message: string
  user: User
  token: string
}
