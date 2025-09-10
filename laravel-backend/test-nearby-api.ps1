# Test Nearby Karenderias API
Write-Host "=== Testing Nearby Karenderias API ===" -ForegroundColor Green

# Start Laravel server if not running
$serverRunning = $false
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET -TimeoutSec 5
    Write-Host "✅ Laravel server is running" -ForegroundColor Green
    $serverRunning = $true
} catch {
    Write-Host "❌ Laravel server not running. Starting server..." -ForegroundColor Yellow
    Set-Location "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend"
    Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -WindowStyle Minimized
    Start-Sleep -Seconds 5
}

# Test coordinates around Mandaue City, Cebu
$testLocations = @(
    @{
        name = "Center of Mandaue City"
        lat = 10.3234
        lng = 123.9312
        radius = 5000
    },
    @{
        name = "Near Lola Maria's Kitchen" 
        lat = 10.3157
        lng = 123.9227
        radius = 1000
    },
    @{
        name = "Wider Cebu Area"
        lat = 10.3234
        lng = 123.9312
        radius = 10000
    }
)

foreach ($location in $testLocations) {
    Write-Host "`n📍 Testing: $($location.name)" -ForegroundColor Cyan
    Write-Host "   Coordinates: $($location.lat), $($location.lng)" -ForegroundColor White
    Write-Host "   Radius: $($location.radius)m" -ForegroundColor White
    
    $url = "http://localhost:8000/api/karenderias/nearby?latitude=$($location.lat)&longitude=$($location.lng)&radius=$($location.radius)"
    
    try {
        $response = Invoke-RestMethod -Uri $url -Method GET -TimeoutSec 10
        
        if ($response.success) {
            Write-Host "   ✅ Found $($response.data.Count) karenderias:" -ForegroundColor Green
            
            foreach ($karenderia in $response.data) {
                $distanceKm = [math]::Round($karenderia.distance / 1000, 2)
                Write-Host "      🏪 $($karenderia.name)" -ForegroundColor White
                Write-Host "         📍 $($karenderia.address)" -ForegroundColor Gray
                Write-Host "         📏 Distance: ${distanceKm}km" -ForegroundColor Gray
                Write-Host "         ⭐ Rating: $($karenderia.rating)/5" -ForegroundColor Gray
                Write-Host "         🚛 Delivery: ₱$($karenderia.deliveryFee)" -ForegroundColor Gray
                Write-Host ""
            }
        } else {
            Write-Host "   ❌ API returned success=false: $($response.message)" -ForegroundColor Red
        }
    } catch {
        Write-Host "   ❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n=== API Testing Complete ===" -ForegroundColor Green
Write-Host "The nearby API should now work with your Angular map!" -ForegroundColor Cyan
Write-Host "`nExample API call:" -ForegroundColor Yellow
Write-Host "GET http://localhost:8000/api/karenderias/nearby?latitude=10.3234&longitude=123.9312&radius=5000" -ForegroundColor White

Write-Host "`nTo use in Angular:" -ForegroundColor Yellow
Write-Host "this.karenderiaService.getNearbyKarenderias(10.3234, 123.9312, 5000)" -ForegroundColor White
