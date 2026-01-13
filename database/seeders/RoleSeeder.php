<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles - Simplified to 3 roles
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system access with all permissions',
                'is_system' => true,
            ],
            [
                'name' => 'Teacher',
                'slug' => 'teacher',
                'description' => 'Teacher/Instructor - can manage own content only',
                'is_system' => true,
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'Student with learning access',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Assign permissions based on role
            $this->assignPermissions($role);
        }

        $this->command->info('✓ Created ' . count($roles) . ' roles with permissions');
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissions(Role $role): void
    {
        $permissions = match ($role->slug) {
            'admin' => Permission::all(), // Admin has all permissions
            'teacher' => Permission::whereIn('slug', [
                // Own Content Management
                'courses.view-own',
                'courses.create',
                'courses.edit',
                'courses.delete',
                'courses.publish',
                
                // Enrollment (own courses)
                'enrollments.create',
                'enrollments.view',
                
                // Articles (own)
                'articles.view-own',
                'articles.create',
                'articles.edit',
                'articles.delete',
                'articles.publish',
                
                // Document Management (own)
                'documents.upload',
                'documents.transform',
                'documents.delete',
                
                // Assessment (own courses)
                'quizzes.create',
                'quizzes.edit',
                'quizzes.delete',
                'quizzes.grade',
                'quizzes.view-results',
                
                // Analytics (own data)
                'analytics.courses',
                'analytics.students',
                
                // Communication
                'forums.post',
                'messages.send',
            ])->get(),
            'student' => Permission::whereIn('slug', [
                'forums.post',
                'messages.send',
            ])->get(),
            default => collect([]),
        };

        if ($permissions->isNotEmpty()) {
            $role->permissions()->sync($permissions->pluck('id'));
        }
    }
}
