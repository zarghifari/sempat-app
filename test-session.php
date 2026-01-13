<?php

// Test Session & CSRF Configuration
// Run: php test-session.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Session & CSRF Test ===\n\n";

// Test 1: Check Session Driver
echo "1. Session Driver: ";
$driver = config('session.driver');
echo "$driver\n";

// Test 2: Check Session Table
echo "2. Session Table exists: ";
try {
    $tables = DB::select('SHOW TABLES');
    $hasSessionTable = false;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if ($tableName === 'sessions') {
            $hasSessionTable = true;
            break;
        }
    }
    echo $hasSessionTable ? "✓ Yes\n" : "✗ No\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check Session Config
echo "3. Session Configuration:\n";
echo "   - Lifetime: " . config('session.lifetime') . " minutes\n";
echo "   - Domain: " . (config('session.domain') ?: 'null') . "\n";
echo "   - Path: " . config('session.path') . "\n";
echo "   - Same Site: " . config('session.same_site') . "\n";
echo "   - Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";

// Test 4: Check Cache Config
echo "4. Cache Driver: " . config('cache.default') . "\n";

// Test 5: Check if sessions can be written
echo "5. Session Write Test: ";
try {
    DB::table('sessions')->insert([
        'id' => 'test_session_' . time(),
        'user_id' => null,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test',
        'payload' => 'test_data',
        'last_activity' => time(),
    ]);
    echo "✓ Can write to sessions table\n";
    
    // Clean up test data
    DB::table('sessions')->where('id', 'like', 'test_session_%')->delete();
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nIf all tests pass, try these steps:\n";
echo "1. Close all browser tabs\n";
echo "2. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "3. Open in incognito/private window\n";
echo "4. Try login again\n";
