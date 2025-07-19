# Quick Menu Item Test

Write-Host "🧪 Testing Menu Item Creation..." -ForegroundColor Green

# Test data
$menuData = @{
    name = "Adobo"
    price = 150
    description = "Delicious Filipino adobo"
} | ConvertTo-Json

# Headers (you'll need to replace with actual token from your app)
$headers = @{ 
    Authorization = "Bearer YOUR_TOKEN_HERE"
    "Content-Type" = "application/json"
}

Write-Host "Test Data:" -ForegroundColor Yellow
Write-Host $menuData -ForegroundColor Cyan

Write-Host "`nTo test with your app's token:" -ForegroundColor Yellow
Write-Host "1. Login to your app and get the token" -ForegroundColor White
Write-Host "2. Replace 'YOUR_TOKEN_HERE' with the actual token" -ForegroundColor White
Write-Host "3. Run: Invoke-RestMethod -Uri 'http://localhost:8000/api/menu-items' -Method POST -Body `$menuData -Headers `$headers" -ForegroundColor White

Write-Host "`nServer Status Check:" -ForegroundColor Yellow
try {
    $health = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET
    Write-Host "✅ Server is running: $($health.status)" -ForegroundColor Green
} catch {
    Write-Host "❌ Server not responding" -ForegroundColor Red
}
