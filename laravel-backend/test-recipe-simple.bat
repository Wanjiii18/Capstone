@echo off
echo Testing Recipe Management System
echo.

echo 1. Testing health endpoint...
curl -s "http://localhost:8000/api/health"
echo.
echo.

echo 2. Creating admin user...
curl -s -X POST "http://localhost:8000/api/auth/register" ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"Test Admin\",\"email\":\"admin@test.com\",\"password\":\"admin123\",\"password_confirmation\":\"admin123\",\"phone\":\"+639123456789\",\"address\":\"Cebu City\",\"role\":\"admin\"}"
echo.
echo.

echo 3. Creating karenderia owner...
curl -s -X POST "http://localhost:8000/api/auth/register-karenderia-owner" ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"Maria Santos\",\"email\":\"maria@test.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\",\"phone\":\"+639987654321\",\"address\":\"Lahug, Cebu City\",\"karenderia_name\":\"Maria's Kitchen\",\"karenderia_address\":\"Lahug Circle, Cebu City\",\"latitude\":10.3157,\"longitude\":123.8854}"
echo.
echo.

echo 4. Logging in as karenderia owner...
curl -s -X POST "http://localhost:8000/api/auth/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"maria@test.com\",\"password\":\"password123\"}"
echo.
echo.

echo 5. Testing nearby karenderias (should work with distance calculations)...
curl -s "http://localhost:8000/api/karenderias/nearby?latitude=10.3157&longitude=123.8854&radius=10"
echo.
echo.

echo Testing completed!
pause
