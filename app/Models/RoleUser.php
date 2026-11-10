<?php

namespace App\Models;

use App\Traits\FilterRelationships;
use App\Traits\Filtering;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleUser extends Model
{
    /** @use HasFactory<\Database\Factories\RoleUserFactory> */
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
     * Role user table.
     *
     * @var string
     */
    protected $table = 'role_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'user_id',
    ];

    /**
     * The user role belongs to a role.
     *
     * @return object | App\Models\Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The user role belongs to a user.
     *
     * @return object | App\Models\User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
