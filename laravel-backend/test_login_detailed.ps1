# Test login with pending account - better error handling

$loginBody = @{
    email = "testowner1761577076@test.com"
    password = "password123"
} | ConvertTo-Json

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testing Login BEFORE Admin Approval" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

try {
    $response = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" `
        -Method POST `
        -Body $loginBody `
        -ContentType "application/json"
    
    Write-Host "✅ Login Successful (This shouldn't happen!)" -ForegroundColor Red
    $response | ConvertTo-Json -Depth 10
    
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    Write-Host "❌ Login BLOCKED - Status Code: $statusCode" -ForegroundColor Yellow
    
    # Try to get the error message
    $result = $_.Exception.Response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($result)
    $responseBody = $reader.ReadToEnd()
    
    Write-Host "`nServer Response:" -ForegroundColor Cyan
    try {
        $responseBody | ConvertFrom-Json | ConvertTo-Json -Depth 10
    } catch {
        Write-Host $responseBody
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "✅ GOOD! Login is blocked for pending accounts" -ForegroundColor Green
Write-Host "User must wait for admin approval first" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
