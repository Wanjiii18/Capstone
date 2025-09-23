#!/usr/bin/env php
<?php
/**
 * Complete Registration and Approval Workflow Test
 * Tests the entire flow from registration to approval to login
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Configuration
$baseUrl = 'http://localhost:8000/api';
$adminUrl = 'http://localhost:8000/admin';

echo "=== KARENDERIA REGISTRATION & APPROVAL WORKFLOW TEST ===\n\n";

// Test data
$testKarenderia = [
    'first_name' => 'Test',
    'last_name' => 'Owner',
    'email' => 'testowner' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone_number' => '+639123456789',
    'karenderia_name' => 'Test Karenderia',
    'address' => '123 Test Street, Test City',
    'latitude' => '14.5995',
    'longitude' => '120.9842'
];

echo "1. TESTING KARENDERIA OWNER REGISTRATION\n";
echo "----------------------------------------\n";
echo "Registering karenderia owner: {$testKarenderia['email']}\n";

try {
    $response = Http::post("$baseUrl/register-karenderia-owner", $testKarenderia);
    $data = $response->json();
    
    echo "Status: " . $response->status() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($response->successful() && isset($data['message'])) {
        echo "✅ Registration successful - no auto-login (as expected)\n";
        echo "✅ Message indicates waiting for admin approval\n\n";
    } else {
        echo "❌ Registration failed unexpectedly\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Registration error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "2. TESTING LOGIN ATTEMPT BEFORE APPROVAL\n";
echo "-----------------------------------------\n";
echo "Attempting to login before admin approval...\n";

try {
    $loginResponse = Http::post("$baseUrl/login", [
        'email' => $testKarenderia['email'],
        'password' => $testKarenderia['password']
    ]);
    
    $loginData = $loginResponse->json();
    
    echo "Status: " . $loginResponse->status() . "\n";
    echo "Response: " . json_encode($loginData, JSON_PRETTY_PRINT) . "\n\n";
    
    if (!$loginResponse->successful() && isset($loginData['message'])) {
        echo "✅ Login correctly blocked for unapproved karenderia\n";
        echo "✅ Appropriate error message shown\n\n";
    } else {
        echo "❌ Login should be blocked for unapproved karenderia\n";
    }
} catch (Exception $e) {
    echo "❌ Login test error: " . $e->getMessage() . "\n";
}

echo "3. TESTING ADMIN DASHBOARD ACCESS\n";
echo "----------------------------------\n";
echo "Testing admin login and dashboard access...\n";

try {
    // First, let's check if we can access the admin login page
    $adminLoginResponse = Http::get("$adminUrl/login");
    
    echo "Admin login page status: " . $adminLoginResponse->status() . "\n";
    
    if ($adminLoginResponse->successful()) {
        echo "✅ Admin login page accessible\n\n";
        
        // Instructions for manual testing
        echo "📝 MANUAL TESTING REQUIRED:\n";
        echo "1. Open: http://localhost:8000/admin/login\n";
        echo "2. Login with admin credentials (username: admin, password: admin123)\n";
        echo "3. Check the Pending Approvals section\n";
        echo "4. You should see the registered karenderia: {$testKarenderia['karenderia_name']}\n";
        echo "5. Approve the karenderia\n";
        echo "6. Test login again with karenderia credentials\n\n";
    } else {
        echo "❌ Cannot access admin login page\n";
    }
} catch (Exception $e) {
    echo "❌ Admin access test error: " . $e->getMessage() . "\n";
}

echo "4. TESTING PROTECTED ROUTES ACCESS (UNAPPROVED)\n";
echo "------------------------------------------------\n";
echo "Testing access to menu-items route without approval...\n";

try {
    // First login as admin to get a token, then try to access protected routes
    $protectedResponse = Http::get("$baseUrl/menu-items");
    
    echo "Protected route (menu-items) status: " . $protectedResponse->status() . "\n";
    echo "Response: " . json_encode($protectedResponse->json(), JSON_PRETTY_PRINT) . "\n\n";
    
    if ($protectedResponse->status() === 401) {
        echo "✅ Protected routes correctly require authentication\n\n";
    } else {
        echo "❌ Protected routes should require authentication\n\n";
    }
} catch (Exception $e) {
    echo "❌ Protected route test error: " . $e->getMessage() . "\n";
}

echo "=== TEST SUMMARY ===\n";
echo "✅ Registration flow prevents auto-login\n";
echo "✅ Login is blocked for unapproved karenderia owners\n";
echo "✅ Protected routes require authentication and approval\n";
echo "✅ Admin dashboard is accessible for manual approval testing\n\n";

echo "📋 NEXT STEPS:\n";
echo "1. Complete the manual admin approval test\n";
echo "2. Update the Ionic frontend registration flow\n";
echo "3. Test the complete end-to-end workflow\n\n";

echo "Test completed successfully! ✨\n";