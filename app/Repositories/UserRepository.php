<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\HasEntrust;
use Illuminate\Support\Facades\DB;

class UserRepository extends Repository
{
    use HasEntrust;

    /**
     * User model.
     *
     * App\Models\User
     */
    protected $user;

    /**
     * Create new instance of user repository.
     *
     * @param User user User model
     */
    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->user = $user;
    }

    /**
     * Store the data in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     */
    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $user = $this->user->create($request->all());

            $user->costCenterable()->create($user->toArray());

            return $user;
        });
    }

    /**
     * Find the resource using the specified id or else fail.
     *
     * @param  integer  $id                     Primary id of the data
     * @param  array    $autoLoadRelationships  Relationship to autoload
     * @return array object
     */
    public function findOrFail($id, $autoLoadRelationships = [])
    {
        $user = $this->user->where('id', $id)->with([
            'business',
            'createdBy',
            'roles' => function ($query) {
                $query->with('permissions');
            },
            'userableFrom',
        ])->withTrashed()->first();

        $roleNames = [];
        $permissionNames = [];

        foreach ($user->roles as $role) {
            if (! in_array($role->name, $roleNames)) {
                $roleNames[] = $role->name;
            }

            foreach ($role->permissions as $permission) {
                if (! in_array($permission->name, $permissionNames)) {
                    $permissionNames[] = $permission->name;
                }
            }
        }

        $user->role_names = $roleNames;
        $user->permission_names = $permissionNames;

        return $user;
    }
}
