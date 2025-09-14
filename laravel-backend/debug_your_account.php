<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug: Test with Your Account ===\n\n";

echo "Please provide the email you used to register and login:\n";
echo "(This will help us see exactly what data your account receives)\n\n";

// List all karenderia owners for reference
echo "Available karenderia owner accounts:\n";
$owners = DB::table('users')
    ->where('role', 'karenderia_owner')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('karenderias')
              ->whereRaw('karenderias.owner_id = users.id');
    })
    ->get();

foreach ($owners as $owner) {
    $karenderia = DB::table('karenderias')->where('owner_id', $owner->id)->first();
    echo "  Email: {$owner->email}\n";
    echo "  Owner Name: {$owner->name}\n";
    echo "  Karenderia Name: {$karenderia->name}\n";
    echo "  Business Name: {$karenderia->business_name}\n";
    echo "  Status: {$karenderia->status}\n";
    echo "  ---\n";
}

echo "\nTo test your specific account, please run:\n";
echo "php test_your_account.php YOUR_EMAIL_HERE\n\n";

echo "For example:\n";
echo "php test_your_account.php tabada@gmail.com\n\n";

echo "=== Possible Causes of 'Mama's Kitchen' Display ===\n";
echo "1. Browser cache - Try clearing browser data or incognito mode\n";
echo "2. Different account - Make sure you're using the correct login\n";
echo "3. Frontend default fallback - Check if API call is failing\n";
echo "4. Mock data in development - Check if frontend has test data\n";
echo "5. Another browser tab - Close all tabs and try again\n\n";

echo "To debug frontend:\n";
echo "1. Open browser Developer Tools (F12)\n";
echo "2. Go to Network tab\n";
echo "3. Login to your account\n";
echo "4. Look for API call to '/karenderias/my-karenderia'\n";
echo "5. Check what data is returned\n";

?>