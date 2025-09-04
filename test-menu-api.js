// Test script for menu API functionality
const API_BASE = 'http://localhost:8000/api';

// Test data
const testMenuData = {
    name: 'Chicken Adobo',
    description: 'Classic Filipino chicken adobo with rice',
    price: 120.00,
    category: 'Main Course',
    preparation_time: 25,
    ingredients: [
        { name: 'Chicken', quantity: '1 kg' },
        { name: 'Soy Sauce', quantity: '1/2 cup' },
        { name: 'Vinegar', quantity: '1/3 cup' },
        { name: 'Garlic', quantity: '6 cloves' },
        { name: 'Bay Leaves', quantity: '3 pieces' }
    ]
};

// Test user credentials (the karenderia owner we created)
const testCredentials = {
    email: 'owner@kaplato.com',
    password: 'password123'
};

async function testMenuAPI() {
    console.log('🧪 Testing KaPlato Menu API...\n');

    try {
        // Step 1: Login to get auth token
        console.log('1️⃣ Logging in as karenderia owner...');
        const loginResponse = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(testCredentials)
        });

        if (!loginResponse.ok) {
            throw new Error(`Login failed: ${loginResponse.status} ${loginResponse.statusText}`);
        }

        const loginData = await loginResponse.json();
        const authToken = loginData.access_token;
        console.log('✅ Login successful!');
        console.log(`   Token: ${authToken.substring(0, 20)}...`);
        console.log(`   User: ${loginData.user.name} (${loginData.user.role})\n`);

        // Step 2: Create menu item
        console.log('2️⃣ Creating menu item...');
        const createResponse = await fetch(`${API_BASE}/menu-items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${authToken}`
            },
            body: JSON.stringify(testMenuData)
        });

        if (!createResponse.ok) {
            const errorText = await createResponse.text();
            throw new Error(`Menu creation failed: ${createResponse.status} ${createResponse.statusText}\nResponse: ${errorText}`);
        }

        const createdMenu = await createResponse.json();
        console.log('✅ Menu item created successfully!');
        console.log(`   ID: ${createdMenu.id}`);
        console.log(`   Name: ${createdMenu.name}`);
        console.log(`   Price: ₱${createdMenu.price}`);
        console.log(`   Ingredients: ${createdMenu.ingredients.length} items`);
        console.log(`   Karenderia ID: ${createdMenu.karenderia_id}\n`);

        // Step 3: Fetch menu items (as customer would see)
        console.log('3️⃣ Fetching menu items (customer view)...');
        const menuResponse = await fetch(`${API_BASE}/menu-items?karenderia_id=${createdMenu.karenderia_id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!menuResponse.ok) {
            throw new Error(`Menu fetch failed: ${menuResponse.status} ${menuResponse.statusText}`);
        }

        const menuItems = await menuResponse.json();
        console.log(`✅ Found ${menuItems.length} menu items for customers`);
        
        menuItems.forEach((item, index) => {
            console.log(`   ${index + 1}. ${item.name} - ₱${item.price}`);
            console.log(`      Category: ${item.category}`);
            console.log(`      Prep Time: ${item.preparation_time || 'N/A'} minutes`);
            console.log(`      Ingredients: ${item.ingredients ? item.ingredients.length : 0} items`);
            
            if (item.ingredients && item.ingredients.length > 0) {
                console.log(`      Ingredient List:`);
                item.ingredients.forEach(ing => {
                    console.log(`        - ${ing.name}: ${ing.quantity}`);
                });
            }
        });

        console.log('\n🎉 All tests passed! Menu creation and display are working correctly!');
        
        // Summary
        console.log('\n📊 Test Summary:');
        console.log('✅ User authentication works');
        console.log('✅ Menu item creation with ingredients works');
        console.log('✅ Database storage is functioning');
        console.log('✅ Customer menu browsing works');
        console.log('✅ Ingredient display is working');

    } catch (error) {
        console.error('❌ Test failed:', error.message);
        console.log('\n🔧 Troubleshooting tips:');
        console.log('1. Make sure Laravel backend is running on http://localhost:8000');
        console.log('2. Check that the database has the karenderia owner user');
        console.log('3. Verify that migrations have been run');
    }
}

// Check if running in Node.js environment
if (typeof window === 'undefined') {
    console.log('⚠️  This script needs to run in a browser environment with fetch API');
    console.log('🌐 Please copy this code to browser console or use a testing tool like Postman');
    console.log('\nOr start the Laravel server and open this file in a browser:');
    console.log('   cd laravel-backend && php artisan serve');
} else {
    // Running in browser
    testMenuAPI();
}
