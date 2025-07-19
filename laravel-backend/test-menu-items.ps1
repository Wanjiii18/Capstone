# Test Menu Items API

Write-Host "Testing Menu Items API..." -ForegroundColor Green

# 1. Create a test user and karenderia first
Write-Host "`n1. Creating test user..." -ForegroundColor Yellow
$userData = @{
    name = "Test Karenderia Owner"
    email = "testowner@example.com"
    password = "password123"
    role = "karenderia_owner"
} | ConvertTo-Json

try {
    $userResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/register" -Method POST -Body $userData -ContentType "application/json"
    Write-Host "✅ User created successfully" -ForegroundColor Green
    $token = $userResponse.token
} catch {
    Write-Host "User might already exist, trying to login..." -ForegroundColor Yellow
    $loginData = @{
        email = "testowner@example.com"
        password = "password123"
    } | ConvertTo-Json
    
    $loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.token
    Write-Host "✅ User logged in successfully" -ForegroundColor Green
}

# 2. Create a karenderia
Write-Host "`n2. Creating karenderia..." -ForegroundColor Yellow
$karenderiaData = @{
    name = "Test Karenderia"
    address = "123 Test Street"
    description = "A test karenderia"
} | ConvertTo-Json

$headers = @{ Authorization = "Bearer $token" }
try {
    $karenderiaResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/karenderias" -Method POST -Body $karenderiaData -ContentType "application/json" -Headers $headers
    Write-Host "✅ Karenderia created successfully" -ForegroundColor Green
    $karenderiaId = $karenderiaResponse.karenderia.id
} catch {
    Write-Host "Karenderia might already exist or there's an issue: $($_.Exception.Message)" -ForegroundColor Red
    # Try to get existing karenderia
    try {
        $karenderias = Invoke-RestMethod -Uri "http://localhost:8000/api/karenderias" -Method GET
        $karenderiaId = $karenderias[0].id
        Write-Host "✅ Using existing karenderia ID: $karenderiaId" -ForegroundColor Green
    } catch {
        Write-Host "❌ Could not get karenderia ID" -ForegroundColor Red
        exit 1
    }
}

# 3. Test menu item creation without karenderia_id (should auto-detect)
Write-Host "`n3. Testing menu item creation (auto-detect karenderia)..." -ForegroundColor Yellow
$menuItemData = @{
    name = "Test Dish"
    price = 150.00
    description = "A delicious test dish"
} | ConvertTo-Json

try {
    $menuResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/menu-items" -Method POST -Body $menuItemData -ContentType "application/json" -Headers $headers
    Write-Host "✅ Menu item created successfully (auto-detected karenderia)" -ForegroundColor Green
    Write-Host "   Menu Item: $($menuResponse.menuItem.name) - ₱$($menuResponse.menuItem.price)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Menu item creation failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Error details: $($_.ErrorDetails.Message)" -ForegroundColor Red
}

# 4. Test menu item creation with explicit karenderia_id
Write-Host "`n4. Testing menu item creation (explicit karenderia_id)..." -ForegroundColor Yellow
$menuItemData2 = @{
    name = "Test Dish 2"
    price = 200.00
    description = "Another delicious test dish"
    karenderia_id = $karenderiaId
} | ConvertTo-Json

try {
    $menuResponse2 = Invoke-RestMethod -Uri "http://localhost:8000/api/menu-items" -Method POST -Body $menuItemData2 -ContentType "application/json" -Headers $headers
    Write-Host "✅ Menu item created successfully (explicit karenderia_id)" -ForegroundColor Green
    Write-Host "   Menu Item: $($menuResponse2.menuItem.name) - ₱$($menuResponse2.menuItem.price)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Menu item creation failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Error details: $($_.ErrorDetails.Message)" -ForegroundColor Red
}

# 5. List all menu items
Write-Host "`n5. Listing all menu items..." -ForegroundColor Yellow
try {
    $allMenuItems = Invoke-RestMethod -Uri "http://localhost:8000/api/menu-items" -Method GET -Headers $headers
    Write-Host "✅ Menu items retrieved successfully" -ForegroundColor Green
    foreach ($item in $allMenuItems) {
        Write-Host "   - $($item.name): ₱$($item.price) (Karenderia ID: $($item.karenderia_id))" -ForegroundColor Cyan
    }
} catch {
    Write-Host "❌ Failed to retrieve menu items: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n🎉 Test completed!" -ForegroundColor Green
