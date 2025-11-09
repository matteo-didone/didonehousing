<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties
     *
     * Landlords see only their properties
     * HO and Admin see all properties
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = QueryBuilder::for(Property::class)
            ->allowedFilters([
                'status',
                'city',
                'province',
                AllowedFilter::exact('landlord_id'),
                AllowedFilter::scope('pending_review'),
                AllowedFilter::scope('approved'),
            ])
            ->allowedSorts(['created_at', 'updated_at', 'city'])
            ->with(['landlord', 'listing', 'hoReviewer']);

        // Scope by role
        if ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        }

        $properties = $query->paginate($request->input('per_page', 15));

        return response()->json($properties);
    }

    /**
     * Store a newly created property
     * Only landlords can create properties
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only landlords can create properties
        if (!$user->isLandlord()) {
            return response()->json([
                'message' => 'Only landlords can create properties',
            ], 403);
        }

        $validated = $request->validate([
            // Address
            'street_name' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:50'],
            'apt_number' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:2'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:2'],

            // Cadastral Data
            'cadastral_sheet_number' => ['nullable', 'string', 'max:50'],
            'cadastral_plot_number' => ['nullable', 'string', 'max:50'],
            'cadastral_unit_number' => ['nullable', 'string', 'max:50'],
            'cadastral_tax_evaluation' => ['nullable', 'numeric', 'min:0'],
            'cadastral_category' => ['nullable', 'string', 'max:10'],

            // Rooms
            'living_rooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'dining_rooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'bedrooms' => ['required', 'integer', 'min:1', 'max:10'],
            'bathrooms' => ['required', 'integer', 'min:1', 'max:10'],
            'kitchen' => ['nullable', 'integer', 'min:0', 'max:10'],
            'basement' => ['boolean'],
            'attic' => ['boolean'],
            'garage' => ['boolean'],
            'yard' => ['boolean'],

            // Property Details
            'furnished' => ['boolean'],
            'pets_allowed' => ['boolean'],
            'heating_type' => ['nullable', 'string', Rule::in([
                Property::HEATING_CITY_GAS,
                Property::HEATING_LPG_COUPONS,
                Property::HEATING_LPG_NO_COUPONS,
                Property::HEATING_FUEL,
                Property::HEATING_ELECTRIC,
                Property::HEATING_SEPARATE_SYSTEM,
                Property::HEATING_SEPARATE_METER,
                Property::HEATING_SHARED_US,
                Property::HEATING_SHARED_ITALIANS,
            ])],
            'cooling_type' => ['nullable', 'string', 'max:255'],
        ]);

        // Add landlord_id and set initial status
        $validated['landlord_id'] = $user->id;
        $validated['status'] = Property::STATUS_DRAFT;

        $property = Property::create($validated);

        return response()->json([
            'message' => 'Property created successfully',
            'property' => $property->load(['landlord', 'listing']),
        ], 201);
    }

    /**
     * Display the specified property
     */
    public function show(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Landlords can only view their own properties
        if ($user->isLandlord() && $property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to view this property',
            ], 403);
        }

        $property->load(['landlord', 'listing', 'documents', 'hoReviewer']);

        return response()->json([
            'property' => $property,
        ]);
    }

    /**
     * Update the specified property
     * Landlords can update their own properties
     * HO can update review status
     */
    public function update(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Landlords can only update their own properties
        if ($user->isLandlord() && $property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to update this property',
            ], 403);
        }

        // Only landlords can update property details (not HO)
        if ($user->isLandlord()) {
            $validated = $request->validate([
                // Address
                'street_name' => ['sometimes', 'string', 'max:255'],
                'house_number' => ['sometimes', 'string', 'max:50'],
                'apt_number' => ['nullable', 'string', 'max:50'],
                'city' => ['sometimes', 'string', 'max:255'],
                'province' => ['sometimes', 'string', 'max:2'],
                'postal_code' => ['sometimes', 'string', 'max:10'],
                'country' => ['sometimes', 'string', 'max:2'],

                // Cadastral Data
                'cadastral_sheet_number' => ['nullable', 'string', 'max:50'],
                'cadastral_plot_number' => ['nullable', 'string', 'max:50'],
                'cadastral_unit_number' => ['nullable', 'string', 'max:50'],
                'cadastral_tax_evaluation' => ['nullable', 'numeric', 'min:0'],
                'cadastral_category' => ['nullable', 'string', 'max:10'],

                // Rooms
                'living_rooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
                'dining_rooms' => ['sometimes', 'integer', 'min:0', 'max:10'],
                'bedrooms' => ['sometimes', 'integer', 'min:1', 'max:10'],
                'bathrooms' => ['sometimes', 'integer', 'min:1', 'max:10'],
                'kitchen' => ['sometimes', 'integer', 'min:0', 'max:10'],
                'basement' => ['sometimes', 'boolean'],
                'attic' => ['sometimes', 'boolean'],
                'garage' => ['sometimes', 'boolean'],
                'yard' => ['sometimes', 'boolean'],

                // Property Details
                'furnished' => ['sometimes', 'boolean'],
                'pets_allowed' => ['sometimes', 'boolean'],
                'heating_type' => ['sometimes', 'string', Rule::in([
                    Property::HEATING_CITY_GAS,
                    Property::HEATING_LPG_COUPONS,
                    Property::HEATING_LPG_NO_COUPONS,
                    Property::HEATING_FUEL,
                    Property::HEATING_ELECTRIC,
                    Property::HEATING_SEPARATE_SYSTEM,
                    Property::HEATING_SEPARATE_METER,
                    Property::HEATING_SHARED_US,
                    Property::HEATING_SHARED_ITALIANS,
                ])],
                'cooling_type' => ['sometimes', 'string', 'max:255'],
            ]);

            $property->update($validated);
        }

        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property->fresh(['landlord', 'listing', 'hoReviewer']),
        ]);
    }

    /**
     * Submit property for HO review
     */
    public function submitForReview(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Only the property owner can submit for review
        if ($property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to submit this property',
            ], 403);
        }

        // Can only submit draft or rejected properties
        if (!in_array($property->status, [Property::STATUS_DRAFT, Property::STATUS_REJECTED])) {
            return response()->json([
                'message' => 'Property must be in draft or rejected status to submit',
            ], 422);
        }

        $property->update([
            'status' => Property::STATUS_PENDING_REVIEW,
        ]);

        return response()->json([
            'message' => 'Property submitted for review',
            'property' => $property->fresh(['landlord', 'listing']),
        ]);
    }

    /**
     * Approve property (HO only)
     */
    public function approve(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Only HO can approve
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Only Housing Office staff can approve properties',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $property->update([
            'status' => Property::STATUS_APPROVED,
            'ho_reviewer_id' => $user->id,
            'ho_reviewed_at' => now(),
            'ho_comments' => $validated['comments'] ?? null,
        ]);

        return response()->json([
            'message' => 'Property approved successfully',
            'property' => $property->fresh(['landlord', 'listing', 'hoReviewer']),
        ]);
    }

    /**
     * Reject property (HO only)
     */
    public function reject(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Only HO can reject
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Only Housing Office staff can reject properties',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => ['required', 'string', 'max:2000'],
        ]);

        $property->update([
            'status' => Property::STATUS_REJECTED,
            'ho_reviewer_id' => $user->id,
            'ho_reviewed_at' => now(),
            'ho_comments' => $validated['comments'],
        ]);

        return response()->json([
            'message' => 'Property rejected',
            'property' => $property->fresh(['landlord', 'listing', 'hoReviewer']),
        ]);
    }

    /**
     * Remove the specified property (soft delete)
     */
    public function destroy(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        // Landlords can only delete their own properties
        if ($user->isLandlord() && $property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this property',
            ], 403);
        }

        // Can only delete draft properties (or corrupted properties with null status)
        if ($property->status !== Property::STATUS_DRAFT && $property->status !== null) {
            return response()->json([
                'message' => 'Only draft properties can be deleted',
            ], 422);
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ]);
    }
}
