# KaPlato Admin System Test Guide

## 🧪 **Testing the Admin System**

### **Step 1: Test Basic Health Check**
```bash
curl -X GET "http://localhost:8000/api/health"
```
Expected: `{"status":"Laravel backend is running!","timestamp":"..."}`

### **Step 2: Login as Admin**
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@kaplato.com",
    "password": "admin123"
  }'
```
**Save the token from the response!**

### **Step 3: Test Admin Dashboard (Replace YOUR_TOKEN)**
```bash
curl -X GET "http://localhost:8000/api/admin/dashboard" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### **Step 4: Test Karenderias Management**
```bash
curl -X GET "http://localhost:8000/api/admin/karenderias" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### **Step 5: Test Sales Analytics**
```bash
curl -X GET "http://localhost:8000/api/admin/analytics/sales" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### **Step 6: Test Inventory Alerts**
```bash
curl -X GET "http://localhost:8000/api/admin/inventory/alerts" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### **Step 7: Test Users Management**
```bash
curl -X GET "http://localhost:8000/api/admin/users" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

## 🔒 **Test Access Control**
Try accessing admin endpoint without admin role:

### **Login as Regular Customer**
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@kaplato.com",
    "password": "customer123"
  }'
```

### **Try to Access Admin Dashboard (Should Fail)**
```bash
curl -X GET "http://localhost:8000/api/admin/dashboard" \
  -H "Authorization: Bearer CUSTOMER_TOKEN" \
  -H "Content-Type: application/json"
```
Expected: `403 Forbidden - Access denied. Admin privileges required.`

## 📊 **Expected Admin Dashboard Data**
The admin should see:
- Total karenderias count
- Total revenue across all karenderias
- Number of low stock items
- Recent orders summary
- Active karenderias list

## 🏪 **Expected Karenderia Data**
The admin should see:
- Karenderia details (name, owner, status)
- Revenue and profit calculations
- Menu items with cost analysis
- Inventory status
- Recent orders
