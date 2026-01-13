<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot('assigned_by', 'assigned_at', 'expires_at');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Grant permission to role.
     */
    public function givePermissionTo(Permission|int $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        
        if (!$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermissionTo(Permission|int $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        $this->permissions()->detach($permissionId);
    }
}
