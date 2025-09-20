#!/usr/bin/env pwsh

Write-Host "Testing Settings Page Data Load" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

# Test with alica's account
$loginData = @{
    email = "alica@kaplato.com"
    password = "123123123"
} | ConvertTo-Json

try {
    Write-Host "1. Testing login..." -ForegroundColor Yellow
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "Login successful for alica" -ForegroundColor Green
    
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    Write-Host "`n2. Testing karenderia data endpoint..." -ForegroundColor Yellow
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "API call successful" -ForegroundColor Green
    Write-Host "`nData that Settings page should show:" -ForegroundColor Cyan
    Write-Host "Business Name: $($karenderiaResponse.data.business_name)" -ForegroundColor White
    Write-Host "Phone: $($karenderiaResponse.data.phone)" -ForegroundColor White
    Write-Host "Email: $($karenderiaResponse.data.email)" -ForegroundColor White
    Write-Host "Description: $($karenderiaResponse.data.description)" -ForegroundColor White
    Write-Host "Address: $($karenderiaResponse.data.address)" -ForegroundColor White
    
} catch {
    Write-Host "Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`nSettings page should now show real data instead of Loading or Error messages" -ForegroundColor Green