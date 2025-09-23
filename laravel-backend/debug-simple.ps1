#!/usr/bin/env pwsh

Write-Host "Debug API Response Format" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

$loginData = @{
    email = "owner@kaplato.com"
    password = "owner123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "Login successful" -ForegroundColor Green
    
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "Full API Response:" -ForegroundColor Yellow
    $karenderiaResponse | ConvertTo-Json -Depth 3
    
} catch {
    Write-Host "Test failed: $($_.Exception.Message)" -ForegroundColor Red
}