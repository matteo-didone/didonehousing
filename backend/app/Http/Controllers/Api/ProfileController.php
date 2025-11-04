<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['roles', 'permissions']);

        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'locale' => $user->locale,
            'two_factor_enabled' => $user->two_factor_enabled,
            'last_login_at' => $user->last_login_at,
            'email_verified_at' => $user->email_verified_at,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];

        // Add profile data based on role
        if ($user->isTenant()) {
            $data['profile'] = $user->tenantProfile;
        } elseif ($user->isLandlord()) {
            $data['profile'] = $user->landlordProfile;
        } elseif ($user->isHousingOffice()) {
            $data['profile'] = $user->hoProfile;
        } elseif ($user->isVendor()) {
            $data['profile'] = $user->vendorProfile;
        }

        return response()->json($data);
    }

    /**
     * Update user profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'locale' => ['sometimes', 'in:en,it'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Update role-specific profile
     */
    public function updateRoleProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get the appropriate profile based on role
        if ($user->isTenant()) {
            $profile = $user->tenantProfile;
            $validated = $request->validate([
                'rank' => ['sometimes', 'string'],
                'branch' => ['sometimes', 'string'],
                'unit' => ['sometimes', 'string'],
                'sponsor_name' => ['sometimes', 'string'],
                'sponsor_phone' => ['sometimes', 'string'],
                'pcs_date' => ['sometimes', 'date'],
                'deros_date' => ['sometimes', 'date'],
                'family_size' => ['sometimes', 'integer', 'min:1'],
                'has_pets' => ['sometimes', 'boolean'],
                'pet_details' => ['nullable', 'string'],
                'oha_amount' => ['sometimes', 'numeric', 'min:0'],
                'oha_currency' => ['sometimes', 'in:USD,EUR'],
                'special_requirements' => ['nullable', 'string'],
            ]);
        } elseif ($user->isLandlord()) {
            $profile = $user->landlordProfile;
            $validated = $request->validate([
                'company_name' => ['sometimes', 'string'],
                'tax_id' => ['sometimes', 'string'],
                'business_type' => ['sometimes', 'in:individual,company'],
                'address' => ['sometimes', 'string'],
                'city' => ['sometimes', 'string'],
                'province' => ['sometimes', 'string', 'max:2'],
                'postal_code' => ['sometimes', 'string'],
                'country' => ['sometimes', 'string', 'max:2'],
                'bank_name' => ['sometimes', 'string'],
                'iban' => ['sometimes', 'string'],
                'swift_bic' => ['sometimes', 'string'],
                'cedolare_secca' => ['sometimes', 'boolean'],
            ]);
        } elseif ($user->isVendor()) {
            $profile = $user->vendorProfile;
            $validated = $request->validate([
                'company_name' => ['sometimes', 'string'],
                'tax_id' => ['sometimes', 'string'],
                'services_offered' => ['sometimes', 'array'],
                'address' => ['sometimes', 'string'],
                'city' => ['sometimes', 'string'],
                'province' => ['sometimes', 'string', 'max:2'],
                'postal_code' => ['sometimes', 'string'],
                'service_area' => ['sometimes', 'array'],
                'website' => ['sometimes', 'url'],
                'emergency_available' => ['sometimes', 'boolean'],
            ]);
        } else {
            return response()->json([
                'message' => 'Profile update not available for this role',
            ], 403);
        }

        $profile->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }
}
