<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionCollection;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Permission repository instance.
     *
     * @var App\Repositories\PermissionRepository
     */
    protected $permissionRepository;

    /**
     * Create new instance of permission controller.
     *
     * @param PermissionRepository $permissionRepository Permission repository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = new PermissionCollection($this->permissionRepository->paginateWithFilters(request()));

        if (! $permissions) {
            return response()->json([
                'message' => 'Failed to retrieve permissions',
            ], 404);
        }

        return $permissions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // fields
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $permission = $this->permissionRepository->store($request)) {
            return response()->json([
                'message' => 'Failed to saved permission',
            ], 500);
        }

        return response()->json([
            'message' => 'Permission successfully saved',
            'permission' => $permission,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! $permission = $this->permissionRepository->findOrFail($id)) {
            return response()->json([
                'message' => 'Permission does not exist',
            ], 404);
        }

        return response()->json([
            'message' => 'Permission successfully retrieve',
            'permission' => $permission,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // fields
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $this->permissionRepository->update($request, $id)) {
            return response()->json([
                'message' => 'Failed to update permission',
            ], 500);
        }

        return response()->json([
            'message' => 'Permission successfully updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->permissionRepository->findOrFail($id)->delete()) {
            return response()->json([
                'message' => 'Failed to delete permission',
            ], 404);
        }

        return response()->json([
            'message' => 'Permission successfully deleted',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! $this->permissionRepository->restore($id)) {
            return response()->json([
                'message' => 'Failed to restore permission',
            ], 404);
        }

        return response()->json([
            'message' => 'Permission successfully restored',
        ], 200);
    }

    /**
     * Forcefully remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDestroy($id)
    {
        if (! $this->permissionRepository->forceDestroy($id)) {
            return response()->json([
                'message' => 'Failed to permanently delete permission',
            ], 404);
        }

        return response()->json([
            'message' => 'Permission successfully deleted permanently',
        ], 200);
    }

    /**
     * Search the specified data from the storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $permissions = new PermissionCollection($this->permissionRepository->paginateWithFilters(request()));

        if (! $permissions) {
            return response()->json([
                'message' => 'Failed to retrieve permissions',
            ], 404);
        }

        return $permissions;
    }

    /**
     * Retrieve all resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        if (! $permissions = $this->permissionRepository->allWithFilters(request())) {
            return response()->json([
                'message' => 'Resources does not exist',
            ], 404);
        }

        return response()->json([
            'message' => 'Resources successfully retrieve',
            'permissions' => $permissions,
        ], 200);
    }
}
