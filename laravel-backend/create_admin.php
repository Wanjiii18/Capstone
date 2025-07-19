<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = new User();
$admin->name = 'Admin User';
$admin->email = 'admin@karenderia.com';
$admin->password = Hash::make('admin123');
$admin->email_verified_at = now();
$admin->save();

// Create admin profile
$profile = new UserProfile();
$profile->user_id = $admin->id;
$profile->role = 'admin';
$profile->save();

echo "✅ Admin user created successfully!\n";
echo "📧 Email: admin@karenderia.com\n";
echo "🔑 Password: admin123\n";
echo "👤 User ID: {$admin->id}\n";
