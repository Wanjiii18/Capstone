# Final Test Script for KaPlato
Write-Host "=== KaPlato Final Test ===" -ForegroundColor Green

Set-Location "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend"

Write-Host "1. Starting Laravel server..." -ForegroundColor Yellow
Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -WindowStyle Minimized

Write-Host "2. Waiting for server to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

Write-Host "3. Testing health endpoint..." -ForegroundColor Yellow
try {
    $health = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/health" -Method GET -TimeoutSec 10
    Write-Host "✅ Health check: $($health.status)" -ForegroundColor Green
} catch {
    Write-Host "❌ Health check failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Server might not be ready yet. Try manually:" -ForegroundColor Yellow
    Write-Host "php artisan serve --host=127.0.0.1 --port=8000" -ForegroundColor White
    return
}

Write-Host "4. Testing login with owner credentials..." -ForegroundColor Yellow
$loginData = @{
    email = "owner@kaplato.com"
    password = "owner123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/auth/login" -Method POST -Body $loginData -ContentType "application/json" -TimeoutSec 10
    Write-Host "✅ LOGIN SUCCESS!" -ForegroundColor Green
    Write-Host "   User: $($loginResponse.user.email)" -ForegroundColor White
    Write-Host "   Role: $($loginResponse.user.role)" -ForegroundColor White
    Write-Host "   Token: $($loginResponse.access_token.Substring(0,30))..." -ForegroundColor White
} catch {
    Write-Host "❌ LOGIN FAILED: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response: $responseBody" -ForegroundColor Yellow
    }
}

Write-Host "`n=== Test Complete ===" -ForegroundColor Green
Write-Host "If login worked, you can now use the Angular app!" -ForegroundColor Cyan
Write-Host "Laravel server should be running at: http://127.0.0.1:8000" -ForegroundColor White
