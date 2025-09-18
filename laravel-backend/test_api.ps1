$headers = @{
    "Authorization" = "Bearer 11|FMQMHUFdM1LCvbWAL"
    "Accept" = "application/json"
}

try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/karenderias/my-karenderia" -Method GET -Headers $headers
    Write-Host "=== API Response ==="
    $response | ConvertTo-Json -Depth 5
} catch {
    Write-Host "Error: $_"
    Write-Host "Status Code: $($_.Exception.Response.StatusCode)"
}