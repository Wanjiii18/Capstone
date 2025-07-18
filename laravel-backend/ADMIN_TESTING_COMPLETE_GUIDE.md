# KaPlato Admin System Testing Guide

## Quick Start

1. **Start Laravel Server**:
   ```cmd
   cd "C:\Users\ACER NITRO AN515-52\Documents\Mobile\Capstone\laravel-backend"
   php artisan serve
   ```
   
   Or double-click `start-server.bat`

2. **Run Admin Tests**:
   ```powershell
   .\test-admin.ps1
   ```

## Server URLs

- **Local Development**: http://localhost:8000
- **Network Access**: http://172.29.2.44:8000 (when using start-local-network.bat)

## Admin System Features

### 🔐 Admin Authentication
- **Admin User**: admin@kaplato.com / admin123
- **Regular User**: user@example.com / password123

### 📊 Admin Dashboard Endpoints

1. **Health Check**
   ```
   GET /api/health
   ```

2. **Admin Login**
   ```
   POST /api/auth/login
   Body: {"email": "admin@kaplato.com", "password": "admin123"}
   ```

3. **Admin Dashboard** (requires admin token)
   ```
   GET /api/admin/dashboard
   Headers: Authorization: Bearer {token}
   ```

4. **Karenderias Management** (requires admin token)
   ```
   GET /api/admin/karenderias
   Headers: Authorization: Bearer {token}
   ```

5. **Karenderia Details** (requires admin token)
   ```
   GET /api/admin/karenderias/{id}
   Headers: Authorization: Bearer {token}
   ```

6. **Sales Analytics** (requires admin token)
   ```
   GET /api/admin/analytics/sales?period=daily&karenderia_id=1
   Headers: Authorization: Bearer {token}
   ```

7. **Inventory Alerts** (requires admin token)
   ```
   GET /api/admin/inventory/alerts
   Headers: Authorization: Bearer {token}
   ```

## What the Admin Can See

### 🏪 Karenderia Overview
- List all registered karenderias
- Basic information (name, address, contact)
- Owner details
- Registration status

### 💰 Financial Data
- **Revenue**: Total sales per karenderia
- **Profit**: Calculated profit margins
- **Sales Volume**: Number of orders/items sold
- **Growth Trends**: Daily/weekly/monthly analytics

### 📦 Inventory Management
- **Stock Levels**: Current inventory for each menu item
- **Low Stock Alerts**: Items below minimum threshold
- **Inventory Value**: Total value of stock
- **Stock Movement**: Inventory changes over time

### 📈 Analytics Dashboard
- **Sales Performance**: Top-selling items and karenderias
- **Revenue Trends**: Financial performance over time
- **Inventory Turnover**: How quickly items sell
- **Comparative Analysis**: Performance across different karenderias

## Role-Based Access Control

- ✅ **Admin Users**: Full access to all data and analytics
- ❌ **Regular Users**: Cannot access admin endpoints (returns 403 Forbidden)
- 🔒 **Protected Routes**: All admin endpoints require valid admin token

## Testing Scenarios

The `test-admin.ps1` script validates:

1. **Health Check**: Server is running
2. **Admin Login**: Authentication works
3. **Dashboard Access**: Admin can view dashboard
4. **Karenderias List**: Admin can see all karenderias
5. **Access Control**: Regular users are blocked from admin routes

## Expected Test Results

```
🧪 Testing KaPlato Admin System...

1️⃣ Testing Health Check...
✅ Health Check: OK

2️⃣ Testing Admin Login...
✅ Admin Login Successful
   Token: eyJ0eXAiOiJKV1QiLCJhbGc...

3️⃣ Testing Admin Dashboard...
✅ Dashboard Data Retrieved
   Karenderias: 3
   Total Revenue: ₱15,450.00

4️⃣ Testing Karenderias List...
✅ Karenderias List Retrieved
   Found 3 karenderias

5️⃣ Testing Access Control...
✅ Access Control Working
   Regular users blocked from admin routes

🎉 All tests passed! Admin system is working correctly.
```

## Troubleshooting

### Server Not Starting
- Check if PHP is installed: `php --version`
- Verify database connection in `.env`
- Run: `php artisan config:clear`

### Database Issues
- Run migrations: `php artisan migrate:fresh --seed`
- Check MySQL connection

### Authentication Errors
- Clear cache: `php artisan cache:clear`
- Regenerate app key: `php artisan key:generate`

### API Errors
- Check Laravel logs: `storage/logs/laravel.log`
- Verify Sanctum configuration
- Test with Postman/curl

## Next Steps

Once testing passes:
1. Integrate with mobile app
2. Set up production environment
3. Configure proper SSL/HTTPS
4. Implement additional admin features as needed
