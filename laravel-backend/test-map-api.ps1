Write-Host "=== Testing Nearby Karenderias API ===" -ForegroundColor Green

# Cebu coordinates (Mandaue City area)
$lat = 10.3234
$lng = 123.9312
$radius = 5000

Write-Host "🗺️ Testing Mandaue City, Cebu coordinates:" -ForegroundColor Cyan
Write-Host "   Latitude: $lat" -ForegroundColor White
Write-Host "   Longitude: $lng" -ForegroundColor White
Write-Host "   Radius: ${radius}m" -ForegroundColor White

$url = "http://127.0.0.1:8000/api/karenderias/nearby?latitude=$lat&longitude=$lng&radius=$radius"
Write-Host "🌐 API URL: $url" -ForegroundColor Yellow

try {
    $response = Invoke-RestMethod -Uri $url -Method GET -ContentType "application/json"
    
    if ($response.success) {
        Write-Host ""
        Write-Host "✅ SUCCESS! Found $($response.data.Count) karenderias nearby!" -ForegroundColor Green
        Write-Host ""
        
        foreach ($karenderia in $response.data) {
            $distanceKm = [math]::Round($karenderia.distance / 1000, 2)
            Write-Host "🏪 $($karenderia.name)" -ForegroundColor Yellow
            Write-Host "   📍 $($karenderia.address)" -ForegroundColor Gray
            Write-Host "   📏 Distance: ${distanceKm} km" -ForegroundColor Cyan
            Write-Host "   ⭐ Rating: $($karenderia.rating)/5.0" -ForegroundColor Green
            Write-Host "   💰 Delivery: ₱$($karenderia.deliveryFee)" -ForegroundColor Magenta
            Write-Host "   ⏰ Time: $($karenderia.deliveryTime)" -ForegroundColor Blue
            Write-Host "   📞 Phone: $($karenderia.phone)" -ForegroundColor Gray
            Write-Host ""
        }
        
        Write-Host "🎉 PERFECT! These karenderias will show up on your map!" -ForegroundColor Green
        Write-Host "📱 Your search nearby feature is ready!" -ForegroundColor Cyan
        
    } else {
        Write-Host "❌ API Error: $($response.message)" -ForegroundColor Red
    }
    
} catch {
    Write-Host "❌ Connection Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "💡 Make sure Laravel server is running on port 8000" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Test Complete ===" -ForegroundColor Green
