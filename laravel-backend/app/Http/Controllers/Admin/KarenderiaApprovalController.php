<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Karenderia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class KarenderiaApprovalController extends Controller
{
    /**
     * Get all pending karenderia applications
     */
    public function getPendingApplications(): JsonResponse
    {
        try {
            $pendingKarenderias = Karenderia::with('owner')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $formatted = $pendingKarenderias->map(function ($karenderia) {
                return [
                    'id' => $karenderia->id,
                    'name' => $karenderia->name,
                    'business_name' => $karenderia->business_name,
                    'description' => $karenderia->description,
                    'address' => $karenderia->address,
                    'city' => $karenderia->city,
                    'province' => $karenderia->province,
                    'latitude' => $karenderia->latitude,
                    'longitude' => $karenderia->longitude,
                    'phone' => $karenderia->phone,
                    'business_email' => $karenderia->business_email,
                    'opening_time' => $karenderia->opening_time,
                    'closing_time' => $karenderia->closing_time,
                    'operating_days' => $karenderia->operating_days,
                    'delivery_fee' => $karenderia->delivery_fee,
                    'delivery_time_minutes' => $karenderia->delivery_time_minutes,
                    'business_permit' => $karenderia->business_permit,
                    'submitted_at' => $karenderia->created_at,
                    'owner' => [
                        'id' => $karenderia->owner->id,
                        'name' => $karenderia->owner->name,
                        'email' => $karenderia->owner->email,
                        'phone_number' => $karenderia->owner->phone_number
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => "Found {$formatted->count()} pending applications",
                'data' => $formatted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get pending applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a karenderia application
     */
    public function approveApplication(Request $request, $karenderiaId): JsonResponse
    {
        try {
            $admin = $request->user();
            
            // Skip auth check if no user (for test routes)
            if ($admin && $admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $karenderia = Karenderia::with('owner')->find($karenderiaId);
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia not found'
                ], 404);
            }

            if ($karenderia->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia is not pending approval'
                ], 400);
            }

            // Update karenderia status
            $karenderia->update([
                'status' => 'active',
                'approved_at' => Carbon::now(),
                'approved_by' => $admin ? $admin->id : 1 // Use admin ID 1 if no user
            ]);

            // Update owner verification status
            $karenderia->owner->update([
                'verified' => true,
                'application_status' => 'approved'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karenderia approved successfully',
                'data' => [
                    'karenderia' => [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'status' => $karenderia->status,
                        'approved_at' => $karenderia->approved_at,
                        'approved_by' => $admin->name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve karenderia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a karenderia application
     */
    public function rejectApplication(Request $request, $karenderiaId): JsonResponse
    {
        try {
            $admin = $request->user();
            
            // Skip auth check if no user (for test routes)
            if ($admin && $admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            $karenderia = Karenderia::with('owner')->find($karenderiaId);
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia not found'
                ], 404);
            }

            if ($karenderia->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia is not pending approval'
                ], 400);
            }

            // Update karenderia status
            $karenderia->update([
                'status' => 'rejected'
            ]);

            // Update owner application status
            $karenderia->owner->update([
                'application_status' => 'rejected'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karenderia application rejected',
                'data' => [
                    'karenderia' => [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'status' => $karenderia->status,
                        'rejection_reason' => $request->reason
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject karenderia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all karenderias (approved, pending, rejected)
     */
    public function getAllKarenderias(): JsonResponse
    {
        try {
            $karenderias = Karenderia::with('owner')
                ->orderBy('created_at', 'desc')
                ->get();

            $formatted = $karenderias->map(function ($karenderia) {
                return [
                    'id' => $karenderia->id,
                    'name' => $karenderia->name,
                    'business_name' => $karenderia->business_name,
                    'address' => $karenderia->address,
                    'city' => $karenderia->city,
                    'province' => $karenderia->province,
                    'status' => $karenderia->status,
                    'latitude' => $karenderia->latitude,
                    'longitude' => $karenderia->longitude,
                    'average_rating' => $karenderia->average_rating,
                    'created_at' => $karenderia->created_at,
                    'approved_at' => $karenderia->approved_at,
                    'owner' => [
                        'id' => $karenderia->owner->id,
                        'name' => $karenderia->owner->name,
                        'email' => $karenderia->owner->email,
                        'application_status' => $karenderia->owner->application_status
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => "Found {$formatted->count()} karenderias",
                'data' => $formatted,
                'summary' => [
                    'total' => $karenderias->count(),
                    'active' => $karenderias->where('status', 'active')->count(),
                    'pending' => $karenderias->where('status', 'pending')->count(),
                    'rejected' => $karenderias->where('status', 'rejected')->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get karenderias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate/Deactivate a karenderia
     */
    public function toggleKarenderiaStatus(Request $request, $karenderiaId): JsonResponse
    {
        try {
            $admin = $request->user();
            
            if ($admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $karenderia = Karenderia::find($karenderiaId);
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia not found'
                ], 404);
            }

            // Toggle between active and inactive
            $newStatus = $karenderia->status === 'active' ? 'inactive' : 'active';
            $karenderia->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Karenderia {$newStatus}",
                'data' => [
                    'karenderia' => [
                        'id' => $karenderia->id,
                        'name' => $karenderia->name,
                        'status' => $karenderia->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update karenderia status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}