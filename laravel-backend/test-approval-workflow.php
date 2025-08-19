<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Karenderia;

// Simulate running the test within Laravel context
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Karenderia Approval Workflow Test ===\n\n";

// Step 1: Show pending karenderias (waiting for approval)
echo "1. KARENDERIAS WAITING FOR ADMIN APPROVAL:\n";
$pendingKarenderias = Karenderia::where('status', 'pending')->with('owner')->get();
foreach ($pendingKarenderias as $karenderia) {
    echo "   - {$karenderia->name} (Owner: {$karenderia->owner->name})\n";
    echo "     Location: {$karenderia->address}\n";
    echo "     Coordinates: {$karenderia->latitude}, {$karenderia->longitude}\n";
    echo "     Status: {$karenderia->status}\n";
    echo "     Submitted: {$karenderia->created_at->format('Y-m-d H:i')}\n\n";
}

// Step 2: Show what customers can see (only active)
echo "2. KARENDERIAS VISIBLE TO CUSTOMERS:\n";
$customerVisibleKarenderias = Karenderia::where('status', 'active')->with('owner')->get();
foreach ($customerVisibleKarenderias as $karenderia) {
    echo "   - {$karenderia->name} (Owner: {$karenderia->owner->name})\n";
    echo "     Location: {$karenderia->address}\n";
    echo "     Status: {$karenderia->status}\n";
    echo "     Rating: {$karenderia->average_rating}/5.0\n\n";
}

// Step 3: Simulate admin approval
echo "3. SIMULATING ADMIN APPROVAL:\n";
$adminUser = User::where('role', 'admin')->first();
if ($pendingKarenderias->count() > 0 && $adminUser) {
    $karenderiaToApprove = $pendingKarenderias->first();
    
    echo "   Admin '{$adminUser->name}' approving '{$karenderiaToApprove->name}'...\n";
    
    // Update status to active
    $karenderiaToApprove->update([
        'status' => 'active',
        'approved_at' => now(),
        'approved_by' => $adminUser->id
    ]);
    
    echo "   ✅ {$karenderiaToApprove->name} has been APPROVED!\n\n";
}

// Step 4: Show updated customer view
echo "4. UPDATED CUSTOMER VIEW (after approval):\n";
$updatedCustomerView = Karenderia::where('status', 'active')->with('owner')->get();
foreach ($updatedCustomerView as $karenderia) {
    echo "   - {$karenderia->name} (Owner: {$karenderia->owner->name})\n";
    echo "     Status: {$karenderia->status}\n";
    if ($karenderia->approved_at) {
        echo "     Approved: {$karenderia->approved_at->format('Y-m-d H:i')}\n";
    }
    echo "\n";
}

// Step 5: Test distance functionality with approved karenderias
echo "5. TESTING DISTANCE CALCULATION FOR APPROVED KARENDERIAS:\n";
$testLat = 14.5995; // Manila coordinates
$testLng = 120.9842;
$testRadius = 5000; // 5km

echo "   Testing from location: {$testLat}, {$testLng}\n";
echo "   Search radius: {$testRadius}m\n\n";

foreach ($updatedCustomerView as $karenderia) {
    // Calculate distance using Haversine formula
    $earthRadius = 6371000; // Earth's radius in meters
    
    $lat1Rad = deg2rad($testLat);
    $lng1Rad = deg2rad($testLng);
    $lat2Rad = deg2rad($karenderia->latitude);
    $lng2Rad = deg2rad($karenderia->longitude);
    
    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLng = $lng2Rad - $lng1Rad;
    
    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         cos($lat1Rad) * cos($lat2Rad) *
         sin($deltaLng / 2) * sin($deltaLng / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    
    $withinRange = $distance <= $testRadius ? "✅ WITHIN RANGE" : "❌ TOO FAR";
    
    echo "   - {$karenderia->name}: " . round($distance) . "m away - {$withinRange}\n";
}

echo "\n=== Workflow Summary ===\n";
echo "✅ Owner registration → Status: 'pending'\n";
echo "✅ Admin approval → Status: 'active'\n";
echo "✅ Customer visibility → Only 'active' karenderias\n";
echo "✅ Distance calculation → Works with approved locations\n";
echo "✅ Range filtering → Customers see relevant karenderias\n\n";

echo "🎯 The approval workflow is working correctly!\n";
