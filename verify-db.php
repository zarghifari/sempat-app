<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

echo "\n";
echo "═══════════════════════════════════════\n";
echo "  DATABASE VERIFICATION\n";
echo "═══════════════════════════════════════\n\n";

// Count totals
$roleCount = Role::count();
$permissionCount = Permission::count();
$userCount = User::count();

echo "✓ Total Roles: {$roleCount}\n";
echo "✓ Total Permissions: {$permissionCount}\n";
echo "✓ Total Users: {$userCount}\n\n";

echo "───────────────────────────────────────\n";
echo "  ROLES & PERMISSIONS\n";
echo "───────────────────────────────────────\n\n";

// Show each role with permission count
$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    $permCount = $role->permissions->count();
    echo "→ {$role->name} ({$role->slug})\n";
    echo "  {$permCount} permissions assigned\n";
    echo "  Description: {$role->description}\n\n";
}

echo "───────────────────────────────────────\n";
echo "  USERS & ROLES\n";
echo "───────────────────────────────────────\n\n";

// Show each user with their role
$users = User::with('roles')->get();
foreach ($users as $user) {
    $roleNames = $user->roles->pluck('name')->join(', ');
    echo "→ {$user->full_name} ({$user->email})\n";
    echo "  Role: {$roleNames}\n\n";
}

echo "═══════════════════════════════════════\n";
echo "  ✓ VERIFICATION COMPLETE!\n";
echo "═══════════════════════════════════════\n\n";
