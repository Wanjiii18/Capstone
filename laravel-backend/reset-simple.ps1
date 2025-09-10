# KaPlato Complete Database Reset Script
Write-Host "=== KaPlato Complete Database Reset ===" -ForegroundColor Red

# Navigate to Laravel directory
Set-Location "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend"

Write-Host "1. Clearing Laravel cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear

Write-Host "2. Dropping and recreating database..." -ForegroundColor Red

# Drop and create database
$sql = "DROP DATABASE IF EXISTS kaplato_db; CREATE DATABASE kaplato_db;"
echo $sql | mysql -u root -proot

Write-Host "3. Running fresh migrations..." -ForegroundColor Yellow
php artisan migrate:fresh --force

Write-Host "4. Creating test users..." -ForegroundColor Yellow

# Simple user creation
php artisan tinker --execute="
\App\Models\User::create([
    'name' => 'Test Admin',
    'email' => 'admin@kaplato.com',
    'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
    'role' => 'admin',
    'verified' => true,
    'email_verified_at' => now(),
]);

\App\Models\User::create([
    'name' => 'Test Owner',
    'email' => 'owner@kaplato.com',
    'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
    'role' => 'karenderia_owner',
    'verified' => true,
    'email_verified_at' => now(),
]);

\App\Models\User::create([
    'name' => 'Test Customer',
    'email' => 'customer@kaplato.com',
    'password' => \Illuminate\Support\Facades\Hash::make('customer123'),
    'role' => 'customer',
    'verified' => true,
    'email_verified_at' => now(),
]);

echo 'Users created successfully!';
"

Write-Host "Reset Complete!" -ForegroundColor Green
Write-Host "Test accounts:" -ForegroundColor Cyan
Write-Host "- admin@kaplato.com / admin123" -ForegroundColor White
Write-Host "- owner@kaplato.com / owner123" -ForegroundColor White
Write-Host "- customer@kaplato.com / customer123" -ForegroundColor White
