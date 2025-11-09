<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
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
    public function store(StorePropertyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Add landlord_id and set initial status
        $validated['landlord_id'] = $request->user()->id;
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
    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        $validated = $request->validated();
        $property->update($validated);

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
