<?php

namespace App\Models;

use App\Traits\FilterRelationships;
use App\Traits\Filtering;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use Filtering;
    use FilterRelationships;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Roles table.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by_id',
        'updated_by_id',
        'name',
        'display_name',
        'description',
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
     * The role belongs to many users.
     *
     * @return array object | App\Models\UserRole
     */
    public function users(): belongsToMany
    {
        return $this->belongsToMany(UserRole::class);
    }

    /**
     * The role belongs to many permissions.
     *
     * @return array object | App\Models\Permission
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
