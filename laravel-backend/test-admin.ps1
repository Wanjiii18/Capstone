# KaPlato Admin System Test Script
Write-Host "🧪 Testing KaPlato Admin System..." -ForegroundColor Green

# Test 1: Health Check
Write-Host "`n1️⃣ Testing Health Check..." -ForegroundColor Yellow
try {
    $healthResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET
    Write-Host "✅ Health Check: $($healthResponse.status)" -ForegroundColor Green
} catch {
    Write-Host "❌ Health Check Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test 2: Admin Login
Write-Host "`n2️⃣ Testing Admin Login..." -ForegroundColor Yellow
$loginData = @{
    email = "admin@kaplato.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $adminToken = $loginResponse.token
    Write-Host "✅ Admin Login Successful" -ForegroundColor Green
    Write-Host "   Token: $($adminToken.Substring(0,20))..." -ForegroundColor Cyan
} catch {
    Write-Host "❌ Admin Login Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test 3: Admin Dashboard
Write-Host "`n3️⃣ Testing Admin Dashboard..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $adminToken"
        "Content-Type" = "application/json"
    }
    $dashboardResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/dashboard" -Method GET -Headers $headers
    Write-Host "✅ Admin Dashboard Access Successful" -ForegroundColor Green
    Write-Host "   Total Karenderias: $($dashboardResponse.total_karenderias)" -ForegroundColor Cyan
    Write-Host "   Total Revenue: ₱$($dashboardResponse.total_revenue)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Admin Dashboard Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Karenderias List
Write-Host "`n4️⃣ Testing Karenderias Management..." -ForegroundColor Yellow
try {
    $karenderiaResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias" -Method GET -Headers $headers
    Write-Host "✅ Karenderias List Access Successful" -ForegroundColor Green
    Write-Host "   Found $($karenderiaResponse.data.Count) karenderias" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Karenderias List Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Access Control (Customer trying to access admin)
Write-Host "`n5️⃣ Testing Access Control..." -ForegroundColor Yellow
$customerLoginData = @{
    email = "customer@kaplato.com"
    password = "customer123"
} | ConvertTo-Json

try {
    $customerLoginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $customerLoginData -ContentType "application/json"
    $customerToken = $customerLoginResponse.token
    
    $customerHeaders = @{
        "Authorization" = "Bearer $customerToken"
        "Content-Type" = "application/json"
    }
    
    try {
        $forbiddenResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/dashboard" -Method GET -Headers $customerHeaders
        Write-Host "❌ Access Control Failed: Customer was able to access admin panel!" -ForegroundColor Red
    } catch {
        Write-Host "✅ Access Control Working: Customer properly denied admin access" -ForegroundColor Green
    }
} catch {
    Write-Host "⚠️ Customer login failed, skipping access control test" -ForegroundColor Yellow
}

Write-Host "`n🎉 Admin System Testing Complete!" -ForegroundColor Green
Write-Host "`nAdmin Credentials:" -ForegroundColor Cyan
Write-Host "Email: admin@kaplato.com" -ForegroundColor White
Write-Host "Password: admin123" -ForegroundColor White
Write-Host "`nOther Test Accounts:" -ForegroundColor Cyan
Write-Host "Owner: owner@kaplato.com / owner123" -ForegroundColor White
Write-Host "Customer: customer@kaplato.com / customer123" -ForegroundColor White
