Write-Host "=== Testing Nearby Karenderias API ===" -ForegroundColor Green

$lat = 10.3234
$lng = 123.9312
$radius = 5000

Write-Host "Testing coordinates: $lat, $lng with radius ${radius}m" -ForegroundColor Cyan

$url = "http://localhost:8000/api/karenderias/nearby?latitude=$lat&longitude=$lng&radius=$radius"
Write-Host "URL: $url" -ForegroundColor White

try {
    $response = Invoke-RestMethod -Uri $url -Method GET
    
    if ($response.success) {
        Write-Host "SUCCESS: Found karenderias!" -ForegroundColor Green
        Write-Host "Count: $($response.data.Count)" -ForegroundColor Cyan
        
        foreach ($k in $response.data) {
            Write-Host "- $($k.name) at $($k.address)" -ForegroundColor White
        }
    } else {
        Write-Host "ERROR: $($response.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "FAILED: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "=== Test Complete ===" -ForegroundColor Green
