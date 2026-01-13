# 🔄 Role Structure Update - LMS SEMPAT

**Date:** 12 Januari 2026  
**Version:** 1.1  
**Change Type:** Role Simplification & Content Ownership Model

---

## 📋 Change Summary

### Previous Structure (v1.0)
- **4 Roles:** Super Admin, Admin, Teacher, Student
- **50+ Permissions**
- Super Admin and Admin had overlapping permissions
- Teachers could potentially edit any content

### New Structure (v1.1)
- **3 Roles:** Admin, Teacher, Student
- **45 Permissions** (streamlined)
- Clear separation of responsibilities
- **Content Ownership Model** for Teachers

---

## 🎯 Key Changes

### 1. Role Consolidation

**Merged Roles:**
```
Super Admin + Admin → Admin (single role)
```

**Rationale:**
- Eliminate redundancy and confusion
- Simplify permission management
- Reduce administrative overhead
- Clearer role hierarchy

### 2. Content Ownership Model

**New Ownership Rules:**

**Teacher:**
- Can CREATE courses, articles, modules, lessons, quizzes
- Can EDIT/DELETE only **own content** (`created_by = user_id`)
- Can VIEW all published content (read-only)
- Cannot modify other teachers' content

**Admin:**
- Full access to ALL content (all users)
- Can create, edit, delete any content
- Can moderate and approve content
- System-wide permissions

**Student:**
- No content creation permissions
- Can view enrolled/published content
- Can interact (comments, likes, etc.)

---

## 📊 Role Comparison Matrix

| Capability | Admin | Teacher | Student |
|------------|-------|---------|---------|
| **System Management** |
| User Management | ✅ Full | ❌ | ❌ |
| Role Management | ✅ | ❌ | ❌ |
| System Settings | ✅ | ❌ | ❌ |
| View Logs | ✅ | ❌ | ❌ |
| **Content Management** |
| Create Content | ✅ Any | ✅ Own | ❌ |
| Edit Content | ✅ All | ✅ Own Only | ❌ |
| Delete Content | ✅ All | ✅ Own Only | ❌ |
| Publish Content | ✅ All | ✅ Own Only | ❌ |
| View Content | ✅ All | ✅ All Published | ✅ Enrolled |
| Content Moderation | ✅ | ❌ | ❌ |
| **Student Management** |
| View All Students | ✅ | ❌ | ❌ |
| View Own Students | ✅ | ✅ | ❌ |
| Enroll Students | ✅ | ✅ Own Courses | ❌ |
| Grade Assessments | ✅ All | ✅ Own Courses | ❌ |
| **Analytics** |
| System Analytics | ✅ | ❌ | ❌ |
| Course Analytics | ✅ All | ✅ Own | ✅ Own |
| Student Progress | ✅ All | ✅ Own Students | ✅ Own |

---

## 🔐 Permission Changes

### Removed Permissions
- `users.view-all` (consolidated into admin role)
- `roles.delete` (system roles cannot be deleted)
- `courses.view-all` (now implicit for admin)
- `articles.view-all` (now implicit for admin)
- 5 other redundant permissions

### New/Modified Permissions
- `courses.view-own` - View only own created courses (Teacher)
- `courses.edit-own` - Edit only own courses (Teacher)
- `courses.delete-own` - Delete only own courses (Teacher)
- `articles.view-own` - View only own articles (Teacher)
- `articles.edit-own` - Edit only own articles (Teacher)
- `articles.delete-own` - Delete only own articles (Teacher)

### Permission Groups
```
1. User Management (5 permissions)
   - users.view, create, edit, delete, manage-roles

2. Role Management (4 permissions)
   - roles.view, create, edit, manage-permissions

3. Course Management (6 permissions)
   - courses.view-own, create, edit-own, delete-own, publish

4. Enrollment (3 permissions)
   - enrollments.create, view, delete

5. Article Management (6 permissions)
   - articles.view-own, create, edit-own, delete-own, publish

6. Document Management (3 permissions)
   - documents.upload, transform, delete

7. Assessment (5 permissions)
   - quizzes.create, edit, delete, grade, view-results

8. Analytics (4 permissions)
   - analytics.system, courses, students, export

9. Communication (4 permissions)
   - forums.manage, post, messages.send, comments.moderate

10. System (4 permissions)
    - system.settings, logs, categories, tags

Total: 45 permissions (reduced from 50+)
```

