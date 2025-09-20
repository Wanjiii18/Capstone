<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Karenderia;

echo "🧹 Cleaning up sample users and fixing data:\n";
echo "===========================================\n\n";

try {
    // Step 1: Remove sample karenderias that are not yours
    echo "1️⃣ Removing sample karenderias...\n";
    $sampleKarenderias = Karenderia::whereIn('name', [
        'Lola Maria\'s Kitchen',
        'Tita Linda\'s Lutong Bahay',
        'Test Cebu Karenderia',
        'Mama\'s Kitchen'
    ])->get();
    
    foreach ($sampleKarenderias as $sample) {
        echo "   🗑️ Removing: {$sample->name}\n";
        $sample->delete();
    }
    
    // Step 2: Remove sample users that are not needed
    echo "\n2️⃣ Checking sample users...\n";
    $sampleUsers = User::whereIn('email', [
        'owner@kaplato.com',
        'customer@kaplato.com'
    ])->get();
    
    foreach ($sampleUsers as $sample) {
        echo "   🗑️ Removing sample user: {$sample->email}\n";
        $sample->delete();
    }
    
    // Step 3: Keep admin and your real user
    echo "\n3️⃣ Keeping essential users:\n";
    $admin = User::where('email', 'admin@kaplato.com')->first();
    if ($admin) {
        echo "   ✅ Admin user: {$admin->email}\n";
    }
    
    $yourUser = User::where('email', 'test@gmail.com')->first();
    if ($yourUser) {
        echo "   ✅ Your user: {$yourUser->email}\n";
        
        // Check your karenderia
        $yourKarenderia = Karenderia::where('owner_id', $yourUser->id)->first();
        if ($yourKarenderia) {
            echo "   ✅ Your karenderia: {$yourKarenderia->name}\n";
            
            // Make sure it's approved/active
            if ($yourKarenderia->status !== 'active') {
                echo "   🔧 Setting karenderia status to 'active'...\n";
                $yourKarenderia->status = 'active';
                $yourKarenderia->save();
                echo "   ✅ Status updated to 'active'\n";
            }
        }
    }
    
    // Step 4: Final check
    echo "\n4️⃣ Final database state:\n";
    $users = User::all();
    $karenderias = Karenderia::all();
    
    echo "   👥 Users remaining: {$users->count()}\n";
    foreach ($users as $u) {
        echo "      - {$u->email} ({$u->role})\n";
    }
    
    echo "   🏪 Karenderias remaining: {$karenderias->count()}\n";
    foreach ($karenderias as $k) {
        $owner = User::find($k->owner_id);
        echo "      - {$k->name} (Owner: " . ($owner ? $owner->email : 'Unknown') . ")\n";
    }
    
    echo "\n🎉 Cleanup complete!\n";
    echo "✅ Your login: test@gmail.com\n";
    echo "✅ Your karenderia: " . ($yourKarenderia ? $yourKarenderia->name : 'Not found') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}