<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karenderia;
use App\Models\User;

echo "🔍 Checking Talisay Karenderias...\n\n";

$talisayKarenderias = Karenderia::where('address', 'LIKE', '%Talisay%')->get();

echo "Found " . $talisayKarenderias->count() . " Talisay karenderias:\n\n";

foreach ($talisayKarenderias as $k) {
    echo "🏪 {$k->name}\n";
    echo "   📍 {$k->address}\n";
    echo "   📱 {$k->phone}\n";
    echo "   🌟 Rating: {$k->rating}/5.0\n";
    echo "   💰 Delivery Fee: ₱{$k->delivery_fee}\n";
    echo "   🍽️  Menu Items: " . $k->menuItems()->count() . "\n\n";
}

// Also check users
$talisayOwners = User::where('email', 'LIKE', '%talisay_owner%')->get();
echo "📧 Test Owner Accounts Created: " . $talisayOwners->count() . "\n\n";

foreach ($talisayOwners as $owner) {
    echo "👤 {$owner->name} - {$owner->email}\n";
}

if ($talisayKarenderias->count() > 0) {
    echo "\n✅ SUCCESS: Talisay karenderias were created successfully!\n";
    echo "🌴 You can now test the app with these local businesses.\n";
} else {
    echo "\n❌ No Talisay karenderias found. The script may have failed.\n";
}
?>
