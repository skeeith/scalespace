<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository
{
    /**
     * Main repository model
     *
     * @var $model
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param object $model Main repository model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Search resources with filters in the storage.
     *
     * @param  Illuminate\Http\Request  $request                Request
     * @param  array                    $autoLoadRelationships  Auto load model relationships
     * @return array object
     */
    public function search($request, $autoLoadRelationships = [])
    {
        if (count($request->all()) == 0) {
            return;
        }

        $limit = (isset($request->limit)) ? $request->limit : 15;

        if ($limit > 100) {
            $limit = 100;
        }

        return $this->model->with($autoLoadRelationships)->filter($request)->limit($limit)->get();
    }

    /**
     * Create pagination for the resources.
     *
     * @param  integer $per_page
     * @return array object
     */
    public function paginate($per_page = 10)
    {
        return $this->model->paginate($per_page);
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

        if (
            ($request->filled('with_trashed') && $request->boolean('with_trashed'))
            && (! $request->filled('only_trashed') || ! $request->boolean('only_trashed'))
        ) {
            $query = $query->withTrashed();
        }

        if (
            ($request->filled('only_trashed') && $request->boolean('only_trashed'))
            && (! $request->filled('with_trashed') || ! $request->boolean('with_trashed'))
        ) {
            $query = $query->onlyTrashed();
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
     * Get all resources with filters in the storage.
     *
     * @param  Illuminate\Http\Request  $request                Request
     * @param  array                    $autoLoadRelationships  Auto load model relationships
     * @return array object
     */
    public function allWithFilters($request, $autoLoadRelationships = [])
    {
        return $this->model->with($autoLoadRelationships)->filter($request)->get();
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
        return $this->model->with($autoLoadRelationships)->withTrashed()->findOrFail($id);
    }

    /**
     * Store the data in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return object
     */
    public function store($request)
    {
        return $this->model->create($request->all());
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
        $model = $this->model->findOrFail($id);
        $model->fill($request->all());

        $model->save();

        return $model;
    }

    /**
     * Remove the specified resource from the storage.
     *
     * @param  int $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Restore the specified resource from the storage.
     *
     * @param  int $id
     * @return boolean
     */
    public function restore($id)
    {
        return $this->model->withTrashed()->where('id', $id)->restore();
    }

    /**
     * Permanently remove the specified resource from the storage.
     *
     * @param  int $id
     * @return boolean
     */
    public function forceDestroy($id)
    {
        return $this->model->forceDestroy($id);
    }

    /**
     * Check if the user is authorize for certain ability.
     *
     * @param  string $ability
     * @return boolean
     */
    public function authorize($ability)
    {
        return auth()->user()->can($ability, $this->model);
    }

    /**
     * Retrieve archived resources for the model.
     *
     * @return array object
     */
    public function archives()
    {
        return $this->model->archives();
    }
}
