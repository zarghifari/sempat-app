<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['group' => 'User Management', 'name' => 'View Users', 'slug' => 'users.view', 'description' => 'View user list and details'],
            ['group' => 'User Management', 'name' => 'Create Users', 'slug' => 'users.create', 'description' => 'Create new users'],
            ['group' => 'User Management', 'name' => 'Edit Users', 'slug' => 'users.edit', 'description' => 'Edit user information'],
            ['group' => 'User Management', 'name' => 'Delete Users', 'slug' => 'users.delete', 'description' => 'Delete users'],
            ['group' => 'User Management', 'name' => 'Manage User Roles', 'slug' => 'users.manage-roles', 'description' => 'Assign and remove user roles'],

            // Role & Permission Management
            ['group' => 'Role Management', 'name' => 'View Roles', 'slug' => 'roles.view', 'description' => 'View roles and permissions'],
            ['group' => 'Role Management', 'name' => 'Create Roles', 'slug' => 'roles.create', 'description' => 'Create new roles'],
            ['group' => 'Role Management', 'name' => 'Edit Roles', 'slug' => 'roles.edit', 'description' => 'Edit role information'],
            ['group' => 'Role Management', 'name' => 'Delete Roles', 'slug' => 'roles.delete', 'description' => 'Delete roles'],
            ['group' => 'Role Management', 'name' => 'Manage Permissions', 'slug' => 'roles.manage-permissions', 'description' => 'Assign permissions to roles'],

            // Course Management (FSDL)
            ['group' => 'Course Management', 'name' => 'View All Courses', 'slug' => 'courses.view-all', 'description' => 'View all courses in system'],
            ['group' => 'Course Management', 'name' => 'View Own Courses', 'slug' => 'courses.view-own', 'description' => 'View own created courses'],
            ['group' => 'Course Management', 'name' => 'Create Courses', 'slug' => 'courses.create', 'description' => 'Create new courses'],
            ['group' => 'Course Management', 'name' => 'Edit Courses', 'slug' => 'courses.edit', 'description' => 'Edit course content'],
            ['group' => 'Course Management', 'name' => 'Delete Courses', 'slug' => 'courses.delete', 'description' => 'Delete courses'],
            ['group' => 'Course Management', 'name' => 'Publish Courses', 'slug' => 'courses.publish', 'description' => 'Publish/unpublish courses'],

            // Enrollment Management
            ['group' => 'Enrollment', 'name' => 'Enroll Students', 'slug' => 'enrollments.create', 'description' => 'Enroll students in courses'],
            ['group' => 'Enrollment', 'name' => 'Unenroll Students', 'slug' => 'enrollments.delete', 'description' => 'Remove student enrollments'],
            ['group' => 'Enrollment', 'name' => 'View Enrollments', 'slug' => 'enrollments.view', 'description' => 'View enrollment lists'],

            // Article Management (SPSDL)
            ['group' => 'Article Management', 'name' => 'View All Articles', 'slug' => 'articles.view-all', 'description' => 'View all articles in system'],
            ['group' => 'Article Management', 'name' => 'View Own Articles', 'slug' => 'articles.view-own', 'description' => 'View own created articles'],
            ['group' => 'Article Management', 'name' => 'Create Articles', 'slug' => 'articles.create', 'description' => 'Create new articles'],
            ['group' => 'Article Management', 'name' => 'Edit Articles', 'slug' => 'articles.edit', 'description' => 'Edit article content'],
            ['group' => 'Article Management', 'name' => 'Delete Articles', 'slug' => 'articles.delete', 'description' => 'Delete articles'],
            ['group' => 'Article Management', 'name' => 'Publish Articles', 'slug' => 'articles.publish', 'description' => 'Publish/unpublish articles'],

            // Document Import
            ['group' => 'Document Management', 'name' => 'Upload Documents', 'slug' => 'documents.upload', 'description' => 'Upload Word documents'],
            ['group' => 'Document Management', 'name' => 'Transform Documents', 'slug' => 'documents.transform', 'description' => 'Transform documents to HTML'],
            ['group' => 'Document Management', 'name' => 'Delete Documents', 'slug' => 'documents.delete', 'description' => 'Delete uploaded documents'],

            // Assessment & Quiz
            ['group' => 'Assessment', 'name' => 'Create Quizzes', 'slug' => 'quizzes.create', 'description' => 'Create quizzes and questions'],
            ['group' => 'Assessment', 'name' => 'Edit Quizzes', 'slug' => 'quizzes.edit', 'description' => 'Edit quiz content'],
            ['group' => 'Assessment', 'name' => 'Delete Quizzes', 'slug' => 'quizzes.delete', 'description' => 'Delete quizzes'],
            ['group' => 'Assessment', 'name' => 'Grade Quizzes', 'slug' => 'quizzes.grade', 'description' => 'Grade quiz submissions'],
            ['group' => 'Assessment', 'name' => 'View Results', 'slug' => 'quizzes.view-results', 'description' => 'View quiz results'],

            // Analytics & Reports
            ['group' => 'Analytics', 'name' => 'View System Analytics', 'slug' => 'analytics.system', 'description' => 'View system-wide analytics'],
            ['group' => 'Analytics', 'name' => 'View Course Analytics', 'slug' => 'analytics.courses', 'description' => 'View course analytics'],
            ['group' => 'Analytics', 'name' => 'View Student Progress', 'slug' => 'analytics.students', 'description' => 'View student progress reports'],
            ['group' => 'Analytics', 'name' => 'Export Reports', 'slug' => 'analytics.export', 'description' => 'Export analytics data'],

            // Communication
            ['group' => 'Communication', 'name' => 'Manage Forums', 'slug' => 'forums.manage', 'description' => 'Create and moderate forums'],
            ['group' => 'Communication', 'name' => 'Post in Forums', 'slug' => 'forums.post', 'description' => 'Post in discussion forums'],
            ['group' => 'Communication', 'name' => 'Send Messages', 'slug' => 'messages.send', 'description' => 'Send direct messages'],
            ['group' => 'Communication', 'name' => 'Moderate Comments', 'slug' => 'comments.moderate', 'description' => 'Moderate user comments'],

            // System Settings
            ['group' => 'System', 'name' => 'Manage Settings', 'slug' => 'system.settings', 'description' => 'Manage system settings'],
            ['group' => 'System', 'name' => 'View Logs', 'slug' => 'system.logs', 'description' => 'View system activity logs'],
            ['group' => 'System', 'name' => 'Manage Categories', 'slug' => 'system.categories', 'description' => 'Manage content categories'],
            ['group' => 'System', 'name' => 'Manage Tags', 'slug' => 'system.tags', 'description' => 'Manage content tags'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $this->command->info('✓ Created ' . count($permissions) . ' permissions');
    }
}
