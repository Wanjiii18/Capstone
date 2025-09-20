#!/usr/bin/env pwsh

Write-Host "🧪 Testing Karenderia Business Name API" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

# Test with owner@kaplato.com
Write-Host "`n1️⃣ Testing with owner@kaplato.com" -ForegroundColor Yellow

$loginData = @{
    email = "owner@kaplato.com"
    password = "owner123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "✅ Login successful" -ForegroundColor Green
    Write-Host "User: $($loginResponse.user.name)" -ForegroundColor White
    
    # Test my-karenderia endpoint
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "✅ Karenderia API successful" -ForegroundColor Green
    Write-Host "Business Name: $($karenderiaResponse.data.business_name)" -ForegroundColor White
    Write-Host "Display Name: $($karenderiaResponse.data.name)" -ForegroundColor White
    Write-Host "Status: $($karenderiaResponse.data.status)" -ForegroundColor White
    
} catch {
    Write-Host "❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test with alica@kaplato.com
Write-Host "`n2️⃣ Testing with alica@kaplato.com" -ForegroundColor Yellow

$alicaLoginData = @{
    email = "alica@kaplato.com"
    password = "123123123"
} | ConvertTo-Json

try {
    $alicaLoginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $alicaLoginData -ContentType "application/json"
    $alicaToken = $alicaLoginResponse.access_token
    
    Write-Host "✅ Login successful" -ForegroundColor Green
    Write-Host "User: $($alicaLoginResponse.user.name)" -ForegroundColor White
    
    # Test my-karenderia endpoint
    $alicaHeaders = @{
        "Authorization" = "Bearer $alicaToken"
        "Content-Type" = "application/json"
    }
    
    $alicaKarenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $alicaHeaders
    
    Write-Host "✅ Karenderia API successful" -ForegroundColor Green
    Write-Host "Business Name: $($alicaKarenderiaResponse.data.business_name)" -ForegroundColor White
    Write-Host "Display Name: $($alicaKarenderiaResponse.data.name)" -ForegroundColor White
    Write-Host "Status: $($alicaKarenderiaResponse.data.status)" -ForegroundColor White
    
} catch {
    Write-Host "❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n🎉 Test completed!" -ForegroundColor Green