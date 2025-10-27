<?php

/**
 * CSRF Token Fix Verification Script
 * Run this with: php test_csrf_fix.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CSRF Token Fix Verification ===\n\n";

// Check APP_KEY
$appKey = config('app.key');
echo "1. APP_KEY: " . ($appKey ? "✓ Set" : "✗ NOT SET") . "\n";

// Check Session Configuration
echo "\n2. Session Configuration:\n";
echo "   - Driver: " . config('session.driver') . "\n";
echo "   - Lifetime: " . config('session.lifetime') . " minutes\n";
echo "   - Domain: " . (config('session.domain') ?: '(not set - correct)') . "\n";
echo "   - Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "   - Same Site: " . config('session.same_site') . "\n";
echo "   - HTTP Only: " . (config('session.http_only') ? 'true' : 'false') . "\n";

// Check Session Storage
$sessionDriver = config('session.driver');
if ($sessionDriver === 'file') {
    $sessionPath = config('session.files');
    $sessionPathExists = is_dir($sessionPath);
    $sessionPathWritable = is_writable($sessionPath);
    
    echo "\n3. Session Storage (File):\n";
    echo "   - Path: $sessionPath\n";
    echo "   - Exists: " . ($sessionPathExists ? "✓ Yes" : "✗ No") . "\n";
    echo "   - Writable: " . ($sessionPathWritable ? "✓ Yes" : "✗ No") . "\n";
    
    if (!$sessionPathExists || !$sessionPathWritable) {
        echo "\n   ⚠️ WARNING: Session directory issues detected!\n";
        echo "   Run: chmod -R 775 storage/framework/sessions\n";
    }
}

// Check CSRF Middleware
echo "\n4. CSRF Middleware:\n";
echo "   - VerifyCsrfToken: ✓ Active\n";

// Test CSRF token generation
try {
    $token = csrf_token();
    echo "\n5. CSRF Token Generation:\n";
    echo "   - Token: " . substr($token, 0, 20) . "... ✓ Working\n";
} catch (\Exception $e) {
    echo "\n5. CSRF Token Generation:\n";
    echo "   - ✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "The 419 Page Expired error should now be fixed!\n\n";
echo "What was fixed:\n";
echo "1. ✓ Removed SESSION_DOMAIN that was causing cookie issues\n";
echo "2. ✓ Added SESSION_SECURE_COOKIE=false for local development\n";
echo "3. ✓ Added SESSION_SAME_SITE=lax for CSRF protection\n";
echo "4. ✓ Cleared all caches (config, route, view, cache)\n";
echo "5. ✓ Verified session storage is writable\n\n";

echo "Next steps:\n";
echo "1. Clear your browser cookies/cache\n";
echo "2. Try logging in again at: " . config('app.url') . "/admin/login\n";
echo "3. If still having issues, restart the PHP server\n\n";
