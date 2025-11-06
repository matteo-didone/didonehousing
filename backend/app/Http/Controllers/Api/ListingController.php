<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ListingController extends Controller
{
    /**
     * Display a listing of listings
     *
     * Landlords see only their property listings
     * HO and Admin see all listings
     * Tenants see only published listings
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = QueryBuilder::for(Listing::class)
            ->allowedFilters([
                'status',
                AllowedFilter::exact('property_id'),
                AllowedFilter::scope('submitted'),
                AllowedFilter::scope('in_review'),
                AllowedFilter::scope('published'),
            ])
            ->allowedSorts(['created_at', 'monthly_rent', 'published_at'])
            ->with(['property.landlord', 'hoReviewer']);

        // Scope by role
        if ($user->isLandlord()) {
            // Landlords see listings for their properties only
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        } elseif ($user->isTenant()) {
            // Tenants see only published listings
            $query->where('status', Listing::STATUS_PUBLISHED);
        }

        $listings = $query->paginate($request->input('per_page', 15));

        return response()->json($listings);
    }

    /**
     * Store a newly created listing
     * Only landlords can create listings for their approved properties
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only landlords can create listings
        if (!$user->isLandlord()) {
            return response()->json([
                'message' => 'Only landlords can create listings',
            ], 403);
        }

        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'monthly_rent' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'security_deposit' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'condo_fees' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:20'],
            'checklist_data' => ['nullable', 'array'],
        ]);

        // Verify property belongs to landlord
        $property = Property::findOrFail($validated['property_id']);
        if ($property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'You can only create listings for your own properties',
            ], 403);
        }

        // Property must be approved
        if ($property->status !== Property::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Property must be approved before creating a listing',
            ], 422);
        }

        // Check if property already has a listing
        if ($property->listing) {
            return response()->json([
                'message' => 'This property already has a listing',
            ], 422);
        }

        // Set initial status
        $validated['status'] = Listing::STATUS_DRAFT;

        $listing = Listing::create($validated);

        return response()->json([
            'message' => 'Listing created successfully',
            'listing' => $listing->load(['property.landlord', 'hoReviewer']),
        ], 201);
    }

    /**
     * Display the specified listing
     */
    public function show(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Landlords can only view listings for their properties
        if ($user->isLandlord() && $listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to view this listing',
            ], 403);
        }

        // Tenants can only view published listings
        if ($user->isTenant() && $listing->status !== Listing::STATUS_PUBLISHED) {
            return response()->json([
                'message' => 'This listing is not available',
            ], 404);
        }

        $listing->load(['property.landlord', 'hoReviewer', 'documents']);

        return response()->json([
            'listing' => $listing,
        ]);
    }

    /**
     * Update the specified listing
     */
    public function update(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only landlords can update their listings
        if ($listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to update this listing',
            ], 403);
        }

        // Can only update draft listings
        if (!in_array($listing->status, [Listing::STATUS_DRAFT, Listing::STATUS_REJECTED])) {
            return response()->json([
                'message' => 'Can only update draft or rejected listings',
            ], 422);
        }

        $validated = $request->validate([
            'monthly_rent' => ['sometimes', 'numeric', 'min:0', 'max:999999.99'],
            'security_deposit' => ['sometimes', 'numeric', 'min:0', 'max:999999.99'],
            'condo_fees' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:20'],
            'checklist_data' => ['nullable', 'array'],
        ]);

        $listing->update($validated);

        return response()->json([
            'message' => 'Listing updated successfully',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Submit listing for HO review
     */
    public function submit(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only the property owner can submit
        if ($listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to submit this listing',
            ], 403);
        }

        // Can only submit draft or rejected listings
        if (!in_array($listing->status, [Listing::STATUS_DRAFT, Listing::STATUS_REJECTED])) {
            return response()->json([
                'message' => 'Only draft or rejected listings can be submitted',
            ], 422);
        }

        $listing->submit();

        return response()->json([
            'message' => 'Listing submitted for review',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Start review (HO only)
     */
    public function startReview(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only HO can start review
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Only Housing Office staff can start review',
            ], 403);
        }

        // Can only review submitted listings
        if ($listing->status !== Listing::STATUS_SUBMITTED) {
            return response()->json([
                'message' => 'Only submitted listings can be reviewed',
            ], 422);
        }

        $listing->startReview($user);

        return response()->json([
            'message' => 'Review started',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Approve listing (HO only)
     */
    public function approve(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only HO can approve
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Only Housing Office staff can approve listings',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $listing->approve($user, $validated['comments'] ?? null);

        return response()->json([
            'message' => 'Listing approved successfully',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Reject listing (HO only)
     */
    public function reject(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only HO can reject
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Only Housing Office staff can reject listings',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => ['required', 'string', 'max:2000'],
        ]);

        $listing->reject($user, $validated['comments']);

        return response()->json([
            'message' => 'Listing rejected',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Publish listing (Landlord only, after HO approval)
     */
    public function publish(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only the property owner can publish
        if ($listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to publish this listing',
            ], 403);
        }

        try {
            $listing->publish();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Listing published successfully',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Unpublish listing (Landlord only)
     */
    public function unpublish(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only the property owner can unpublish
        if ($listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to unpublish this listing',
            ], 403);
        }

        if ($listing->status !== Listing::STATUS_PUBLISHED) {
            return response()->json([
                'message' => 'Listing is not published',
            ], 422);
        }

        $listing->update(['status' => Listing::STATUS_UNPUBLISHED]);

        return response()->json([
            'message' => 'Listing unpublished successfully',
            'listing' => $listing->fresh(['property.landlord', 'hoReviewer']),
        ]);
    }

    /**
     * Remove the specified listing (soft delete)
     */
    public function destroy(Request $request, Listing $listing): JsonResponse
    {
        $user = $request->user();

        // Only landlords can delete their listings
        if ($listing->property->landlord_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this listing',
            ], 403);
        }

        // Can only delete draft listings
        if ($listing->status !== Listing::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Only draft listings can be deleted',
            ], 422);
        }

        $listing->delete();

        return response()->json([
            'message' => 'Listing deleted successfully',
        ]);
    }
}
