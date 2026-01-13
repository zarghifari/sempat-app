<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'user' => [
                    'username' => 'admin',
                    'email' => 'admin@sempat.test',
                    'password' => Hash::make('password'),
                    'first_name' => 'Admin',
                    'last_name' => 'System',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
                'role' => 'admin',
                'profile' => [
                    'bio' => 'System Administrator with full access',
                ],
            ],
            [
                'user' => [
                    'username' => 'teacher1',
                    'email' => 'teacher@sempat.test',
                    'password' => Hash::make('password'),
                    'first_name' => 'Guru',
                    'last_name' => 'Matematika',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
                'role' => 'teacher',
                'profile' => [
                    'bio' => 'Mathematics Teacher',
                    'school_name' => 'SMA Negeri 1 Jakarta',
                ],
            ],
            [
                'user' => [
                    'username' => 'teacher2',
                    'email' => 'teacher2@sempat.test',
                    'password' => Hash::make('password'),
                    'first_name' => 'Guru',
                    'last_name' => 'Fisika',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
                'role' => 'teacher',
                'profile' => [
                    'bio' => 'Physics Teacher',
                    'school_name' => 'SMA Negeri 1 Jakarta',
                ],
            ],
            [
                'user' => [
                    'username' => 'student',
                    'email' => 'student@sempat.test',
                    'password' => Hash::make('password'),
                    'first_name' => 'Siswa',
                    'last_name' => 'Demo',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
                'role' => 'student',
                'profile' => [
                    'bio' => 'Student Demo Account',
                    'school_name' => 'SMA Negeri 1 Jakarta',
                    'grade_level' => '12',
                    'major' => 'IPA',
                ],
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['user']['email']],
                $userData['user']
            );

            // Create profile
            if (!$user->profile) {
                $user->profile()->create($userData['profile']);
            }

            // Assign role
            $role = Role::where('slug', $userData['role'])->first();
            if ($role && !$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id, [
                    'assigned_at' => now(),
                ]);
            }

            $this->command->info("✓ Created user: {$user->email} ({$userData['role']})");
        }
    }
}
