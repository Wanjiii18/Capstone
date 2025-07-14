@echo off
echo Starting Laravel server on local network...
echo.
echo Your local IP: 172.29.2.44
echo Server will be accessible at: http://172.29.2.44:8000
echo API endpoints will be available at: http://172.29.2.44:8000/api
echo.
echo Make sure your mobile device is connected to the same WiFi network (usjr.edu.ph)
echo.
echo Available API endpoints:
echo - GET http://172.29.2.44:8000/api/health
echo - GET http://172.29.2.44:8000/api/karenderias
echo - POST http://172.29.2.44:8000/api/auth/register
echo - POST http://172.29.2.44:8000/api/auth/login
echo.
php "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend\artisan" serve --host=172.29.2.44 --port=8000
pause
