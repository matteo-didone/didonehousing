<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    /**
     * Get all published properties/listings
     */
    public function index(Request $request): JsonResponse
    {
        $query = Listing::with(['property.landlord'])
            ->published()
            ->whereHas('property', function ($query) {
                $query->whereNull('deleted_at');
            });

        // Apply filters
        if ($request->has('city')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }

        if ($request->has('bedrooms')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('bedrooms', '>=', $request->bedrooms);
            });
        }

        if ($request->has('min_rent')) {
            $query->where('monthly_rent', '>=', $request->min_rent);
        }

        if ($request->has('max_rent')) {
            $query->where('monthly_rent', '<=', $request->max_rent);
        }

        if ($request->has('furnished')) {
            $furnished = filter_var($request->furnished, FILTER_VALIDATE_BOOLEAN);
            $query->whereHas('property', function ($q) use ($furnished) {
                $q->where('furnished', $furnished);
            });
        }

        if ($request->has('pets_allowed')) {
            $petsAllowed = filter_var($request->pets_allowed, FILTER_VALIDATE_BOOLEAN);
            $query->whereHas('property', function ($q) use ($petsAllowed) {
                $q->where('pets_allowed', $petsAllowed);
            });
        }

        $listings = $query->latest('published_at')->paginate(12);

        // Transform the data to match frontend expectations
        $transformedData = $listings->map(function ($listing) {
            return $this->transformListing($listing);
        });

        return response()->json([
            'data' => $transformedData,
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ]
        ]);
    }

    /**
     * Get a single property/listing by ID
     */
    public function show($id): JsonResponse
    {
        $listing = Listing::with(['property.landlord', 'property.documents'])
            ->published()
            ->findOrFail($id);

        return response()->json([
            'data' => $this->transformListing($listing, true)
        ]);
    }

    /**
     * Transform Listing + Property data to match frontend format
     */
    private function transformListing(Listing $listing, bool $includeDetails = false): array
    {
        $property = $listing->property;

        $data = [
            'id' => $listing->id,
            'title' => $this->generateTitle($property),
            'description' => $this->generateDescription($property),
            'address' => $property->street_name . ' ' . $property->house_number,
            'city' => $property->city,
            'zip_code' => $property->postal_code,
            'property_type' => $this->determinePropertyType($property),
            'bedrooms' => $property->bedrooms ?? 0,
            'bathrooms' => $property->bathrooms ?? 0,
            'square_meters' => $this->calculateSquareMeters($property),
            'monthly_rent' => (float) $listing->monthly_rent,
            'deposit' => (float) $listing->security_deposit,
            'available_from' => $listing->published_at?->toDateString() ?? now()->toDateString(),
            'furnished' => (bool) $property->furnished,
            'pets_allowed' => (bool) $property->pets_allowed,
            'smoking_allowed' => false, // Not in current schema
            'utilities_included' => false, // Not in current schema
            'status' => $listing->status === Listing::STATUS_PUBLISHED ? 'available' : 'rented',
            'photos' => [], // TODO: Implement photo handling
            'created_at' => $listing->created_at->toIso8601String(),
            'updated_at' => $listing->updated_at->toIso8601String(),
        ];

        if ($includeDetails && $property->landlord) {
            $data['owner'] = [
                'id' => $property->landlord->id,
                'name' => $property->landlord->name,
                'email' => $property->landlord->email,
            ];
        }

        return $data;
    }

    /**
     * Generate a title for the property
     */
    private function generateTitle(Property $property): string
    {
        $type = $this->determinePropertyType($property);
        $typeLabel = match($type) {
            'house' => 'Casa',
            'apartment' => 'Appartamento',
            'room' => 'Stanza',
            default => 'Proprietà'
        };

        return "{$typeLabel} a {$property->city}";
    }

    /**
     * Generate a description for the property
     */
    private function generateDescription(Property $property): string
    {
        $parts = [];

        if ($property->bedrooms) {
            $parts[] = "{$property->bedrooms} camere da letto";
        }
        if ($property->bathrooms) {
            $parts[] = "{$property->bathrooms} bagni";
        }
        if ($property->furnished) {
            $parts[] = "arredato";
        }
        if ($property->garage) {
            $parts[] = "con garage";
        }
        if ($property->yard) {
            $parts[] = "con cortile";
        }

        $description = ucfirst(implode(', ', $parts));
        return $description ?: "Proprietà in affitto a {$property->city}";
    }

    /**
     * Determine property type from room configuration
     */
    private function determinePropertyType(Property $property): string
    {
        // If it has multiple rooms and dining/living rooms, it's likely a house or apartment
        $totalRooms = ($property->bedrooms ?? 0) +
                      ($property->living_rooms ?? 0) +
                      ($property->dining_rooms ?? 0);

        if ($totalRooms >= 4) {
            return 'house';
        } elseif ($totalRooms >= 2) {
            return 'apartment';
        } else {
            return 'room';
        }
    }

    /**
     * Calculate approximate square meters (placeholder logic)
     */
    private function calculateSquareMeters(Property $property): int
    {
        // Rough estimation: 15-20 sqm per room
        $rooms = ($property->bedrooms ?? 0) +
                 ($property->living_rooms ?? 0) +
                 ($property->dining_rooms ?? 0);

        return max(30, $rooms * 18); // Minimum 30 sqm
    }
}
