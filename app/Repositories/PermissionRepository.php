<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends Repository
{
    /**
     * Permission
     *
     * App\Permission
     */
    protected $permission;

    /**
     * Create new instance of permission repository.
     *
     * @param Permission $permission Permission model
     */
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);

        $this->permission = $permission;
    }

    /**
     * Create pagination with filters for the resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $autoLoadRelationships
     * @param  boolean                  $removePage
     * @return array object
     */
    public function paginateWithFilters(
        $request = null,
        $autoLoadRelationships = [],
        $removePage = true
    ) {
        $per_page = ($request->per_page === null || $request->per_page > 1000) ? 15 : $request->per_page;

        $orderBy = (
            $request->order_by === null
            || $request->order_by == ''
            || $request->order_by != 'desc'
        ) ? 'asc' : $request->order_by;

        $query = $this->model->with($autoLoadRelationships)->filter($request)->orderBy('id', $orderBy);

        if ($request->with_trashed == 1 && ($request->only_trashed == 0 || ! isset($request->only_trashed))) {
            $query = $query->withTrashed();
        }

        if ($request->only_trashed == 1 && ($request->with_trashed == 0 || ! isset($request->with_trashed))) {
            $query = $query->onlyTrashed();
        }

        return $query->paginate($per_page)->withPath($this->permission->createPaginationUrl($request, $removePage));
    }

    /**
     * Get all resources with filters in the storage.
     *
     * @param  Illuminate\Http\Request  $request                Request
     * @param  array                    $autoLoadRelationships  Auto load model relationships
     * @return array object
     */
    public function allWithFilters($request, $autoLoadRelationships = [])
    {
        $permissions = $this->permission->filter($request)->get();

        return $permissions->groupBy('module');
    }
}
