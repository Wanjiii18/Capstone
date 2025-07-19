// Test script to verify menu API functionality
const axios = require('axios');

const BASE_URL = 'http://localhost:8000/api';

// Test credentials
const adminCredentials = {
    email: 'admin@karenderia.com',
    password: 'admin123'
};

const karenderiaOwnerCredentials = {
    email: 'owner@karenderia.com',
    password: 'password123'
};

let adminToken = '';
let ownerToken = '';

async function login(credentials) {
    try {
        const response = await axios.post(`${BASE_URL}/login`, credentials);
        console.log(`✅ Login successful for ${credentials.email}`);
        return response.data.token;
    } catch (error) {
        console.error(`❌ Login failed for ${credentials.email}:`, error.response?.data?.message || error.message);
        return null;
    }
}

async function testMenuItems(token, userType) {
    try {
        const response = await axios.get(`${BASE_URL}/menu-items`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        console.log(`\n📋 Menu items for ${userType}:`);
        console.log(`Found ${response.data.data.length} menu items`);
        
        if (response.data.data.length > 0) {
            const firstItem = response.data.data[0];
            console.log(`First item: ${firstItem.name} - Available: ${firstItem.is_available}`);
            
            // Test availability toggle
            if (userType === 'Owner') {
                await testAvailabilityToggle(token, firstItem.id, firstItem.is_available);
            }
        }
        
        return response.data.data;
    } catch (error) {
        console.error(`❌ Error fetching menu items for ${userType}:`, error.response?.data?.message || error.message);
        return [];
    }
}

async function testAvailabilityToggle(token, itemId, currentAvailability) {
    try {
        const newAvailability = !currentAvailability;
        const response = await axios.patch(
            `${BASE_URL}/menu-items/${itemId}/availability`,
            { is_available: newAvailability },
            { headers: { Authorization: `Bearer ${token}` } }
        );
        
        console.log(`✅ Availability toggle successful: ${currentAvailability} → ${newAvailability}`);
        return true;
    } catch (error) {
        console.error(`❌ Availability toggle failed:`, error.response?.data?.message || error.message);
        return false;
    }
}

async function testAdminAllMenuItems(token) {
    try {
        const response = await axios.get(`${BASE_URL}/admin/menu-items`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        console.log(`\n🔧 Admin view - All menu items: ${response.data.data.length} total`);
        return response.data.data;
    } catch (error) {
        console.error(`❌ Error fetching admin menu items:`, error.response?.data?.message || error.message);
        return [];
    }
}

async function runTests() {
    console.log('🚀 Starting Menu API Tests...\n');
    
    // Test admin login
    adminToken = await login(adminCredentials);
    if (!adminToken) {
        console.log('❌ Cannot proceed without admin token');
        return;
    }
    
    // Test karenderia owner login
    ownerToken = await login(karenderiaOwnerCredentials);
    if (!ownerToken) {
        console.log('❌ Cannot proceed without owner token');
        return;
    }
    
    // Test admin functionality
    await testAdminAllMenuItems(adminToken);
    
    // Test karenderia owner functionality
    await testMenuItems(ownerToken, 'Owner');
    
    console.log('\n✅ All tests completed!');
}

// Run the tests
runTests().catch(console.error);
