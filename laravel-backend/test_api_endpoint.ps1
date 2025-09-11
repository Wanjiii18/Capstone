# Test nearby karenderias API
Write-Host "Testing nearby karenderias API..." -ForegroundColor Green
Write-Host ""

$url = "http://localhost:8000/api/karenderias/nearby?latitude=10.2442&longitude=123.8492&radius=10"

try {
    $response = Invoke-RestMethod -Uri $url -Method GET -Headers @{
        "Accept" = "application/json"
        "Content-Type" = "application/json"
    }
    
    Write-Host "API Response:" -ForegroundColor Yellow
    $response | ConvertTo-Json -Depth 10 | Write-Host
    
    Write-Host ""
    Write-Host "Success: Found $($response.data.Count) karenderias" -ForegroundColor Green
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
}

Write-Host ""
Write-Host "Done!" -ForegroundColor Cyan