---

## 💾 Database Impact

### Schema Changes
**No database migration required!**

The existing schema already supports ownership:
- `courses.created_by` field (existing)
- `articles.created_by` field (existing)
- `modules.created_by` field (existing)
- All content tables have `created_by` foreign key

### Policy Implementation
Will implement Laravel Policies to enforce ownership:

```php
// CoursePolicy.php
public function update(User $user, Course $course): bool
{
    return $user->hasRole('admin') || 
           $course->created_by === $user->id;
}

public function delete(User $user, Course $course): bool
{
    return $user->hasRole('admin') || 
           $course->created_by === $user->id;
}
```

---

## 🔄 Migration Path

### Code Changes Required

1. **Seeders Update:**
   - RoleSeeder: Remove 'super-admin', keep only 'admin', 'teacher', 'student'
   - PermissionSeeder: Update to 45 permissions
   - UserSeeder: Remove superadmin user, update demo accounts

2. **Policies Creation:**
   - CoursePolicy (check ownership)
   - ArticlePolicy (check ownership)
   - ModulePolicy (check ownership)
   - LessonPolicy (check ownership)
   - QuizPolicy (check ownership)

3. **Middleware Update:**
   - CheckPermission middleware (add ownership check)
   - EnsureOwnership middleware (new)

4. **Controllers Update:**
   - Add policy checks in controllers
   - Filter queries by ownership for teachers

---

## 📝 Documentation Updates

### Files Updated
✅ `docs/04-Features-and-Modules.md`
   - Updated User Roles & Permissions section
   - Added Permission Matrix
   - Clarified ownership rules

✅ `docs/README.md`
   - Updated role count (4 → 3)
   - Updated permission count (50+ → 45)
   - Updated key features

✅ `docs/07-Development-Roadmap.md`
   - Updated Phase 1 deliverables
   - Updated RBAC implementation notes

---

## 🎓 Benefits of New Structure

### 1. **Clearer Responsibilities**
- Each role has distinct, non-overlapping responsibilities
- Easier to understand and explain to stakeholders
- Reduced confusion for users

### 2. **Better Security**
- Content ownership prevents unauthorized modifications
- Teachers cannot interfere with each other's content
- Principle of least privilege applied

### 3. **Simplified Management**
- Fewer roles to manage
- Fewer permissions to assign
- Easier onboarding for new admins

### 4. **Scalability**
- Model scales well for multiple schools
- Easy to add tenant isolation later
- Clear content boundaries

### 5. **Better UX**
- Teachers see only relevant content (own)
- Reduced clutter in interfaces
- Faster queries (filtered by ownership)

---

## 🚀 Next Steps

### Implementation Order

1. ✅ Update documentation (DONE)
2. ⏳ Update seeders (role, permission, user)
3. ⏳ Create policies for content ownership
4. ⏳ Create middleware for ownership checks
5. ⏳ Update controllers with policy authorization
6. ⏳ Add ownership filters to queries
7. ⏳ Update UI to show ownership indicators
8. ⏳ Write tests for ownership logic
9. ⏳ Update API documentation

---

## ✅ Testing Checklist

- [ ] Admin can create/edit/delete any content
- [ ] Teacher can create own content
- [ ] Teacher can edit/delete only own content
- [ ] Teacher cannot edit/delete other teachers' content
- [ ] Teacher can view all published content
- [ ] Student can view enrolled content only
- [ ] Permission checks work correctly
- [ ] Ownership filters work in listings
- [ ] Proper error messages for unauthorized actions

---

## 📞 Questions & Considerations

**Q: What happens to existing Super Admin users?**  
A: Migrate them to Admin role during seeder update.

**Q: Can a teacher transfer content ownership?**  
A: Only Admin can transfer ownership (future feature).

**Q: Can multiple teachers collaborate on one course?**  
A: Not in v1.0. Collaboration feature planned for v2.0.

**Q: What about shared resources?**  
A: Admins can mark content as "shared" (future feature).

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Author:** Development Team
