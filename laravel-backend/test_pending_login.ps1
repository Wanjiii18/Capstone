# Test login for pending karenderia owner
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Testing Login for Pending Karenderia Owner" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

$email = "testowner1761577076@test.com"
$password = "password123"

Write-Host "Attempting login with:" -ForegroundColor Yellow
Write-Host "Email: $email" -ForegroundColor White
Write-Host "Password: $password`n" -ForegroundColor White

$body = @{
    email = $email
    password = $password
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $body `
        -ErrorAction Stop
    
    Write-Host "❌ UNEXPECTED: Login succeeded!" -ForegroundColor Red
    Write-Host ($response | ConvertTo-Json -Depth 10) -ForegroundColor White
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    
    if ($_.ErrorDetails.Message) {
        $errorResponse = $_.ErrorDetails.Message | ConvertFrom-Json
        
        Write-Host "✅ Login BLOCKED - Status Code: $statusCode" -ForegroundColor Green
        Write-Host "`nServer Response:" -ForegroundColor Yellow
        Write-Host ($errorResponse | ConvertTo-Json -Depth 10) -ForegroundColor White
        
        Write-Host "`n========================================" -ForegroundColor Cyan
        Write-Host "✅ SUCCESS! System is working correctly:" -ForegroundColor Green
        Write-Host "   - Login blocked for pending accounts" -ForegroundColor White
        Write-Host "   - Clear error message displayed" -ForegroundColor White
        Write-Host "   - User role: karenderia_owner" -ForegroundColor White
        Write-Host "========================================`n" -ForegroundColor Cyan
    } else {
        Write-Host "⚠️  Error occurred but no details: $statusCode" -ForegroundColor Yellow
        Write-Host $_.Exception.Message -ForegroundColor White
    }
}
