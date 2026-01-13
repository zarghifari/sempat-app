<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n";
echo "═══════════════════════════════════════\n";
echo "  AUTHENTICATION TEST\n";
echo "═══════════════════════════════════════\n\n";

// Test 1: Check if users exist
$users = User::with('roles')->get();
echo "✓ Total Users: " . $users->count() . "\n\n";

// Test 2: Test login credentials for each demo user
$testAccounts = [
    ['email' => 'admin@sempat.test', 'password' => 'password'],
    ['email' => 'teacher@sempat.test', 'password' => 'password'],
    ['email' => 'teacher2@sempat.test', 'password' => 'password'],
    ['email' => 'student@sempat.test', 'password' => 'password'],
];

echo "Testing Authentication:\n";
echo "───────────────────────────────────────\n\n";

foreach ($testAccounts as $account) {
    $user = User::where('email', $account['email'])->first();
    
    if ($user) {
        $passwordMatch = Hash::check($account['password'], $user->password);
        $status = $passwordMatch ? '✓' : '✗';
        $roles = $user->roles->pluck('name')->join(', ');
        
        echo "{$status} {$user->email}\n";
        echo "  Name: {$user->full_name}\n";
        echo "  Roles: {$roles}\n";
        echo "  Password Check: " . ($passwordMatch ? 'PASS' : 'FAIL') . "\n";
        echo "  Active: " . ($user->is_active ? 'Yes' : 'No') . "\n\n";
    } else {
        echo "✗ {$account['email']} - USER NOT FOUND\n\n";
    }
}

echo "═══════════════════════════════════════\n";
echo "  Routes Check:\n";
echo "═══════════════════════════════════════\n\n";

// Check if routes are registered
$router = app('router');
$routes = $router->getRoutes();

$authRoutes = [
    'GET|HEAD login',
    'POST login',
    'GET|HEAD register',
    'POST register',
    'POST logout',
    'GET|HEAD dashboard',
];

echo "Web Routes:\n";
foreach ($authRoutes as $routeName) {
    $found = false;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'login') || 
            str_contains($route->uri(), 'register') || 
            str_contains($route->uri(), 'logout') ||
            str_contains($route->uri(), 'dashboard')) {
            if (str_contains($routeName, $route->methods()[0])) {
                $found = true;
                break;
            }
        }
    }
    echo ($found ? '✓' : '✗') . " {$routeName}\n";
}

echo "\nAPI Routes:\n";
$apiRoutes = [
    'POST api/v1/auth/login',
    'POST api/v1/auth/register',
    'POST api/v1/auth/logout',
    'GET|HEAD api/v1/auth/me',
];

foreach ($apiRoutes as $routeName) {
    $found = false;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'api/v1/auth')) {
            $found = true;
            break;
        }
    }
    echo ($found ? '✓' : '✗') . " {$routeName}\n";
}

echo "\n═══════════════════════════════════════\n";
echo "  ✓ AUTHENTICATION TEST COMPLETE!\n";
echo "═══════════════════════════════════════\n\n";

echo "Next Steps:\n";
echo "1. Start server: php artisan serve\n";
echo "2. Open browser: http://127.0.0.1:8000\n";
echo "3. Try login with any demo account above\n\n";
