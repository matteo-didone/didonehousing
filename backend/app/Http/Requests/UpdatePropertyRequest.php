<?php

namespace App\Http\Requests;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $property = $this->route('property');
        $user = $this->user();

        // Landlords can only update their own properties
        if ($user->isLandlord()) {
            return $property->landlord_id === $user->id;
        }

        // HO can update any property (for status changes)
        return $user->isHousingOffice();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // =========================================================
            // ADDRESS
            // =========================================================
            'street_name' => ['sometimes', 'string', 'max:255'],
            'house_number' => ['sometimes', 'string', 'max:50'],
            'apt_number' => ['nullable', 'string', 'max:50'],
            'city' => ['sometimes', 'string', 'max:255'],
            'province' => ['sometimes', 'string', 'max:2'],
            'postal_code' => ['sometimes', 'string', 'max:10'],
            'country' => ['sometimes', 'string', 'max:2'],

            // =========================================================
            // GOOGLE MAPS INTEGRATION
            // =========================================================
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'formatted_address' => ['nullable', 'string', 'max:500'],

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
            'living_rooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'dining_rooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'bedrooms' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'full_bathrooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'half_bathrooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'kitchen' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'basement' => ['sometimes', 'boolean'],
            'attic' => ['sometimes', 'boolean'],
            'garage' => ['sometimes', 'boolean'],
            'garage_type' => ['nullable', Rule::in(['indoor', 'outdoor', 'both'])],
            'garage_spaces' => ['nullable', 'integer', 'min:1', 'max:10'],
            'yard' => ['sometimes', 'boolean'],
            'yard_type' => ['nullable', Rule::in(['front', 'back', 'both'])],
            'yard_sqm' => ['nullable', 'numeric', 'min:1', 'max:10000'],

            // =========================================================
            // FURNISHING
            // =========================================================
            'furnishing_status' => ['sometimes', Rule::in([
                Property::FURNISHING_UNFURNISHED,
                Property::FURNISHING_PARTIALLY,
                Property::FURNISHING_FULLY,
            ])],

            // =========================================================
            // PETS
            // =========================================================
            'pets_allowed' => ['sometimes', 'boolean'],
            'pets_notes' => ['nullable', 'string', 'max:1000'],

            // =========================================================
            // HEATING & COOLING
            // =========================================================
            'heating_type' => ['sometimes', Rule::in([
                Property::HEATING_CITY_GAS,
                Property::HEATING_LPG_COUPONS,
                Property::HEATING_LPG_NO_COUPONS,
                Property::HEATING_FUEL,
                Property::HEATING_ELECTRIC,
                Property::HEATING_HEAT_PUMP,
                Property::HEATING_WOOD,
                Property::HEATING_OTHER,
            ])],
            'heating_system' => ['sometimes', Rule::in([
                Property::HEATING_SYSTEM_CENTRALIZED,
                Property::HEATING_SYSTEM_AUTONOMOUS,
                Property::HEATING_SYSTEM_SHARED_US,
                Property::HEATING_SYSTEM_SHARED_ITALIANS,
            ])],
            'has_heat_meter' => ['sometimes', 'boolean'],
            'heating_notes' => ['nullable', 'string', 'max:1000'],
            'cooling_type' => ['nullable', 'string', 'max:255'],

            // =========================================================
            // REDECORATION
            // =========================================================
            'redecoration_fees_required' => ['sometimes', 'boolean'],
            'redecoration_fees_amount' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'redecoration_date' => ['nullable', 'date'],

            // =========================================================
            // ADDITIONAL DETAILS
            // =========================================================
            'floor_number' => ['nullable', 'integer', 'min:-5', 'max:50'],
            'total_floors' => ['nullable', 'integer', 'min:1', 'max:50'],
            'elevator' => ['sometimes', 'boolean'],
            'balcony' => ['sometimes', 'boolean'],
            'terrace' => ['sometimes', 'boolean'],
            'total_sqm' => ['nullable', 'numeric', 'min:10', 'max:1000'],
            'energy_class' => ['nullable', 'string', Rule::in([
                'A4', 'A3', 'A2', 'A1', 'A', 'B', 'C', 'D', 'E', 'F', 'G'
            ])],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 2)],
        ];
    }

    /**
     * Custom validation rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $property = $this->route('property');

            // Check total bathrooms if updating bathroom fields
            if ($this->has('full_bathrooms') || $this->has('half_bathrooms')) {
                $fullBaths = $this->input('full_bathrooms', $property->full_bathrooms ?? 0);
                $halfBaths = $this->input('half_bathrooms', $property->half_bathrooms ?? 0);

                if ($fullBaths + $halfBaths < 1) {
                    $validator->errors()->add(
                        'bathrooms',
                        'Property must have at least one bathroom (full or half)'
                    );
                }
            }

            // Floor validation
            if ($this->filled('floor_number') || $this->filled('total_floors')) {
                $floor = $this->input('floor_number', $property->floor_number);
                $total = $this->input('total_floors', $property->total_floors);

                if ($floor !== null && $total !== null && $floor > $total) {
                    $validator->errors()->add(
                        'floor_number',
                        'Floor number cannot exceed total floors'
                    );
                }
            }

            // Redecoration fees validation
            if ($this->input('redecoration_fees_required') && !$this->filled('redecoration_fees_amount')) {
                $currentAmount = $property->redecoration_fees_amount;
                if (!$currentAmount) {
                    $validator->errors()->add(
                        'redecoration_fees_amount',
                        'Please specify the redecoration fee amount'
                    );
                }
            }
        });
    }
}
