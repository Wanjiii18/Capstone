#!/usr/bin/env pwsh

Write-Host "🔍 Debugging API Response Format" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

$loginData = @{
    email = "owner@kaplato.com"
    password = "owner123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "✅ Login successful" -ForegroundColor Green
    
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "`n📋 Full API Response:" -ForegroundColor Yellow
    $karenderiaResponse | ConvertTo-Json -Depth 3
    
    Write-Host "`n🔍 Key Fields:" -ForegroundColor Yellow
    Write-Host "Success: $($karenderiaResponse.success)" -ForegroundColor White
    Write-Host "Has Data: $($karenderiaResponse.data -ne $null)" -ForegroundColor White
    
    if ($karenderiaResponse.data) {
        Write-Host "ID: $($karenderiaResponse.data.id)" -ForegroundColor White
        Write-Host "Name: '$($karenderiaResponse.data.name)'" -ForegroundColor White
        Write-Host "Business Name: '$($karenderiaResponse.data.business_name)'" -ForegroundColor White
        Write-Host "Status: '$($karenderiaResponse.data.status)'" -ForegroundColor White
        Write-Host "Owner ID: $($karenderiaResponse.data.owner_id)" -ForegroundColor White
    }
    
} catch {
    Write-Host "❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response body: $responseBody" -ForegroundColor Red
    }
}