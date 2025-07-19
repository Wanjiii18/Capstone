v# Manual API Testing Commands

# 1. Health Check
Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET

# 2. Admin Login
$loginData = @{
    email = "admin@kaplato.com"
    password = "admin123"
} | ConvertTo-Json
$loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body $loginData -ContentType "application/json"
$adminToken = $loginResponse.token

# 3. Admin Dashboard (use token from login)
$headers = @{ Authorization = "Bearer $adminToken" }
Invoke-RestMethod -Uri "http://localhost:8000/api/admin/dashboard" -Method GET -Headers $headers

# 4. Karenderias List
Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias" -Method GET -Headers $headers

# 5. Karenderia Details (ID 1)
Invoke-RestMethod -Uri "http://localhost:8000/api/admin/karenderias/1" -Method GET -Headers $headers

# 6. Sales Analytics
Invoke-RestMethod -Uri "http://localhost:8000/api/admin/analytics/sales?period=daily" -Method GET -Headers $headers

# 7. Inventory Alerts
Invoke-RestMethod -Uri "http://localhost:8000/api/admin/inventory/alerts" -Method GET -Headers $headers
