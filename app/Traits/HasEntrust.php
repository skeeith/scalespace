<?php

/*
 * This file is part of laravel filtering package.
 *
 * Author (a) Gether Kestrel B. Medel <gether.medel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2019 Gether Kestrel B. Medel
 */

namespace App\Traits;

use App\Models\RoleUser;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait HasEntrust
{
    /**
     * Specify the guard, leave blank for default guard.
     *
     * @var string
     */
    protected $guards = [
        'web',
        'api',
    ];

    /**
     * User.
     *
     * @var object
     */
    protected $user;

    /**
     * Get authenticated user.
     *
     * @return object
     */
    public function getAuthenticatedUser()
    {
        foreach ($this->guards as $guard) {
            if (auth($guard)->check()) {
                return auth($guard)->user();
            }
        }

        return false;
    }

    /**
     * Check authenticated user is set.
     *
     * @param  obect  $user  Authenticated user
     *
     * @return object
     */
    public function checkAuthenticatedUser($user)
    {
        if (! isset($user) && ($user === null || $user === false || ! is_int($user))) {
            $user = $this->getAuthenticatedUser();
        }

        return $user;
    }

    /**
     * Attach roles to a user.
     *
     * @param  integer|array  $roles  Role ids
     * @param  integer|object $user   User id or object
     * @return boolean
     */
    public function attachRoles($roles, $user = null)
    {
        $user = $this->checkAuthenticatedUser($user);

        if (! $user->roles()->attach($roles)) {
            return false;
        }

        return true;
    }

    /**
     * detach roles from a user.
     *
     * @param  integer|array  $roles  Role ids
     * @param  integer|object $user   User id or object
     *
     * @return boolean
     */
    public function detatachRoles($roles, $user = null)
    {
        $user = $this->checkAuthenticatedUser($user);

        if (! $user->roles()->detach($roles)) {
            return false;
        }

        return true;
    }

    /**
     * Toggle to add or remove role on certain admin.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     */
    public function toggleRole($request)
    {
        if ($this->user->findOrFail($request->user_id)->roles()->toggle($request->role_id)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve assigned roles for specific user using id.
     *
     * @param  int  $id Permission  id
     *
     * @return array
     */
    public function getAssignedRoles($id)
    {
        return $this->user->where('id', $id)->with([
            'roles' => function ($query) {
                $query->select('roles.id', 'roles.name');
            },
        ])->first()->roles;
    }

    /**
     * Check if the user has the required role.
     *
     * @param  string|array  $roles        Role name
     * @param  boolean       $requiredAll  Require all roles to be true
     *
     * @return boolean
     */
    public function hasRole($roles, $requiredAll = false)
    {
        if (count($this->getAuthenticatedUser()->roles) === 0) {
            return false;
        }

        if ($user = $this->getAuthenticatedUser()) {
            $userRoles = RoleUser::with('role')->where('user_id', $user->id)->get();
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                $hasRole = $this->hasRole($role);

                if ($hasRole && ! $requiredAll) {
                    return true;
                } elseif (! $hasRole && $requiredAll) {
                    return false;
                }
            }

            return $requiredAll;
        } else {
            foreach ($userRoles as $userRole) {
                if ($userRole->role->name === $roles) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the user has the required permission.
     *
     * @param  string|array  $permissions  Permission name
     * @param  boolean       $requiredAll  Require all permissions to be true
     *
     * @return boolean
     */
    public function hasPermission($permissions, $requiredAll = false)
    {
        if ($user = $this->getAuthenticatedUser()) {
            $userRoles = RoleUser::with('permissions')->where('user_id', $user->id)->get();
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $hasPermission = $this->hasPermission($permission);

                if ($hasPermission && ! $requiredAll) {
                    return true;
                } elseif (! $hasPermission && $requiredAll) {
                    return false;
                }
            }

            return $requiredAll;
        } else {
            foreach ($userRoles as $userRole) {
                foreach ($userRole->permissions as $permission) {
                    if ($permission->name === $permissions) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if the user has the required role and permission.
     *
     * @param  string|array  $roles        Role Name
     * @param  string|array  $permissions  Permission Name
     * @param  boolean       $requiredAll  Require all roles and permissions
     *
     * @return boolean
     */
    public function hasAbility($roles, $permissions, $requiredAll = false)
    {
        $checkedRoles       = [];
        $checkedPermissions = [];

        if (is_array($roles)) {
            foreach ($roles as $role) {
                $checkedRoles[$role] = $this->hasRole($role);
            }
        } else {
            $checkedRoles[$roles] = $this->hasRole($roles);
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $checkedPermissions[$permission] = $this->hasPermission($permission);
            }
        } else {
            $checkedPermissions[$permissions] = $this->hasPermission($permissions);
        }

        if (
            ($requiredAll && ! (in_array(false, $checkedRoles) || in_array(false, $checkedPermissions)))
            || (! $requiredAll && (in_array(true, $checkedRoles) || in_array(true, $checkedPermissions)))
        ) {
            return true;
        }

        return false;
    }

    // public function cachedRoles()
    // {
    //     $userPrimaryKey = $this->primaryKey;
    //     $cacheKey = 'entrust_roles_for_user_' . $this->$userPrimaryKey;

    //     if (Cache::getStore() instanceof TaggableStore) {
    //         return Cache::tags(Config::get('entrust.role_user_table'))->remember($cacheKey, Config::get('cache.ttl'), function () {
    //             return $this->roles()->get();
    //         });
    //     } else {
    //         return $this->roles()->get();
    //     }
    // }

    // public function cachedPermissions()
    // {
    //     $rolePrimaryKey = $this->primaryKey;
    //     $cacheKey = 'entrust_permissions_for_role_' . $this->$rolePrimaryKey;

    //     if (Cache::getStore() instanceof TaggableStore) {
    //         return Cache::tags(Config::get('entrust.permission_role_table'))->remember($cacheKey, Config::get('cache.ttl', 60), function () {
    //             return $this->perms()->get();
    //         });
    //     } else {
    //         return $this->perms()->get();
    //     }
    // }
}
