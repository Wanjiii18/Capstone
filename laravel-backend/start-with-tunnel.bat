@echo off
echo =================================================
echo     KaPlato Laravel Backend - Tunnel Setup
echo =================================================
echo.
echo This script will create a public tunnel to your Laravel backend
echo so your mobile app can access it from anywhere.
echo.
echo Steps to set up:
echo 1. Install ngrok from https://ngrok.com/download
echo 2. Create a free ngrok account and get your authtoken
echo 3. Run: ngrok config add-authtoken YOUR_AUTHTOKEN
echo 4. Run this script
echo.

echo Starting Laravel server on localhost:8000...
start /B php "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend\artisan" serve --host=127.0.0.1 --port=8000

echo.
echo Waiting for Laravel server to start...
timeout /t 5 /nobreak > nul

echo.
echo Starting ngrok tunnel...
echo Your backend will be accessible from anywhere via the ngrok URL
echo.
echo Copy the HTTPS URL from ngrok and update your environment.ts file
echo Example: https://abc123.ngrok.io/api
echo.

ngrok http 8000

pause
