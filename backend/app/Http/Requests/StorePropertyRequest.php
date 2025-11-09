<?php

namespace App\Http\Requests;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isLandlord();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // =========================================================
            // ADDRESS
            // =========================================================
            'street_name' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:50'],
            'apt_number' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:2'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:2'],

            // =========================================================
            // GOOGLE MAPS INTEGRATION
            // =========================================================
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'formatted_address' => ['nullable', 'string', 'max:500'],
            // distance_from_base_km is auto-calculated, not validated

            // =========================================================
            // CADASTRAL DATA
            // =========================================================
            'cadastral_sheet_number' => ['nullable', 'string', 'max:50'],
            'cadastral_plot_number' => ['nullable', 'string', 'max:50'],
            'cadastral_unit_number' => ['nullable', 'string', 'max:50'],
            'cadastral_tax_evaluation' => ['nullable', 'numeric', 'min:0'],
            'cadastral_category' => ['nullable', 'string', 'max:10'],

            // =========================================================
            // ROOMS
            // =========================================================
            'living_rooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'dining_rooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'bedrooms' => ['required', 'integer', 'min:1', 'max:20'],
            'full_bathrooms' => ['required', 'integer', 'min:0', 'max:10'],
            'half_bathrooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'kitchen' => ['nullable', 'integer', 'min:0', 'max:5'],
            'basement' => ['boolean'],
            'attic' => ['boolean'],
            'garage' => ['boolean'],
            'garage_type' => ['nullable', Rule::in(['indoor', 'outdoor', 'both'])],
            'garage_spaces' => ['nullable', 'integer', 'min:1', 'max:10'],
            'yard' => ['boolean'],
            'yard_type' => ['nullable', Rule::in(['front', 'back', 'both'])],
            'yard_sqm' => ['nullable', 'numeric', 'min:1', 'max:10000'],

            // =========================================================
            // FURNISHING
            // =========================================================
            'furnishing_status' => ['required', Rule::in([
                Property::FURNISHING_UNFURNISHED,
                Property::FURNISHING_PARTIALLY,
                Property::FURNISHING_FULLY,
            ])],

            // =========================================================
            // PETS
            // =========================================================
            'pets_allowed' => ['boolean'],
            'pets_notes' => ['nullable', 'string', 'max:1000'],

            // =========================================================
            // HEATING & COOLING
            // =========================================================
            'heating_type' => ['nullable', Rule::in([
                Property::HEATING_CITY_GAS,
                Property::HEATING_LPG_COUPONS,
                Property::HEATING_LPG_NO_COUPONS,
                Property::HEATING_FUEL,
                Property::HEATING_ELECTRIC,
                Property::HEATING_HEAT_PUMP,
                Property::HEATING_WOOD,
                Property::HEATING_OTHER,
            ])],
            'heating_system' => ['nullable', Rule::in([
                Property::HEATING_SYSTEM_CENTRALIZED,
                Property::HEATING_SYSTEM_AUTONOMOUS,
                Property::HEATING_SYSTEM_SHARED_US,
                Property::HEATING_SYSTEM_SHARED_ITALIANS,
            ])],
            'has_heat_meter' => ['boolean'],
            'heating_notes' => ['nullable', 'string', 'max:1000'],
            'cooling_type' => ['nullable', 'string', 'max:255'],

            // =========================================================
            // REDECORATION
            // =========================================================
            'redecoration_fees_required' => ['boolean'],
            'redecoration_fees_amount' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'redecoration_date' => ['nullable', 'date'],

            // =========================================================
            // ADDITIONAL DETAILS
            // =========================================================
            'floor_number' => ['nullable', 'integer', 'min:-5', 'max:50'],
            'total_floors' => ['nullable', 'integer', 'min:1', 'max:50'],
            'elevator' => ['boolean'],
            'balcony' => ['boolean'],
            'terrace' => ['boolean'],
            'total_sqm' => ['nullable', 'numeric', 'min:10', 'max:1000'],
            'energy_class' => ['nullable', 'string', Rule::in([
                'A4', 'A3', 'A2', 'A1', 'A', 'B', 'C', 'D', 'E', 'F', 'G'
            ])],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 2)],

            // =========================================================
            // LISTING / FINANCIAL INFO (will be used to create Listing)
            // =========================================================
            'monthly_rent' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'security_deposit' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'condo_fees' => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'bedrooms.required' => 'At least one bedroom is required',
            'bedrooms.min' => 'Property must have at least 1 bedroom',
            'full_bathrooms.required' => 'Please specify the number of full bathrooms',
            'furnishing_status.required' => 'Please specify the furnishing status',
            'total_sqm.min' => 'Property must be at least 10 square meters',
            'year_built.min' => 'Year built must be after 1800',
            'year_built.max' => 'Year built cannot be in the far future',
        ];
    }

    /**
     * Custom validation rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // At least one bathroom (full or half) must be present
            $fullBaths = $this->input('full_bathrooms', 0);
            $halfBaths = $this->input('half_bathrooms', 0);

            if ($fullBaths + $halfBaths < 1) {
                $validator->errors()->add(
                    'bathrooms',
                    'Property must have at least one bathroom (full or half)'
                );
            }

            // If floor_number is provided, total_floors should be >= floor_number
            if ($this->filled('floor_number') && $this->filled('total_floors')) {
                $floor = $this->input('floor_number');
                $total = $this->input('total_floors');

                if ($floor > $total) {
                    $validator->errors()->add(
                        'floor_number',
                        'Floor number cannot exceed total floors'
                    );
                }
            }

            // If redecoration fees required, amount should be specified
            if ($this->input('redecoration_fees_required') && !$this->filled('redecoration_fees_amount')) {
                $validator->errors()->add(
                    'redecoration_fees_amount',
                    'Please specify the redecoration fee amount'
                );
            }
        });
    }
}
