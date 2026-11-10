<?php

namespace App\Models;

use App\Traits\FilterRelationships;
use App\Traits\Filtering;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Concerns\belongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use Filtering;
    use FilterRelationships;
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Permissions table.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
    ];

    /**
     * The permission is created by a user.
     *
     * @return object
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * The permissions belongs to many user roles.
     *
     * @return array object | App\Models\UserRole
     */
    public function userRoles(): belongsToMany
    {
        return $this->belongsToMany(UserRole::class, UserRolePermission::class, 'permission_id', 'user_role_id', 'id');
    }

    /**
     * The permissions has many user role permission.
     *
     * @return array object | App\Models\UserRolePermission
     */
    public function userRolePermission(): HasMany
    {
        return $this->hasMany(UserRolePermission::class);
    }
}
