@echo off
echo Testing nearby karenderias API...
echo.

REM Test the API endpoint
curl -X GET "http://localhost:8000/api/karenderias/nearby?latitude=10.2442&longitude=123.8492&radius=10" -H "Accept: application/json" -H "Content-Type: application/json"

echo.
echo.
echo Done!
pause
