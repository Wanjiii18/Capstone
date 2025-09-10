# Test Nearby Karenderias API  
Write-Host "=== Testing Nearby Karenderias API ===" -ForegroundColor Green

# Test coordinates for Mandaue City, Cebu
$lat = 10.3234
$lng = 123.9312
$radius = 5000

Write-Host "Testing location: Mandaue City, Cebu" -ForegroundColor Cyan
Write-Host "Coordinates: $lat, $lng" -ForegroundColor White
Write-Host "Radius: ${radius}m" -ForegroundColor White

$url = "http://localhost:8000/api/karenderias/nearby?latitude=$lat" + "&longitude=$lng" + "&radius=$radius"

try {
    $response = Invoke-RestMethod -Uri $url -Method GET -TimeoutSec 10
    
    if ($response.success) {
        Write-Host "✅ Found $($response.data.Count) karenderias:" -ForegroundColor Green
        
        foreach ($karenderia in $response.data) {
            $distanceKm = [math]::Round($karenderia.distance / 1000, 2)
            Write-Host ""
            Write-Host "🏪 $($karenderia.name)" -ForegroundColor White
            Write-Host "   📍 $($karenderia.address)" -ForegroundColor Gray
            Write-Host "   📏 Distance: ${distanceKm}km" -ForegroundColor Gray
            Write-Host "   ⭐ Rating: $($karenderia.rating)/5" -ForegroundColor Gray
            Write-Host "   🚛 Delivery Fee: $($karenderia.deliveryFee)" -ForegroundColor Gray
            Write-Host "   ⏰ Delivery Time: $($karenderia.deliveryTime)" -ForegroundColor Gray
            Write-Host "   📞 Phone: $($karenderia.phone)" -ForegroundColor Gray
        }
        
        Write-Host ""
        Write-Host "✅ Nearby API is working!" -ForegroundColor Green
        Write-Host "Total found: $($response.data.Count)" -ForegroundColor Cyan
        
    } else {
        Write-Host "❌ API returned error: $($response.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error calling API: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== API Testing Complete ===" -ForegroundColor Green
