#!/usr/bin/env pwsh

Write-Host "🧪 Testing Settings Page Data Load" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

$BASE_URL = "http://localhost:8000/api"

# Test with alica's account (since we know the password)
$loginData = @{
    email = "alica@kaplato.com"
    password = "123123123"
} | ConvertTo-Json

try {
    Write-Host "1️⃣ Testing login..." -ForegroundColor Yellow
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $loginResponse.access_token
    
    Write-Host "✅ Login successful for alica" -ForegroundColor Green
    
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    
    Write-Host "`n2️⃣ Testing karenderia data endpoint..." -ForegroundColor Yellow
    $karenderiaResponse = Invoke-RestMethod -Uri "$BASE_URL/karenderias/my-karenderia" -Method GET -Headers $headers
    
    Write-Host "✅ API call successful" -ForegroundColor Green
    Write-Host "`n📋 Data that Settings page should show:" -ForegroundColor Cyan
    Write-Host "Business Name: '$($karenderiaResponse.data.business_name)'" -ForegroundColor White
    Write-Host "Display Name: '$($karenderiaResponse.data.name)'" -ForegroundColor White  
    Write-Host "Phone: '$($karenderiaResponse.data.phone)'" -ForegroundColor White
    Write-Host "Email: '$($karenderiaResponse.data.email)'" -ForegroundColor White
    Write-Host "Business Email: '$($karenderiaResponse.data.business_email)'" -ForegroundColor White
    Write-Host "Description: '$($karenderiaResponse.data.description)'" -ForegroundColor White
    Write-Host "Address: '$($karenderiaResponse.data.address)'" -ForegroundColor White
    Write-Host "Status: '$($karenderiaResponse.data.status)'" -ForegroundColor White
    
    Write-Host "`n🎯 Expected form values:" -ForegroundColor Cyan
    $businessName = if ($karenderiaResponse.data.business_name) { $karenderiaResponse.data.business_name } elseif ($karenderiaResponse.data.name) { $karenderiaResponse.data.name } else { "No name set" }
    $phone = if ($karenderiaResponse.data.phone) { $karenderiaResponse.data.phone } else { "No phone set" }
    $email = if ($karenderiaResponse.data.business_email) { $karenderiaResponse.data.business_email } elseif ($karenderiaResponse.data.email) { $karenderiaResponse.data.email } else { "No email set" }
    $description = if ($karenderiaResponse.data.description) { $karenderiaResponse.data.description } else { "No description set" }
    $address = if ($karenderiaResponse.data.address) { $karenderiaResponse.data.address } else { "No address set" }
    
    Write-Host "Form Business Name: '$businessName'" -ForegroundColor Green
    Write-Host "Form Phone: '$phone'" -ForegroundColor Green
    Write-Host "Form Email: '$email'" -ForegroundColor Green
    Write-Host "Form Description: '$description'" -ForegroundColor Green
    Write-Host "Form Address: '$address'" -ForegroundColor Green
    
} catch {
    Write-Host "❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n🎉 Settings page should now show real data instead of 'Loading...' or 'Error loading data'" -ForegroundColor Green