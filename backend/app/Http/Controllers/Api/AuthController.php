<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenantProfile;
use App\Models\LandlordProfile;
use App\Models\HoProfile;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'locale' => ['required', 'in:en,it'],
            'role' => ['required', 'in:tenant,landlord,vendor'],
            
            // Profile data (conditional based on role)
            'profile' => ['sometimes', 'array'],
        ]);

        // Create user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'locale' => $validated['locale'],
            'is_active' => true,
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        // Create corresponding profile
        $this->createProfile($user, $validated['role'], $request->input('profile', []));

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['roles', 'permissions']);

        return response()->json([
            'user' => $this->getUserData($user),
        ]);
    }

    /**
     * Create user profile based on role
     */
    private function createProfile(User $user, string $role, array $profileData): void
    {
        switch ($role) {
            case 'tenant':
                TenantProfile::create(array_merge([
                    'user_id' => $user->id,
                ], $profileData));
                break;

            case 'landlord':
                LandlordProfile::create(array_merge([
                    'user_id' => $user->id,
                ], $profileData));
                break;

            case 'vendor':
                VendorProfile::create(array_merge([
                    'user_id' => $user->id,
                ], $profileData));
                break;
        }
    }

    /**
     * Get formatted user data with profile and roles
     */
    private function getUserData(User $user): array
    {
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

        return $data;
    }
}
