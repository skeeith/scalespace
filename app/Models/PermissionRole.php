<?php

namespace App\Models;

use App\Traits\FilterRelationships;
use App\Traits\Filtering;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRole extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionRoleFactory> */
    use Filtering;
    use FilterRelationships;
    use HasFactory;

    /**
     * Disable timestamps;
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Permission role table.
     *
     * @var string
     */
    protected $table = 'permission_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    /**
     * The permission role belongs to a permission.
     *
     * @return object | App\Models\Permission
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * The permission role belongs to a role.
     *
     * @return object | App\Models\Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
