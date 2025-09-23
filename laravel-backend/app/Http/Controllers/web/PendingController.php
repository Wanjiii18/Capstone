<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karenderia;

class PendingController extends Controller
{
    public function index()
    {
        $pendingKarenderias = Karenderia::with('owner')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingCount = $pendingKarenderias->total();

        return view('admin.pending', compact('pendingKarenderias'))->with('pendingCount', $pendingCount);
    }

    public function approve(Request $request, $id)
    {
        try {
            $karenderia = Karenderia::findOrFail($id);
            $karenderia->status = 'approved';
            $karenderia->approved_at = now();
            $karenderia->save();

            return redirect()->route('admin.pending')
                ->with('success', "Karenderia '{$karenderia->name}' has been approved successfully!");
        } catch (\Exception $e) {
            return redirect()->route('admin.pending')
                ->with('error', 'Failed to approve karenderia. Please try again.');
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            $karenderia = Karenderia::findOrFail($id);
            $karenderia->status = 'rejected';
            $karenderia->rejection_reason = $request->rejection_reason;
            $karenderia->rejected_at = now();
            $karenderia->save();

            return redirect()->route('admin.pending')
                ->with('success', "Karenderia '{$karenderia->name}' has been rejected.");
        } catch (\Exception $e) {
            return redirect()->route('admin.pending')
                ->with('error', 'Failed to reject karenderia. Please try again.');
        }
    }

    public function approveUser(Request $request, $id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->verified = true;
            $user->save();

            return redirect()->route('admin.pending')
                ->with('success', "User '{$user->name}' has been approved successfully!");
        } catch (\Exception $e) {
            return redirect()->route('admin.pending')
                ->with('error', 'Failed to approve user. Please try again.');
        }
    }

    public function rejectUser(Request $request, $id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->delete(); // Or you could add a 'rejected' status

            return redirect()->route('admin.pending')
                ->with('success', "User '{$user->name}' has been rejected and removed.");
        } catch (\Exception $e) {
            return redirect()->route('admin.pending')
                ->with('error', 'Failed to reject user. Please try again.');
        }
    }

    public function showPendingDashboard()
    {
        $pendingKarenderias = Karenderia::select('id', 'name', 'description')
            ->where('status', 'pending')
            ->get()
            ->toArray();

        return view('pending.pendingDashboard', ['pendingKarenderias' => $pendingKarenderias]);
    }
}
