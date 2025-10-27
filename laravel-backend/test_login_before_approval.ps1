# Test login with pending (not approved) karenderia owner account

# Using the account we just created
$loginBody = @{
    email = "testowner1761577076@test.com"
    password = "password123"
} | ConvertTo-Json

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testing Login BEFORE Admin Approval" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Attempting to login with:" -ForegroundColor Yellow
Write-Host "Email: testowner1761577076@test.com" -ForegroundColor White
Write-Host "Status: PENDING (Not Approved Yet)`n" -ForegroundColor Red

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/auth/login" `
        -Method POST `
        -Body $loginBody `
        -ContentType "application/json" `
        -UseBasicParsing
    
    Write-Host "Response Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "`nResponse:" -ForegroundColor Cyan
    $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
    
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    Write-Host "❌ Login Failed! Status Code: $statusCode" -ForegroundColor Red
    Write-Host "`nError Response:" -ForegroundColor Yellow
    $errorBody = $_.ErrorDetails.Message
    if ($errorBody) {
        $errorBody | ConvertFrom-Json | ConvertTo-Json -Depth 10
    } else {
        Write-Host $_.Exception.Message
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Expected Result:" -ForegroundColor Yellow
Write-Host "Login should be BLOCKED because account is" -ForegroundColor White
Write-Host "waiting for admin approval" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
