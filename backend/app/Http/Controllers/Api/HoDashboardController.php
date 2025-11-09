<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoDashboardController extends Controller
{
    /**
     * Get Housing Office dashboard statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only HO users can access these stats
        if (!$user->isHousingOffice()) {
            return response()->json([
                'message' => 'Unauthorized. Only Housing Office staff can access this resource.',
            ], 403);
        }

        $stats = [
            'pendingApprovals' => Property::where('status', Property::STATUS_PENDING_REVIEW)->count(),
            'totalProperties' => Property::whereIn('status', [
                Property::STATUS_APPROVED,
                Property::STATUS_PENDING_REVIEW,
            ])->count(),
            'activeTenants' => 0, // TODO: Implement when tenant contracts are ready
            'openTickets' => 0, // TODO: Implement when maintenance tickets are ready
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
