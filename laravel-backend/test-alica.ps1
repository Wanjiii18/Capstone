#!/usr/bin/env pwsh

Write-Host "Testing alica account" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

$loginData = @{
    email = "alica@kaplato.com"
    password = "123123123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "Login successful for alica" -ForegroundColor Green
    
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "Business Name: $($karenderiaResponse.data.business_name)" -ForegroundColor Yellow
    Write-Host "Display Name: $($karenderiaResponse.data.name)" -ForegroundColor Yellow
    Write-Host "Status: $($karenderiaResponse.data.status)" -ForegroundColor Yellow
    
} catch {
    Write-Host "Test failed: $($_.Exception.Message)" -ForegroundColor Red
}