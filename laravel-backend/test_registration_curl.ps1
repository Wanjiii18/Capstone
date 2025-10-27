# Test karenderia owner registration via HTTP POST

$timestamp = [DateTimeOffset]::UtcNow.ToUnixTimeSeconds()

$body = @{
    name = "Test Owner $timestamp"
    email = "testowner$timestamp@test.com"
    password = "password123"
    password_confirmation = "password123"
    business_name = "Test Karenderia $timestamp"
    description = "A test karenderia for debugging"
    address = "123 Test Street"
    city = "Test City"
    province = "Test Province"
    phone = "09123456789"
    business_email = "business$timestamp@test.com"
    opening_time = "08:00"
    closing_time = "18:00"
} | ConvertTo-Json

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testing Karenderia Owner Registration" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Sending registration request..." -ForegroundColor Yellow
Write-Host "URL: http://localhost:8000/api/auth/register-karenderia-owner`n" -ForegroundColor White

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/auth/register-karenderia-owner" `
        -Method POST `
        -Body $body `
        -ContentType "application/json" `
        -UseBasicParsing
    
    Write-Host "✅ Success! Status Code: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "`nResponse:" -ForegroundColor Cyan
    $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
    
} catch {
    Write-Host "❌ Error! Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    Write-Host "`nError Response:" -ForegroundColor Red
    $errorBody = $_.ErrorDetails.Message
    if ($errorBody) {
        $errorBody | ConvertFrom-Json | ConvertTo-Json -Depth 10
    } else {
        Write-Host $_.Exception.Message
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Now check the admin panel at:" -ForegroundColor Yellow
Write-Host "http://192.168.1.17:8000/admin/pending" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
