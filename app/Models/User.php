<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('assigned_by', 'assigned_at', 'expires_at');
    }

    /**
     * Get the user's course enrollments.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get courses the user is enrolled in.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Get the user's lesson completions.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Get the user's learning goals.
     */
    public function learningGoals(): HasMany
    {
        return $this->hasMany(LearningGoal::class);
    }

    /**
     * Get the user's learning journal entries.
     */
    public function learningJournals(): HasMany
    {
        return $this->hasMany(LearningJournal::class);
    }

    /**
     * Get courses created by the user (for teachers).
     */
    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    /**
     * Get articles created by the user (for teachers).
     */
    public function createdArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by');
    }

    /**
     * Get the user's article bookmarks.
     */
    public function articleBookmarks(): HasMany
    {
        return $this->hasMany(ArticleBookmark::class);
    }

    /**
     * Get bookmarked articles.
     */
    public function bookmarkedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_bookmarks')
            ->withPivot('notes', 'reading_position', 'last_read_at')
            ->withTimestamps();
    }

    /**
     * Get the user's learning journal entries.
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(LearningJournal::class);
    }

    /**
     * Get the user's quiz attempts.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('slug', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles()->pluck('slug')->toArray();
        return empty(array_diff($roles, $userRoles));
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }
}
