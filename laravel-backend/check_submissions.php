<?php
echo "🔍 Checking Your Recent Submission\n";
echo "================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the most recent pending applications
    $pendingApps = \App\Models\Karenderia::with('owner')
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    echo "⏳ Recent Pending Applications: {$pendingApps->count()}\n\n";
    
    foreach ($pendingApps as $index => $app) {
        echo "📋 Application #" . ($index + 1) . ":\n";
        echo "  🏪 Karenderia: {$app->name}\n";
        echo "  👤 Owner: " . ($app->owner ? $app->owner->name : 'Unknown') . "\n";
        echo "  📧 Email: " . ($app->owner ? $app->owner->email : 'Unknown') . "\n";
        echo "  📍 Address: {$app->address}\n";
        echo "  📅 Submitted: {$app->created_at}\n";
        echo "  🆔 ID: {$app->id}\n\n";
    }
    
    if ($pendingApps->count() > 0) {
        $latest = $pendingApps->first();
        echo "✅ Your most recent submission:\n";
        echo "  • Name: {$latest->name}\n";
        echo "  • Status: {$latest->status}\n";
        echo "  • Location: {$latest->latitude}, {$latest->longitude}\n";
        echo "  • Submitted: {$latest->created_at}\n\n";
        
        echo "🎯 Next Steps:\n";
        echo "  1. Open: http://127.0.0.1:8000/admin-karenderia-dashboard.html\n";
        echo "  2. Find your application (ID: {$latest->id})\n";
        echo "  3. Click 'Approve' button\n";
        echo "  4. Your karenderia will become active!\n\n";
    } else {
        echo "❓ No pending applications found.\n";
        echo "💡 Either:\n";
        echo "  - Your submission wasn't successful\n";
        echo "  - It was already approved\n";
        echo "  - There was an error\n\n";
        
        // Check for active karenderias
        $activeKars = \App\Models\Karenderia::where('status', 'active')->count();
        echo "🏪 Active karenderias: {$activeKars}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}