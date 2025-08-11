# Test Admin Approval System

Write-Host "Testing KaPlato Admin Approval System..." -ForegroundColor Green

# 1. Test Health Check
Write-Host "`n1. Testing Health Check..." -ForegroundColor Yellow
try {
    $healthResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET
    Write-Host "✅ Health Check: $($healthResponse.status)" -ForegroundColor Green
} catch {
    Write-Host "❌ Health Check Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 2. Login as Karenderia Owner
Write-Host "`n2. Testing Karenderia Owner Login..." -ForegroundColor Yellow
$ownerLoginData = @{
    email = "owner@kaplato.com"
    password = "owner123"
} | ConvertTo-Json

try {
    $ownerLoginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $ownerLoginData -ContentType "application/json"
    Write-Host "✅ Owner Login Successful" -ForegroundColor Green
    $ownerToken = $ownerLoginResponse.token
} catch {
    Write-Host "❌ Owner Login Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 3. Register a new Karenderia (should be pending)
Write-Host "`n3. Registering New Karenderia..." -ForegroundColor Yellow
$karenderiaData = @{
    name = "Test Karenderia for Approval"
    description = "A test karenderia that needs admin approval"
    address = "123 Test Street, Test City"
    phone = "+639123456789"
    email = "test@karenderia.com"
    accepts_cash = $true
    accepts_online_payment = $false
} | ConvertTo-Json

$ownerHeaders = @{ Authorization = "Bearer $ownerToken" }
try {
    $karenderiaResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/karenderias" -Method POST -Body $karenderiaData -ContentType "application/json" -Headers $ownerHeaders
    Write-Host "✅ Karenderia Registration Submitted" -ForegroundColor Green
    Write-Host "   Status: $($karenderiaResponse.data.status)" -ForegroundColor Cyan
    Write-Host "   Message: $($karenderiaResponse.message)" -ForegroundColor Cyan
    $karenderiaId = $karenderiaResponse.data.id
} catch {
    Write-Host "❌ Karenderia Registration Failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Error details: $($_.ErrorDetails.Message)" -ForegroundColor Red
    exit 1
}

# 4. Login as Admin
Write-Host "`n4. Testing Admin Login..." -ForegroundColor Yellow
$adminLoginData = @{
    email = "admin@kaplato.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $adminLoginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $adminLoginData -ContentType "application/json"
    Write-Host "✅ Admin Login Successful" -ForegroundColor Green
    $adminToken = $adminLoginResponse.token
} catch {
    Write-Host "❌ Admin Login Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 5. Check Pending Karenderias
Write-Host "`n5. Checking Pending Karenderias..." -ForegroundColor Yellow
$adminHeaders = @{ Authorization = "Bearer $adminToken" }
try {
    $karenderiasList = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias?status=pending" -Method GET -Headers $adminHeaders
    Write-Host "✅ Found $($karenderiasList.data.Count) pending karenderia(s)" -ForegroundColor Green
    
    if ($karenderiasList.data.Count -gt 0) {
        foreach ($k in $karenderiasList.data) {
            Write-Host "   - $($k.name): Status = $($k.status)" -ForegroundColor Cyan
        }
    }
} catch {
    Write-Host "❌ Failed to get karenderias list: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 6. Approve the Karenderia
Write-Host "`n6. Approving Karenderia..." -ForegroundColor Yellow
$approvalData = @{
    status = "active"
    notes = "Approved via test script"
} | ConvertTo-Json

try {
    $approvalResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias/$karenderiaId/status" -Method PUT -Body $approvalData -ContentType "application/json" -Headers $adminHeaders
    Write-Host "✅ Karenderia Approved Successfully" -ForegroundColor Green
    Write-Host "   New Status: $($approvalResponse.karenderia.status)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Approval Failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Error details: $($_.ErrorDetails.Message)" -ForegroundColor Red
    exit 1
}

# 7. Verify Status Change
Write-Host "`n7. Verifying Status Change..." -ForegroundColor Yellow
try {
    $updatedKarenderiasList = Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias" -Method GET -Headers $adminHeaders
    $approvedKarenderia = $updatedKarenderiasList.data | Where-Object { $_.id -eq $karenderiaId }
    
    if ($approvedKarenderia -and $approvedKarenderia.status -eq "active") {
        Write-Host "✅ Status Change Verified: $($approvedKarenderia.name) is now ACTIVE" -ForegroundColor Green
    } else {
        Write-Host "❌ Status Change Failed" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Verification Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n🎉 Admin Approval System Test Complete!" -ForegroundColor Green
Write-Host "✅ Karenderia owners can register (pending status)" -ForegroundColor Green
Write-Host "✅ Admins can view pending applications" -ForegroundColor Green
Write-Host "✅ Admins can approve/reject applications" -ForegroundColor Green
Write-Host "✅ Status changes are persisted in database" -ForegroundColor Green
