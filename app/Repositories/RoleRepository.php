<?php

namespace App\Repositories;

use App\Models\Role;
use Facades\App\Repositories\UserRepository;

class RoleRepository extends Repository
{
    /**
     * Role
     *
     * App\Role
     */
    protected $role;

    /**
     * Create new instance of role repository.
     *
     * @param Role $role Role model
     */
    public function __construct(Role $role)
    {
        parent::__construct($role);

        $this->role = $role;
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
        $per_page = ($request->per_page === null || $request->per_page > 100) ? 15 : $request->per_page;

        $orderBy = (
            $request->order_by === null
            || $request->order_by == ''
            || $request->order_by != 'asc'
        ) ? 'desc' : $request->order_by;

        $orderByFieldName = ($request->filled('order_by_field_name')) ? $request->order_by_field_name : 'id';

        if ($this->model->checkRequestValueForRelationship($orderByFieldName)) {
            $query = $this->model->with($autoLoadRelationships)->filter($request)->orderBy('id', $orderBy);
        } else {
            $query = $this->model->with($autoLoadRelationships)->filter($request)->orderBy($orderByFieldName, $orderBy);
        }

        if ($request->with_trashed == 1 && ($request->only_trashed == 0 || ! isset($request->only_trashed))) {
            $query = $query->withTrashed();
        }

        if ($request->only_trashed == 1 && ($request->with_trashed == 0 || ! isset($request->with_trashed))) {
            $query = $query->onlyTrashed();
        }

        if (! UserRepository::hasRole(explode('|', 'super-administrator'), true)) {
            $query->where('name', '!=', 'super-administrator');
            $query->where('id', '!=', 1);
        }

        if (! $this->model->checkRequestValueForRelationship($orderByFieldName)) {
            return $query->paginate($per_page)->withPath($this->model->createPaginationUrl($request, $removePage));
        }

        $results = $query->get();

        if ($this->model->checkRequestValueForRelationship($orderByFieldName)) {
            $relationship = $this->model->convertToRelationship($orderByFieldName);
            $relationshipMethod = call_user_func([$this->model, $relationship]);
            $orderByFieldName = $relationshipMethod->getRelated()->getTable() . '.' . $this->model->convertToColumn($orderByFieldName);

            if ($orderBy == 'desc') {
                $results = $results->sortByDesc($orderByFieldName);
            } else {
                $results = $results->sortBy($orderByFieldName);
            }
        } else {
            if ($orderBy == 'desc') {
                $results = $results->sortByDesc($orderByFieldName);
            } else {
                $results = $results->sortBy($orderByFieldName);
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage('page', 1);

        $paginatedCollection = new LengthAwarePaginator(
            $results->forPage($currentPage, $per_page),
            $results->count(),
            $per_page,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return $paginatedCollection;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return boolean
     */
    public function update($request, $id)
    {
        if ($id === 1) {
            return;
        }

        $model = $this->model->findOrFail($id);
        $model->fill($request->all());

        return $model->save();
    }

    /**
     * Toggle to add or remove persmission on certain role.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     */
    public function togglePermission($request)
    {
        if ($this->role->findOrFail($request->role_id)->permissions()->toggle($request->permission_id)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve assigned permissions for specific role using id.
     *
     * @param  int $id Role id
     * @return array
     */
    public function getAssignedPermissions($id)
    {
        return $this->role->where('id', $id)->with([
            'permissions' => function ($query) {
                $query->select('id');
            },
        ])->first()->permissions;
    }
}
