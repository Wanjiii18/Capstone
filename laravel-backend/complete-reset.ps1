# KaPlato Complete Database Reset Script
Write-Host "=== KaPlato Complete Database Reset ===" -ForegroundColor Red
Write-Host "This will completely remove and recreate the database!" -ForegroundColor Yellow

# Navigate to Laravel directory
Set-Location "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend"

Write-Host "`n1. Clearing all Laravel cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

Write-Host "`n2. Dropping existing database completely..." -ForegroundColor Red

# Drop database if exists and create fresh
$resetDatabase = @"
DROP DATABASE IF EXISTS kaplato_db;
CREATE DATABASE kaplato_db;
USE kaplato_db;
SELECT 'Database kaplato_db created successfully' as status;
"@

try {
    $resetDatabase | mysql -u root -proot
    Write-Host "✅ Database dropped and recreated successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to reset database. Is MySQL running?" -ForegroundColor Red
    Write-Host "Please start XAMPP/WAMP or MySQL service first" -ForegroundColor Yellow
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    return
}

Write-Host "`n3. Running fresh migrations..." -ForegroundColor Yellow
php artisan migrate:fresh --force

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migrations completed successfully" -ForegroundColor Green
} else {
    Write-Host "❌ Migrations failed" -ForegroundColor Red
    return
}

Write-Host "`n4. Creating fresh test users..." -ForegroundColor Yellow

# Create users script
$createUsersScript = @"
<?php
require 'vendor/autoload.php';
`$app = require_once 'bootstrap/app.php';
`$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating test users...\n";

try {
    // Admin user
    `$admin = \App\Models\User::create([
        'name' => 'KaPlato Administrator',
        'email' => 'admin@kaplato.com',
        'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        'role' => 'admin',
        'verified' => true,
        'email_verified_at' => now(),
    ]);
    echo "✅ Admin created: {`$admin->email}\n";
    
    // Karenderia Owner
    `$owner = \App\Models\User::create([
        'name' => 'Test Karenderia Owner',
        'email' => 'owner@kaplato.com',
        'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
        'role' => 'karenderia_owner',
        'verified' => true,
        'email_verified_at' => now(),
    ]);
    echo "✅ Owner created: {`$owner->email}\n";
    
    // Customer
    `$customer = \App\Models\User::create([
        'name' => 'Test Customer',
        'email' => 'customer@kaplato.com',
        'password' => \Illuminate\Support\Facades\Hash::make('customer123'),
        'role' => 'customer',
        'verified' => true,
        'email_verified_at' => now(),
    ]);
    echo "✅ Customer created: {`$customer->email}\n";
    
    echo "\n=== Test Authentication ===\n";
    
    // Test owner login
    `$credentials = ['email' => 'owner@kaplato.com', 'password' => 'owner123'];
    if (\Illuminate\Support\Facades\Auth::attempt(`$credentials)) {
        echo "✅ Owner login test: SUCCESS\n";
        `$user = \Illuminate\Support\Facades\Auth::user();
        `$token = `$user->createToken('test_token')->plainTextToken;
        echo "✅ Token generated: " . substr(`$token, 0, 20) . "...\n";
        \Illuminate\Support\Facades\Auth::logout();
    } else {
        echo "❌ Owner login test: FAILED\n";
    }
    
    echo "\nAll users created successfully!\n";
    
} catch (Exception `$e) {
    echo "❌ Error creating users: " . `$e->getMessage() . "\n";
}
"@

# Write and execute the PHP script
$createUsersScript | Out-File -FilePath "temp_create_users.php" -Encoding UTF8
php temp_create_users.php
Remove-Item "temp_create_users.php"

Write-Host "`n5. Verifying database setup..." -ForegroundColor Yellow

# Check tables
$checkTables = "USE kaplato_db; SHOW TABLES;" | mysql -u root -proot
Write-Host "Database tables created:" -ForegroundColor Cyan
$checkTables

# Check users
$checkUsers = "USE kaplato_db; SELECT email, role FROM users;" | mysql -u root -proot
Write-Host "`nUsers in database:" -ForegroundColor Cyan
$checkUsers

Write-Host "`n=== Reset Complete! ===" -ForegroundColor Green
Write-Host "`nFresh test accounts created:" -ForegroundColor Cyan
Write-Host "🔑 Admin: admin@kaplato.com / admin123" -ForegroundColor White
Write-Host "🏪 Owner: owner@kaplato.com / owner123" -ForegroundColor White
Write-Host "👤 Customer: customer@kaplato.com / customer123" -ForegroundColor White

Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Start Laravel server: php artisan serve --port=8000" -ForegroundColor White
Write-Host "2. Try logging in with: owner@kaplato.com / owner123" -ForegroundColor White

Write-Host "`nDatabase Info:" -ForegroundColor Cyan
Write-Host "- Database: kaplato_db" -ForegroundColor White
Write-Host "- Host: 127.0.0.1:3306" -ForegroundColor White
Write-Host "- User: root" -ForegroundColor White
